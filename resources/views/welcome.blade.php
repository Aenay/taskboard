<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WorkStream</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gradient-to-br from-indigo-950 via-indigo-900 to-purple-900">
        <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen">
            <!-- Navigation -->
            @if (Route::has('login'))
                <nav class="absolute top-0 right-0 px-6 py-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-300 hover:text-white font-semibold transition duration-150 ease-in-out">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white font-semibold transition duration-150 ease-in-out">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-gray-300 hover:text-white font-semibold transition duration-150 ease-in-out">Register</a>
                        @endif
                    @endauth
                </nav>
            @endif

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                <div class="flex flex-col items-center justify-center">
                    <!-- Logo -->
                    <div class="mb-8">
                        <svg class="w-20 h-20 text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"></path>
                        </svg>
                    </div>

                    <!-- Typing Animation Text -->
                    <h1 class="typing-text text-5xl md:text-6xl lg:text-7xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-300 mb-8">
                        Welcome to WorkStream
                    </h1>

                    <!-- Subtitle -->
                    <p class="mt-4 text-gray-400 text-lg text-center max-w-2xl">
                        Streamline your workflow, enhance team collaboration, and boost productivity with our intuitive task management platform.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this style section in your head or in a separate CSS file -->
    <style>
        .typing-text {
            overflow: hidden;
            border-right: .15em solid transparent;
            white-space: nowrap;
            margin: 0 auto;
            animation:
                typing 3.5s steps(40, end),
                blink-caret .75s step-end infinite;
        }

        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }

        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: #a78bfa }
        }

        @media (max-width: 640px) {
            .typing-text {
                font-size: 2rem;
            }
        }
    </style>
</body>
</html>
