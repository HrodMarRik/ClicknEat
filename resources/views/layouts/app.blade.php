<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ClicknEat') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <nav class="bg-white shadow-lg">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                                    <x-application-logo class="block h-8 w-auto fill-current text-indigo-600" />
                                    <span class="text-xl font-bold text-indigo-600">ClicknEat</span>
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
                                @auth
                                    @if(auth()->user()->role === 'restaurateur')
                                        <a href="{{ route('restaurateur.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('restaurateur.dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                            Tableau de bord
                                        </a>
                                        <a href="{{ route('restaurateur.restaurant.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('restaurateur.restaurant.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                            Mes Restaurants
                                        </a>
                                        <a href="{{ route('restaurateur.orders.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('restaurateur.orders.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                            Commandes
                                        </a>
                                    @else
                                        <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('home') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                            Accueil
                                        </a>
                                        <a href="{{ route('restaurants.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('restaurants.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                            Restaurants
                                        </a>
                                        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('orders.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                            Mes Commandes
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('home') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                        Accueil
                                    </a>
                                    <a href="{{ route('restaurants.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('restaurants.*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}">
                                        Restaurants
                                    </a>
                                @endauth
                            </div>
                        </div>

                        <!-- Right Side -->
                        <div class="hidden sm:flex sm:items-center sm:space-x-3">
                            @auth
                                @if(auth()->user()->role === 'client')
                                    <!-- Cart -->
                                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:text-gray-900">
                                        <span class="sr-only">Panier</span>
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        @if(session('cart') && count(session('cart')) > 0)
                                            <span class="absolute top-0 right-0 -mt-1 -mr-1 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">
                                                {{ count(session('cart')) }}
                                            </span>
                                        @endif
                                    </a>
                                @endif

                                <!-- User Menu -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                                        <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ Auth::user()->name }}">
                                        <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                        <div class="py-1">
                                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Mon profil
                                            </a>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Se déconnecter
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Se connecter</a>
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    S'inscrire
                                </a>
                            @endauth
                        </div>

                        <!-- Mobile menu button -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        @auth
                            @if(auth()->user()->role === 'restaurateur')
                                <a href="{{ route('restaurateur.dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('restaurateur.dashboard') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }}">
                                    Tableau de bord
                                </a>
                                <a href="{{ route('restaurateur.restaurant.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('restaurateur.restaurant.*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }}">
                                    Mes Restaurants
                                </a>
                                <a href="{{ route('restaurateur.orders.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('restaurateur.orders.*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }}">
                                    Commandes
                                </a>
                            @else
                                <a href="{{ route('home') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('home') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }}">
                                    Accueil
                                </a>
                                <a href="{{ route('restaurants.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('restaurants.*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }}">
                                    Restaurants
                                </a>
                                <a href="{{ route('orders.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('orders.*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }}">
                                    Mes Commandes
                                </a>
                            @endif
                        @else
                            <a href="{{ route('home') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('home') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }}">
                                Accueil
                            </a>
                            <a href="{{ route('restaurants.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('restaurants.*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }}">
                                Restaurants
                            </a>
                        @endauth
                    </div>

                    <!-- Mobile user menu -->
                    <div class="pt-4 pb-3 border-t border-gray-200">
                        @auth
                            <div class="flex items-center px-4">
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ Auth::user()->name }}">
                                </div>
                                <div class="ml-3">
                                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                                </div>
                                @if(auth()->user()->role === 'client')
                                    <a href="{{ route('cart.index') }}" class="ml-auto flex-shrink-0 bg-white p-1 rounded-full text-gray-400 hover:text-gray-500">
                                        <span class="sr-only">Voir le panier</span>
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        @if(session('cart') && count(session('cart')) > 0)
                                            <span class="absolute top-0 right-0 -mt-1 -mr-1 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">
                                                {{ count(session('cart')) }}
                                            </span>
                                        @endif
                                    </a>
                                @endif
                            </div>
                            <div class="mt-3 space-y-1">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                    Mon profil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                        Se déconnecter
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mt-3 space-y-1">
                                <a href="{{ route('login') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                    Se connecter
                                </a>
                                <a href="{{ route('register') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                                    S'inscrire
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if (session('info'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('info') }}</span>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main class="flex-1 py-12">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200">
                <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <!-- Brand -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-2">
                                <x-application-logo class="h-8 w-8 text-indigo-600" />
                                <span class="text-xl font-bold text-indigo-600">ClicknEat</span>
                            </div>
                            <p class="text-gray-500 text-sm">
                                La meilleure façon de commander votre repas en ligne.
                            </p>
                        </div>

                        <!-- Navigation -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">
                                Navigation
                            </h3>
                            <ul class="space-y-4">
                                <li>
                                    <a href="{{ route('restaurants.index') }}" class="text-base text-gray-500 hover:text-gray-900">
                                        Restaurants
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                                        À propos
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                                        Contact
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Legal -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">
                                Légal
                            </h3>
                            <ul class="space-y-4">
                                <li>
                                    <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                                        Conditions d'utilisation
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                                        Politique de confidentialité
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="text-base text-gray-500 hover:text-gray-900">
                                        Mentions légales
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Social -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">
                                Suivez-nous
                            </h3>
                            <div class="flex space-x-6">
                                <a href="#" class="text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">Facebook</span>
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">Instagram</span>
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">Twitter</span>
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 border-t border-gray-200 pt-8">
                        <p class="text-base text-gray-400 text-center">
                            &copy; {{ date('Y') }} ClicknEat. Tous droits réservés.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
