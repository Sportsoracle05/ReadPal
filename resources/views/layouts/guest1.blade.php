<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:site_name" content="ReadPal Online" />
       <meta property="og:title" content="ReadPal Online" />
<meta property="og:type" content="article" /> 
<meta property="og:url" content="https://readpal.online" />
<meta property="og:image" content="https://readpal.online/public/ReadPalMain.png" />
<meta property="og:description" content="ReadPal is a dedicated mobile learning companion designed to streamline access to academic materials" />

<meta property="og:site_name" content="ReadPal Online" />
<meta property="og:locale" content="en_US" />

<meta name="twitter:card" content="summary_large_image" /> 
<meta name="twitter:site" content="" />
<meta name="twitter:creator" content="" />
<meta name="twitter:title" content="ReadPal Online" />
<meta name="twitter:description" content="ReadPal is a dedicated mobile learning companion designed to streamline access to academic materials"/>
<meta name="twitter:image" content="https://readpal.online/public/ReadPalMain.png" />
    <title>@yield('title') - ReadPal</title>
    <link rel="stylesheet" href="css/app.css">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Development version of Lucide Icons for vanilla HTML -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
      body {
        font-family: 'Inter', sans-serif;
      }
    </style>
  <script type="importmap">
{
  "imports": {
    "@google/genai": "https://aistudiocdn.com/@google/genai@^1.31.0",
    "react": "https://aistudiocdn.com/react@^19.2.1",
    "lucide-react": "https://aistudiocdn.com/lucide-react@^0.555.0",
    "react/": "https://aistudiocdn.com/react@^19.2.1/"
  }
}
</script>
 </head>
<body class="bg-gray-50 min-h-screen flex flex-col">
<custom-navbar></custom-navbar>
<div style="margin-top: 60px">
  <x-alert/>
    @yield('content')
</div>


<custom-footer></custom-footer>

    {{-- Scripts --}}
    <x-js.navbar></x-js.navbar>
    <x-js.footer></x-js.footer>
    <script src="js/app.js"></script>
    <script>
        feather.replace();
        
        // Handle login form submission
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Simulate login (in a real app, this would be an API call)
            login(email, password);
        });
    </script>
</body>
</html>
