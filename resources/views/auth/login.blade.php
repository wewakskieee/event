<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EventTix</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md px-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="text-4xl font-bold text-indigo-600">
                    🎫 EventTix
                </a>
                <p class="text-gray-600 mt-2">Login to your account</p>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
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

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">
                        Email Address
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus
                           class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-semibold mb-2">
                        Password
                    </label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required
                           class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition">
                </div>

                <div class="mb-6 flex items-center">
                    <input type="checkbox" 
                           name="remember" 
                           id="remember"
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">
                        Remember me
                    </label>
                </div>

                <button type="submit" 
                        class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-bold transition transform hover:scale-105 shadow-lg">
                    Login
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                        Register here
                    </a>
                </p>
            </div>

            <!-- Back to Home -->
            <div class="mt-4 text-center">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700 text-sm">
                    ← Back to Home
                </a>
            </div>
        </div>

        <!-- Demo Credentials -->
        <div class="mt-6 bg-white/20 backdrop-blur-sm text-white rounded-lg p-4 text-sm">
            <p class="font-bold mb-2">Demo Credentials:</p>
            <p>Admin: admin@eventtix.com / password</p>
            <p>User: john@example.com / password</p>
        </div>
    </div>
</body>
</html>
