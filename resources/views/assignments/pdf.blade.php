<!DOCTYPE html>
{{--
    resources/views/assignments/pdf.blade.php
    Clean academic PDF layout — questions hidden, only answers shown
    Lightweight CSS only (DomPDF compatible)
--}}
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $assignment->title }}</title>
<style>
/* ── Reset ──────────────────────────────────────────────────── */
* { margin: 0; padding: 0; box-sizing: border-box; }
html { font-size: 11pt; }

/* ── Page layout ────────────────────────────────────────────── */
body {
    font-family: 'Times New Roman', Times, serif;
    color: #1a1a1a;
    background: #ffffff;
    line-height: 1.75;
    padding: 0;
}

/* ── Cover Header ───────────────────────────────────────────── */
.cover-header {
    text-align: center;
    padding: 40px 40px 28px;
    border-bottom: 2px solid #1a1a1a;
    margin-bottom: 32px;
}

.institution-name {
    font-size: 9pt;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #555;
    margin-bottom: 20px;
    font-family: Arial, sans-serif;
}

.assignment-title {
    font-size: 18pt;
    font-weight: bold;
    line-height: 1.3;
    margin-bottom: 10px;
    color: #111;
}

.assignment-topic {
    font-size: 11pt;
    color: #444;
    font-style: italic;
    margin-bottom: 24px;
}

/* Meta table */
.meta-table {
    width: 100%;
    max-width: 380px;
    margin: 0 auto;
    border-collapse: collapse;
}

.meta-table td {
    padding: 4px 10px;
    font-size: 9.5pt;
    font-family: Arial, sans-serif;
    vertical-align: top;
}

.meta-table td:first-child {
    color: #666;
    font-weight: bold;
    text-align: right;
    width: 35%;
    border-right: 1px solid #ddd;
}

.meta-table td:last-child {
    color: #222;
    text-align: left;
}

/* ── Body ───────────────────────────────────────────────────── */
.body-wrap {
    padding: 0 44px 40px;
}

/* ── Section ────────────────────────────────────────────────── */
.assignment-section {
    margin-bottom: 30px;
    page-break-inside: avoid;
}

.section-heading {
    font-size: 12pt;
    font-weight: bold;
    color: #111;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding-bottom: 6px;
    border-bottom: 1px solid #ccc;
    margin-bottom: 14px;
    font-family: Arial, Helvetica, sans-serif;
}

.section-number {
    color: #888;
    font-size: 9pt;
    margin-right: 6px;
}

.section-content {
    font-size: 11pt;
    line-height: 1.85;
    color: #1a1a1a;
    text-align: justify;
    text-indent: 20px;
}

/* Empty section notice */
.section-empty {
    font-size: 10pt;
    color: #aaa;
    font-style: italic;
    text-indent: 0;
    padding: 8px 0;
    border-left: 3px solid #eee;
    padding-left: 12px;
}

/* ── Footer ─────────────────────────────────────────────────── */
.pdf-footer {
    text-align: center;
    padding-top: 24px;
    margin-top: 40px;
    border-top: 1px solid #ddd;
    font-size: 8.5pt;
    color: #999;
    font-family: Arial, sans-serif;
    letter-spacing: 0.04em;
}

/* ── Page numbers (DomPDF) ──────────────────────────────────── */
.page-number:before {
    content: counter(page);
}

@page {
    margin: 20mm 20mm 24mm 20mm;
}
</style>
</head>

<body>

{{-- ── COVER HEADER ──────────────────────────────────────────── --}}
<div class="cover-header">
    <div class="institution-name">Adekunle Ajasin University, Akungba · ReadPal</div>

    <div class="assignment-title">{{ $assignment->title }}</div>
    <div class="assignment-topic">{{ $assignment->topic }}</div>

    <table class="meta-table">
        <tr>
            <td>Student</td>
            <td>{{ $user->name }}</td>
        </tr>
        @if($assignment->course)
        <tr>
            <td>Course</td>
            <td>{{ $assignment->course }}</td>
        </tr>
        @endif
        <tr>
            <td>Status</td>
            <td>{{ ucfirst($userAssignment->status) }}</td>
        </tr>
        <tr>
            <td>Generated</td>
            <td>{{ now()->format('F j, Y') }}</td>
        </tr>
    </table>
</div>

{{-- ── SECTIONS ──────────────────────────────────────────────── --}}
<div class="body-wrap">

    @foreach($assignment->sections as $i => $section)
    @php
        $contentRow = $contents[$section->id] ?? null;
        $bodyText   = $contentRow ? trim($contentRow->content) : null;
    @endphp

    <div class="assignment-section">

        <div class="section-heading">
            <span class="section-number">{{ $i + 1 }}.</span>
            {{ $section->title }}
        </div>

        @if($bodyText)
        <div class="section-content">
            {{-- Convert newlines to paragraphs for PDF --}}
            @foreach(explode("\n\n", $bodyText) as $para)
            @if(trim($para))
            <p style="margin-bottom:10px;">{{ trim($para) }}</p>
            @endif
            @endforeach
        </div>
        @else
        <div class="section-empty">[This section has not been completed yet.]</div>
        @endif

    </div>
    @endforeach

</div>

{{-- ── FOOTER ────────────────────────────────────────────────── --}}
<div class="pdf-footer">
    Generated by ReadPal AI · {{ now()->format('Y') }} · {{ $user->name }}
</div>

</body>
</html>
