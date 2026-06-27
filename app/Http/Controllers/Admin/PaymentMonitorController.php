<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentMonitorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::with(['user', 'paymentPlan'])
                        ->latest();

        // ── Filters ──────────────────────────────────────────────────────────

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                    ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('payment_type', $type);
        }

        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $payments = $query->paginate(25)->withQueryString();

        // ── Summary Stats ────────────────────────────────────────────────────

        $stats = [
            'total_revenue'     => Payment::successful()->sum('amount'),
            'premium_revenue'   => Payment::successful()->premium()->sum('amount'),
            'custom_revenue'    => Payment::successful()->where('payment_type', 'custom')->sum('amount'),
            'total_count'       => Payment::successful()->count(),
            'premium_users'     => User::where('is_premium', true)->count(),
            'today_revenue'     => Payment::successful()->whereDate('paid_at', today())->sum('amount'),
            'this_month'        => Payment::successful()->whereMonth('paid_at', now()->month)->sum('amount'),
        ];

        // ── Per-plan breakdown ───────────────────────────────────────────────

        $planBreakdown = DB::table('payments')
            ->join('payment_plans', 'payments.payment_plan_id', '=', 'payment_plans.id')
            ->where('payments.status', 'success')
            ->groupBy('payment_plans.id', 'payment_plans.name')
            ->select('payment_plans.name', DB::raw('count(*) as count'), DB::raw('sum(payments.amount) as total'))
            ->orderByDesc('total')
            ->get();

        return view('admin.payments.index', compact('payments', 'stats', 'planBreakdown'));
    }

    public function show(Payment $payment): View
    {
        $payment->load('user', 'paymentPlan');

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Export payments as CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Payment::with(['user', 'paymentPlan'])->successful()->latest();

        if ($from = $request->input('from')) {
            $query->whereDate('paid_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('paid_at', '<=', $to);
        }

        $filename = 'payments_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'ID', 'Reference', 'User Name', 'User Email',
                'Type', 'Plan', 'Amount (₦)', 'Status',
                'Channel', 'Paid At',
            ]);

            $query->chunk(500, function ($payments) use ($handle) {
                foreach ($payments as $p) {
                    fputcsv($handle, [
                        $p->id,
                        $p->reference,
                        $p->user->name ?? '',
                        $p->user->email ?? '',
                        $p->payment_type,
                        $p->paymentPlan->name ?? 'Premium',
                        number_format($p->amount / 100, 2),
                        $p->status,
                        $p->gateway_channel ?? '',
                        $p->paid_at?->format('Y-m-d H:i:s') ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
