@if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('profile.update', ['user' => Auth::user()->username]) }}" method="POST" class="space-y-6">
    @csrf
    @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first-name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-feather="user" class="text-gray-400"></i>
                            </div>
                            <input type="text" id="first-name" name="firstname"
                                class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="John" value="{{ old('firstname', Auth::user()->firstname) }}">
                        </div>
                    </div>
                    
                    <div>
                        <label for="last-name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-feather="user" class="text-gray-400"></i>
                            </div>
                            <input type="text" id="last-name" name="lastname"
                                class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Doe" value="{{ old('lastname', Auth::user()->lastname) }}">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-feather="user" class="text-gray-400"></i>
                        </div>
                        <input type="text" id="u-name" name="username"
                            class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="username" value="{{ old('username', Auth::user()->username) }}">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-feather="mail" class="text-gray-400"></i>
                        </div>
                        <input type="email" id="email" name="email"
                            class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="you@example.com" value="{{ old('email', Auth::user()->email) }}">
                    </div>
                </div>
                <div>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                        Save Changes
                    </button>
                </div>
            </form>