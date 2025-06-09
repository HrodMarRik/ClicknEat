<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'nav-link-active' : '' }}">
                                Utilisateurs
                            </a>
                            <a href="{{ route('admin.restaurants.index') }}" class="nav-link {{ request()->routeIs('admin.restaurants.*') ? 'nav-link-active' : '' }}">
                                Restaurants
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'nav-link-active' : '' }}">
                                Commandes
                            </a>
                        @elseif(auth()->user()->role === 'restaurateur')
                            <a href="{{ route('restaurateur.dashboard') }}" class="nav-link {{ request()->routeIs('restaurateur.dashboard') ? 'nav-link-active' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('restaurateur.restaurant.index') }}" class="nav-link {{ request()->routeIs('restaurateur.restaurant.*') ? 'nav-link-active' : '' }}">
                                Mon Restaurant
                            </a>
                            <a href="{{ route('restaurateur.categories.index') }}" class="nav-link {{ request()->routeIs('restaurateur.categories.*') ? 'nav-link-active' : '' }}">
                                Catégories
                            </a>
                            <a href="{{ route('restaurateur.dishes.index') }}" class="nav-link {{ request()->routeIs('restaurateur.dishes.*') ? 'nav-link-active' : '' }}">
                                Plats
                            </a>
                            <a href="{{ route('restaurateur.orders.index') }}" class="nav-link {{ request()->routeIs('restaurateur.orders.*') ? 'nav-link-active' : '' }}">
                                Commandes
                            </a>
                        @else
                            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'nav-link-active' : '' }}">
                                Accueil
                            </a>
                            <a href="{{ route('restaurants.index') }}" class="nav-link {{ request()->routeIs('restaurants.*') ? 'nav-link-active' : '' }}">
                                Restaurants
                            </a>
                            <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'nav-link-active' : '' }}">
                                Mes Commandes
                            </a>
                        @endif
                    @else
                        <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'nav-link-active' : '' }}">
                            Accueil
                        </a>
                        <a href="{{ route('restaurants.index') }}" class="nav-link {{ request()->routeIs('restaurants.*') ? 'nav-link-active' : '' }}">
                            Restaurants
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                    @if(auth()->user()->role !== 'restaurateur')
                        <a href="{{ route('cart.index') }}" class="nav-link mr-4 relative">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">
                                {{ session('cart') ? count(session('cart')) : 0 }}
                            </span>
                        </a>
                    @endif

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Se connecter</a>
                    <a href="{{ route('register') }}" class="nav-link ml-4">S'inscrire</a>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @if(auth()->user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        Utilisateurs
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.restaurants.index')" :active="request()->routeIs('admin.restaurants.*')">
                        Restaurants
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                        Commandes
                    </x-responsive-nav-link>
                @elseif(auth()->user()->role === 'restaurateur')
                    <x-responsive-nav-link :href="route('restaurateur.dashboard')" :active="request()->routeIs('restaurateur.dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('restaurateur.restaurant.index')" :active="request()->routeIs('restaurateur.restaurant.*')">
                        Mon Restaurant
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('restaurateur.categories.index')" :active="request()->routeIs('restaurateur.categories.*')">
                        Catégories
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('restaurateur.dishes.index')" :active="request()->routeIs('restaurateur.dishes.*')">
                        Plats
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('restaurateur.orders.index')" :active="request()->routeIs('restaurateur.orders.*')">
                        Commandes
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        Accueil
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('restaurants.index')" :active="request()->routeIs('restaurants.*')">
                        Restaurants
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                        Mes Commandes
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    Accueil
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('restaurants.index')" :active="request()->routeIs('restaurants.*')">
                    Restaurants
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('login')">
                        Se connecter
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        S'inscrire
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>

<script>
    // Toggle mobile menu
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
</script>
