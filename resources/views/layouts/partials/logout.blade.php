
<div style="width: 100%">
    <form style="padding: 0px; margin-bottom: 0px;" action="{{ route('logout') }}" method="POST">
        @csrf
        <button class="button-with-icon-3 text-red-600 hover:text-red-700">
        Logout
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
            <path fill-rule="evenodd" d="M15.75 2.25a.75.75 0 01.75.75v3a.75.75 0 01-1.5 0V3.75H6A1.5 1.5 0 004.5 5.25v13.5A1.5 1.5 0 006 20.25h9v-2.25a.75.75 0 011.5 0v3a.75.75 0 01-.75.75h-9A3 3 0 013 18.75V5.25A3 3 0 016 2.25h9.75zM21.53 12.53a.75.75 0 010 1.06l-3.72 3.72a.75.75 0 01-1.06-1.06L19.19 13.5H9.75a.75.75 0 010-1.5h9.44l-2.44-2.44a.75.75 0 111.06-1.06l3.72 3.72z" clip-rule="evenodd"/>
        </svg>
        </button>
</form>
</div>