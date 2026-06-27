@extends('layouts.admin')
@section('title', isset($material) ? 'Edit Material' : 'Upload Material')
@section('page_title', isset($material) ? 'Edit Material' : 'Upload Material')
@section('page_sub', 'Materials Management')

@section('content')
<style>
    /* 1. Force the typing text (content) to solid black */
    .ck-content, .ck.ck-editor__editable {
        color: #000000 !important; /* Pure black text */
        background-color: #ffffff !important; /* Ensuring background is pure white for contrast */
    }

    /* 2. Override CKEditor's internal CSS variables for consistent black text */
    :root {
        --ck-color-text: #000000;
        --ck-content-font-color: #000000;
    }

    /* 3. Constrain the editor height and maintain card width */
    .ck-editor__editable_inline {
        min-height: 300px !important;
        max-height: 500px !important;
        width: 100% !important;
    }

    /* 4. Ensure the toolbar icons also use high contrast */
    .ck.ck-toolbar {
        background: #f8fafc !important; /* Light background for the toolbar */
        border-color: #cbd5e1 !important;
    }

    input[type="file"]::file-selector-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Limit CKEditor editable area height */
    .ck-editor__editable_inline {
        min-height: 200px;  /* About 10 lines */
        max-height: 200px;
        overflow-y: auto;   /* Scroll within the editor */
    }
</style>
<div class="max-w-2xl mx-auto">

  <nav class="flex items-center gap-2 text-xs text-ink-700 mb-5 fu">
    <a href="{{ route('admin.materials.index') }}" class="hover:text-ink-400 transition-colors">Materials</a>
    <span>›</span>
    <span class="text-ink-400">{{ isset($material) ? 'Edit' : 'Upload New' }}</span>
  </nav>

  <div class="a-card fu1">
    <h2 class="font-display text-lg font-bold text-white mb-5">
      {{ isset($material) ? 'Edit: ' . Str::limit($material->title, 40) : 'Upload New Material' }}
    </h2>

    <form method="POST"
          action="{{ isset($material) ? route('admin.materials.update', $material->id) : route('admin.materials.store') }}"
          enctype="multipart/form-data">
      @csrf
      @if(isset($material)) @method('PUT') @endif

      <div class="mb-4">
        <label class="form-label">Title</label>
        <input type="text" name="title" required
               value="{{ old('title', $material->title ?? '') }}"
               placeholder="e.g. SOC 303 – Week 4: Social Control"
               class="form-input"/>
        @error('title')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      <div class="mb-4">
        <label class="form-label">Resource / Course</label>
        <select name="resource_id" required class="form-input cursor-pointer">
          <option value="" disabled {{ !isset($material) ? 'selected' : '' }}>Select course…</option>
          @foreach($resources ?? [] as $res)
          <option value="{{ $res->id }}"
                  {{ old('resource_id', $material->resource_id ?? '') == $res->id ? 'selected' : '' }}>
            {{ $res->course_code }} – {{ $res->course_title }}
          </option>
          @endforeach
        </select>
        @error('resource_id')<p class="form-error">{{ $message }}</p>@enderror
      </div>

      <div class="mb-4">
                <label class="form-label">Material Type</label>
                <select name="type" id="type" class="form-input cursor-pointer" value="{{ ucfirst($material->type ?? 'Note') }}" disabled>
                    <option value="note" {{ (old('type', $material->type ?? '') == 'note') ? 'selected' : '' }}>Note</option>
                    <option value="pdf" {{ (old('type', $material->type ?? '') == 'pdf') ? 'selected' : '' }}>PDF</option>
                </select>
                {{-- Hidden input is REQUIRED because disabled selects don't submit data --}}
                <input type="hidden" name="type" value="{{ $material->type ?? 'note' }}">
            </div>

      {{-- Note Text (CKEditor) --}}
            <div id="noteField" class="mb-4 hidden">
                <label class="form-label">Lesson Notes (Text)</label>
                <textarea name="note_text" id="note_text" rows="8" class="w-full mt-1 p-2 border rounded-md dark:bg-gray-800 dark:border-gray-700">{{ old('note_text', $material->note_text ?? '') }}</textarea>
                @error('note_text')<p class="form-error">{{ $message }}</p>@enderror
            </div>


        {{-- PDF Upload --}}

             <div id="pdfField" class="mb-5">
                <label class="form-label">PDF File {{ isset($material) ? '(leave blank to keep existing)' : '' }}</label>
                @if(isset($material) && $material->pdf_path)
                <div class="flex items-center gap-2 mb-2 px-3 py-2 rounded-lg bg-ink-800 border border-ink-700">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2">
                    <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-xs text-ink-400">Current: {{ basename($material->pdf_path) }}</span>
                </div>
                @endif
                <input name="file" type="file" id="fileInput"
                    class="form-input cursor-pointer file:mr-3 file:py-1.5 file:px-3
                            file:rounded-lg file:border-0 file:text-xs file:font-semibold
                            file:bg-forest-950 file:text-forest-400 file:cursor-pointer
                            hover:file:bg-forest-900"/>
                @error('pdf_file')<p class="form-error">{{ $message }}</p>@enderror
            </div>

      <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ isset($material) ? 'Save Changes' : 'Upload Material' }}
        </button>
        <a href="{{ route('admin.materials.index') }}" class="btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>
{{-- CKEditor 5 CDN --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const typeSelect = document.getElementById('type');
    const pdfField = document.getElementById('pdfField');
    const noteField = document.getElementById('noteField');

    function toggleFields() {
        if (typeSelect.value === 'note') {
            pdfField.classList.add('hidden');
            noteField.classList.remove('hidden');
        } else {
            noteField.classList.add('hidden');
            pdfField.classList.remove('hidden');
        }
    }

    toggleFields(); // Run on page load
    typeSelect.addEventListener('change', toggleFields);

    // Initialize CKEditor safely
    const noteText = document.querySelector('#note_text');
    if (noteText) {
        ClassicEditor.create(noteText)
            .catch(error => console.error('CKEditor init failed:', error));
    }
});
</script>
@endsection