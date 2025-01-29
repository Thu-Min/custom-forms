<nav class="flex items-center justify-between flex-wrap bg-blue-400 p-6">
    <div class="flex items-center flex-shrink-0 text-white mr-6">
        <a href="{{ route('dashboard') }}" class="font-semibold text-xl tracking-tight">
            Custom Forms
        </a>
    </div>
    <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto">
        <div class="text-sm lg:flex-grow">
            <a href="#responsive-header" class="block mt-4 lg:inline-block lg:mt-0 text-white hover:text-white mr-4">
                Docs
            </a>
        </div>
        <div>
            @if (auth()->check())
                <span class="mr-3">{{ auth()->user()->name }}</span>
                <a
                    href="{{ route('logout') }}"
                    class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-teal-500 hover:bg-white mt-4 lg:mt-0"
                >
                    Logout
                </a>
            @else
                <a
                    href="{{ route('login') }}"
                    class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-teal-500 hover:bg-white mt-4 lg:mt-0"
                >
                    Login
                </a>
            @endif
        </div>
    </div>
</nav>
