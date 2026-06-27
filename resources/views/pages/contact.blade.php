@extends('layouts.guest')

@section('title', 'Contact Us')
@section('meta_description', 'Get in touch with the ReadPal team at Oracle Tech.')

@section('content')
<section class="px-6 md:px-10 pt-8 pb-20">
    <div class="max-w-5xl mx-auto">

        {{-- Header --}}
        <div class="text-center mb-14 fade-up">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-medium mb-6"
                 style="background: rgba(212,136,42,0.1); border: 1px solid rgba(212,136,42,0.2); color: #15803D;">
                ✉ Get in Touch
            </div>
            <h1 class="font-display text-5xl md:text-6xl font-semibold text-parch-100 leading-tight">We're listening.</h1>
            <p class="text-parch-100/50 text-base mt-4 max-w-xl mx-auto">
                Have a question, a bug report, or just want to say hi? The Oracle Tech team is here to help.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

            {{-- Contact form --}}
            <div class="lg:col-span-3 fade-up delay-1">
                <div class="auth-card rounded-3xl p-8">

                    @if(session('success'))
                        <div class="rp-alert-success rounded-xl px-4 py-4 mb-6 text-sm flex items-start gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div>
                                <p class="font-medium">Message sent successfully!</p>
                                <p class="text-green-300/70 mt-0.5">We'll get back to you within 24–48 hours.</p>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="rp-alert-error rounded-xl px-4 py-3 mb-5 text-sm">
                            @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/contact') }}" class="space-y-5">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">Full Name</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                       placeholder="Your name"
                                       class="rp-input w-full px-4 py-3 rounded-xl text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       placeholder="you@readpal.com"
                                       class="rp-input w-full px-4 py-3 rounded-xl text-sm" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">Subject</label>
                            <select name="subject" class="rp-input w-full px-4 py-3 rounded-xl text-sm">
                                <option value="" disabled selected>Select a topic</option>
                                <option value="general"   {{ old('subject')=='general'   ?'selected':'' }}>General Enquiry</option>
                                <option value="technical" {{ old('subject')=='technical' ?'selected':'' }}>Technical Issue / Bug</option>
                                <option value="materials" {{ old('subject')=='materials' ?'selected':'' }}>Course Materials</option>
                                <option value="account"   {{ old('subject')=='account'   ?'selected':'' }}>Account & Access</option>
                                <option value="feedback"  {{ old('subject')=='feedback'  ?'selected':'' }}>Feedback / Suggestion</option>
                                <option value="other"     {{ old('subject')=='other'     ?'selected':'' }}>Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-parch-100/60 uppercase tracking-wider mb-2">Message</label>
                            <textarea name="message" rows="5"
                                      placeholder="Describe your issue or question in detail..."
                                      class="rp-input w-full px-4 py-3 rounded-xl text-sm resize-none" required>{{ old('message') }}</textarea>
                            <p class="text-parch-100/25 text-xs mt-1">The more detail you provide, the faster we can help.</p>
                        </div>

                        <button type="submit" class="rp-btn-primary w-full py-3.5 rounded-xl text-sm font-body">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>

            {{-- Contact info sidebar --}}
            <div class="lg:col-span-2 space-y-5 fade-up delay-2">

                {{-- Info card --}}
                <div class="rp-card rounded-2xl p-6">
                    <h3 class="font-display text-xl font-semibold text-parch-100 mb-4">Contact Details</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                                 style="background: rgba(212,136,42,0.1);">
                                <svg class="w-4 h-4 text-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-parch-100/50 text-xs uppercase tracking-wider mb-0.5">Email</p>
                                <a href="mailto:support@readpal.app" class="text-parch-100 text-sm hover:text-amber transition-colors">support@readpal.online</a>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                                 style="background: rgba(212,136,42,0.1);">
                                <svg class="w-4 h-4 text-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-parch-100/50 text-xs uppercase tracking-wider mb-0.5">WhatsApp Support</p>
                                <a href="https://wa.me/2348000000000" class="text-parch-100 text-sm hover:text-amber transition-colors">+234 813 961 8496</a>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5"
                                 style="background: rgba(212,136,42,0.1);">
                                <svg class="w-4 h-4 text-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-parch-100/50 text-xs uppercase tracking-wider mb-0.5">Response Time</p>
                                <p class="text-parch-100 text-sm">24–48 hours on weekdays</p>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- FAQ hint --}}
                <div class="rp-card rounded-2xl p-6" style="border-color: rgba(75,110,82,0.25);">
                    <h3 class="font-display text-xl font-semibold text-parch-100 mb-2">Common Questions</h3>
                    <ul class="space-y-3 mt-4">
                        @foreach([
                            'I forgot my matric number — what do I enter?',
                            'How do I download a lecture PDF?',
                            'Why aren\'t my quiz scores saving?',
                            'Can I use ReadPal offline?',
                        ] as $faq)
                        <li class="text-sm text-parch-100/45 hover:text-amber/70 cursor-pointer transition-colors flex items-start gap-2">
                            <span class="text-amber/40 flex-shrink-0 mt-0.5">›</span>
                            {{ $faq }}
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Response promise --}}
                <div class="rounded-2xl p-5 text-center"
                     style="background: rgba(212,136,42,0.06); border: 1px dashed rgba(212,136,42,0.2);">
                    <p class="text-amber text-2xl mb-2">⚡</p>
                    <p class="text-parch-100/60 text-sm">For urgent issues during exam periods, reach us on WhatsApp for a faster response.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
