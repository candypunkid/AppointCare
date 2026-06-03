<nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">
                        {{ config('app.name', 'AppointCare') }}
                    </a>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <span class="text-gray-700">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="text-gray-600 hover:text-gray-900">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
