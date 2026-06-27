@extends($layout)

@section('title', 'Resources')

@section('content')

<style>
    
:root {
  --primary-blue: #2563eb;
  --secondary-blue: #1d4ed8;
}

body {
  background-color: white;
  font-family: 'Inter', sans-serif;
}

.btn-primary {
  background-color: var(--primary-blue);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  transition: all 0.2s;
}

.btn-primary:hover {
  background-color: var(--secondary-blue);
}

.card {
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.2s;
}

.card:hover {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.nav-link {
  color: #4b5563;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
}

.nav-link:hover {
  color: var(--primary-blue);
  background-color: #f3f4f6;
}

</style>

  <main class="flex-grow container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold  mb-8">Course Resources</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($resources as $resource)
      <div class="card bg-white p-6">
        <h2 class="text-xl font-semibold  mb-2">{{ $resource->course_code }}</h2>
        <p class="text-gray-500 mb-4">{{ $resource->course_title }}</p>
        <a href="{{ route('resources.show', $resource) }}" class="text-blue-600 hover:underline">
            View Materials
        </a>
        <a href="{{ route('resources.full.show', $resource) }}" style="float: right;" class="text-blue-600 hover:underline">
            Read Full
        </a>
      </div>
      @endforeach
    </div>
  </main>
@endsection


  

