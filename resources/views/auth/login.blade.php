<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | {{ $lojaAtual->nome ?? 'Restaurante' }}</title>
    @vite(['resources/css/app.css'])
<link rel="icon" href="{{ asset('dobra/mark.svg') }}"></head>
<body class="db-auth"><aside class="db-auth-cover" @if(isset($lojaAtual) && $lojaAtual->banner_path) style="background-image:linear-gradient(rgba(0,0,0,.35),rgba(0,0,0,.7)),url('{{ asset('storage/'.$lojaAtual->banner_path) }}');background-size:cover;background-position:center" @endif><x-dobra-brand/>@unless(isset($lojaAtual) && $lojaAtual->banner_path)<img src="{{ asset('dobra/mesa.svg') }}" alt="">@endunless<p>{{ $lojaAtual->nome ?? 'O restaurante' }}<br>em um só lugar.</p></aside>
    <div class="max-w-md w-full mx-4">
        <!-- Card de Login -->
        <div class="bg-white rounded-lg shadow-2xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Entrada da equipe</h1>
                <p class="text-gray-600 mt-2">Use seu acesso ao painel do restaurante.</p>
            </div>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        E-mail
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        autofocus
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="seu@email.com"
                    >
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Senha
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition"
                        placeholder="••••••••"
                    >
                </div>

                <!-- Remember Me -->
                <div class="mb-6 flex items-center">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Lembrar-me
                    </label>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 ease-in-out transform hover:scale-105"
                >
                    Entrar
                </button>
            </form>

        </div>

        <p class="text-center text-white text-sm mt-4">
            &copy; {{ date('Y') }} Dobra.
        </p>
    </div>
</body>
</html>
