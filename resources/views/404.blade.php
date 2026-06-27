<!doctype html>
<html lang="en"> 
 <head> 
  <meta charset="UTF-8"> 
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"> 
  <meta http-equiv="X-UA-Compatible" content="ie=edge"> 
  <title>
      404 Error Page | ReadPal
    </title> 
  <link rel="icon" href="favicon.ico">
  <link rel="stylesheet" href="{{ asset('style.css') }}">
 
 </head> 
 <body>
  
  <div class="relative z-1 flex min-h-screen flex-col items-center justify-center overflow-hidden p-6"> <!-- ===== Common Grid Shape Start ===== --> 
   <div class="absolute right-0 top-0 -z-1 w-full max-w-[250px] xl:max-w-[450px]"> 
    <img src="{{ asset('grid-01.svg') }}" alt="grid">
    
   </div> 
   <div class="absolute bottom-0 left-0 -z-1 w-full max-w-[250px] rotate-180 xl:max-w-[450px]"> 
    <img src="{{ asset('grid-01.svg') }}" alt="grid"> 
   </div> <!-- ===== Common Grid Shape End ===== --> <!-- Centered Content --> 
   <div class="mx-auto w-full max-w-[242px] text-center sm:max-w-[472px]"> 
    <h1 class="mb-8 text-title-md font-bold text-gray-800 dark:text-white/90 xl:text-title-2xl"> ERROR </h1> 
    <img src="{{ asset('404.svg') }}" alt="404" class="dark:hidden"> 
    <img src="{{ asset('404-dark.svg') }}" alt="404" class="hidden dark:block"> 
    <p class="mb-6 mt-10 text-base text-gray-700 dark:text-gray-400 sm:text-lg"> We can’t seem to find the page you are looking for! </p> <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"> Back to Home Page </a> 
   </div> <!-- Footer --> 
   <p class="absolute bottom-6 left-1/2 -translate-x-1/2 text-center text-sm text-gray-500 dark:text-gray-400"> © {{ now()->year }}</span> - {{ config('app.name') }} </p> 
  </div> <!-- ===== Page Wrapper End ===== --> 
  <script defer src="bundle.js"></script>
  <script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon="{&quot;version&quot;:&quot;2024.11.0&quot;,&quot;token&quot;:&quot;67f7a278e3374824ae6dd92295d38f77&quot;,&quot;r&quot;:1,&quot;server_timing&quot;:{&quot;name&quot;:{&quot;cfCacheStatus&quot;:true,&quot;cfEdge&quot;:true,&quot;cfExtPri&quot;:true,&quot;cfL4&quot;:true,&quot;cfOrigin&quot;:true,&quot;cfSpeedBrain&quot;:true},&quot;location_startswith&quot;:null}}" crossorigin="anonymous"></script>  
 </body>
</html>