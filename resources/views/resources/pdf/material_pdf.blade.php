<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            margin: 50px 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            line-height: 1.2;
            position: relative;
            margin: 0;
            padding: 0;
        }

        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.15; /* transparent */
            z-index: -1;
        }

        .content {
            position: relative;
            z-index: 10;
            margin: 30px;
        }
        
        h2, h4 {
            margin-bottom: 10px;
            text-align: center;
        }

        p, div {
            page-break-inside: auto !important;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- Watermark Image -->
    <img src="{{ public_path('ReadPalBlur.png') }}" class="watermark" />

    <div class="content">
        <h2>{{ $title }}</h2>
        <h4>Course Code: {{ $course_code }}</h4>
        <h4>Lecturer: {{ $lecturer }}</h4>

        {!! $content !!}
    </div>

</body>
</html>