@extends('layouts.guest')

@section('title', 'Feedback')
@section('meta_description', 'Help improve ReadPal — share your experience and suggestions with Oracle Tech.')

@section('content')
<section class="px-6 md:px-10 pt-8 pb-20">
    <div class="max-w-3xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-12 fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-medium mb-6"
                 style="background: rgba(163,115,215,0.1); border: 1px solid rgba(163,115,215,0.2); color: #c4a0e8;">
                💬 Your Voice Matters
            </div>
            <h1 class="font-display text-5xl md:text-6xl font-semibold text-parch-100 leading-tight">
                Shape ReadPal's<br><em>future.</em>
            </h1>
            <p class="text-parch-100/50 text-base mt-4 max-w-lg mx-auto">
                Every piece of feedback we receive directly influences what we build next.
                Tell us what's working, what isn't, and what you wish existed.
            </p>
        </div>

        {{-- Card --}}
        <div class="auth-card rounded-3xl p-8 md:p-10 fade-up delay-1">

            @if(session('success'))
                <div class="rp-alert-success rounded-xl px-4 py-5 mb-7 flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <div>
                        <p class="font-medium text-sm">Thank you for your feedback!</p>
                        <p class="text-green-300/65 text-sm mt-0.5">Your response has been recorded and shared with the Oracle Tech team.</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="rp-alert-error rounded-xl px-4 py-3 mb-5 text-sm">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ url('/feedback') }}" class="space-y-7" id="feedback-form">
                @csrf

                {{-- Overall rating --}}
                <div class="fade-up delay-2">
                    <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-3">
                        Overall Experience
                    </label>
                    <div class="flex gap-3" id="star-rating">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button" data-val="{{ $i }}"
                                class="star-btn text-4xl text-parch-100/15 hover:text-amber transition-colors leading-none"
                                onclick="setRating({{ $i }})">★</button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="{{ old('rating') }}" required>
                    <p id="rating-label" class="text-xs text-parch-100/30 mt-2">Select a rating above</p>
                </div>

                {{-- Category --}}
                <div class="fade-up delay-2">
                    <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">
                        What area is your feedback about?
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @php
                        $cats = ['Lecture Notes','Quiz System','Timetable Alerts','My Notes','App Performance','UI & Design','Other'];
                        @endphp
                        @foreach($cats as $cat)
                        <button type="button"
                                class="cat-btn px-3 py-2.5 rounded-xl text-sm text-parch-100/55 transition-all"
                                style="background: rgba(255,255,255,0.03); border: 1px solid rgba(212,136,42,0.12);"
                                onclick="selectCat(this, '{{ $cat }}')">
                            {{ $cat }}
                        </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="category" id="cat-input" value="{{ old('category') }}">
                </div>

                {{-- Name + email --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 fade-up delay-3">
                    <div>
                        <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">
                            Name <span class="text-parch-100/25 normal-case tracking-normal">(optional)</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="Your name"
                               class="rp-input w-full px-4 py-3 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">
                            Matric No. <span class="text-parch-100/25 normal-case tracking-normal">(optional)</span>
                        </label>
                        <input type="text" name="matric_no" value="{{ old('matric_no') }}"
                               placeholder="20/SC/0001"
                               class="rp-input w-full px-4 py-3 rounded-xl text-sm">
                    </div>
                </div>

                {{-- Feature request --}}
                <div class="fade-up delay-3">
                    <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">
                        What do you like most about ReadPal?
                    </label>
                    <textarea name="likes" rows="3" placeholder="Tell us what's working well..."
                              class="rp-input w-full px-4 py-3 rounded-xl text-sm resize-none">{{ old('likes') }}</textarea>
                </div>

                <div class="fade-up delay-3">
                    <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">
                        What could be improved?
                    </label>
                    <textarea name="improvements" rows="3" placeholder="Be as specific as you like..."
                              class="rp-input w-full px-4 py-3 rounded-xl text-sm resize-none">{{ old('improvements') }}</textarea>
                </div>

                <div class="fade-up delay-4">
                    <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">
                        Anything else you'd like to share?
                    </label>
                    <textarea name="other" rows="3" placeholder="Feature requests, bug reports, questions..."
                              class="rp-input w-full px-4 py-3 rounded-xl text-sm resize-none">{{ old('other') }}</textarea>
                </div>

                {{-- Recommend --}}
                <div class="fade-up delay-4">
                    <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-3">
                        Would you recommend ReadPal to a classmate?
                    </label>
                    <div class="flex gap-3">
                        @foreach(['Definitely yes', 'Probably yes', 'Not sure', 'Probably not'] as $opt)
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="recommend" value="{{ $opt }}" class="sr-only peer">
                            <div class="text-center py-2.5 px-2 rounded-xl text-xs text-parch-100/45 transition-all peer-checked:text-amber peer-checked:font-medium"
                                 style="background: rgba(255,255,255,0.03); border: 1px solid rgba(212,136,42,0.12);">
                                {{ $opt }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="fade-up delay-5 pt-1">
                    <button type="submit" class="rp-btn-primary w-full py-3.5 rounded-xl text-sm font-body">
                        Submit Feedback
                    </button>
                    <p class="text-center text-parch-100/25 text-xs mt-3">
                        Anonymous by default unless you include your name.
                    </p>
                </div>
            </form>
        </div>

        {{-- Stats strip --}}
        <div class="grid grid-cols-3 gap-4 mt-8 fade-up delay-5">
            @foreach([['94%','Students satisfied'],['200+','Feedback responses received'],['12','Features built from feedback']] as $s)
            <div class="rp-card rounded-2xl p-4 text-center">
                <p class="font-display text-2xl font-semibold text-amber">{{ $s[0] }}</p>
                <p class="text-parch-100/40 text-xs mt-1">{{ $s[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
const ratingLabels = ['', 'Poor — needs a lot of work', 'Fair — some improvements needed', 'Good — pretty happy', 'Great — really enjoying it', 'Excellent — love ReadPal! ⭐'];

function setRating(val) {
    document.getElementById('rating-input').value = val;
    document.getElementById('rating-label').textContent = ratingLabels[val];
    document.getElementById('rating-label').style.color = val >= 4 ? '#73A67C' : val >= 3 ? '#F0B050' : '#f87171';

    document.querySelectorAll('.star-btn').forEach((btn, i) => {
        btn.style.color = i < val ? '#15803D' : 'rgba(247,242,232,0.15)';
    });
}

function selectCat(el, val) {
    document.getElementById('cat-input').value = val;
    document.querySelectorAll('.cat-btn').forEach(b => {
        b.style.background = 'rgba(255,255,255,0.03)';
        b.style.borderColor = 'rgba(212,136,42,0.12)';
        b.style.color = 'rgba(247,242,232,0.55)';
    });
    el.style.background = 'rgba(212,136,42,0.12)';
    el.style.borderColor = 'rgba(212,136,42,0.4)';
    el.style.color = '#15803D';
}

// Radio peer-checked visual (pure CSS peer handles it via Tailwind, but let's reinforce)
document.querySelectorAll('input[name="recommend"]').forEach(r => {
    r.addEventListener('change', function() {
        document.querySelectorAll('input[name="recommend"]').forEach(o => {
            const div = o.nextElementSibling;
            div.style.background   = o.checked ? 'rgba(212,136,42,0.1)' : 'rgba(255,255,255,0.03)';
            div.style.borderColor  = o.checked ? 'rgba(212,136,42,0.4)' : 'rgba(212,136,42,0.12)';
            div.style.color        = o.checked ? '#15803D' : 'rgba(247,242,232,0.45)';
        });
    });
});
</script>
@endpush
