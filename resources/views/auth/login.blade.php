<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EventTix</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md px-6">

        <div class="bg-gray-800/60 backdrop-blur-md border border-gray-700 rounded-2xl shadow-2xl p-8">

            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="text-4xl font-semibold bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-purple-400">
                    EventTix
                </a>
                <p class="text-gray-400 mt-2">Login to continue</p>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
            <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-300 text-sm font-medium mb-2">
                        Email
                    </label>
                    <input id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required autofocus
                        class="w-full bg-gray-900 border border-gray-700 text-gray-200 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-300 text-sm font-medium mb-2">
                        Password
                    </label>
                    <input id="password" 
                        type="password" 
                        name="password" 
                        required
                        class="w-full bg-gray-900 border border-gray-700 text-gray-200 rounded-lg px-4 py-3 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                </div>

                <!-- Remember -->
                <div class="mb-6 flex items-center">
                    <input type="checkbox" 
                        name="remember" 
                        id="remember"
                        class="w-4 h-4 text-indigo-500 bg-gray-900 border-gray-600 rounded focus:ring-indigo-400">
                    <label for="remember" class="ml-2 text-sm text-gray-400">
                        Remember me
                    </label>
                </div>

                <!-- Button -->
                <button type="submit" 
                    class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:opacity-90 transition transform hover:scale-[1.02] shadow-lg">
                    Login
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-6 text-center text-gray-400">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 font-medium">
                    Register here
                </a>
            </div>

            <!-- Back -->
            <div class="mt-4 text-center">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-300 text-sm transition">
                    ← Back to Home
                </a>
            </div>
        </div>


</body>
</html>
