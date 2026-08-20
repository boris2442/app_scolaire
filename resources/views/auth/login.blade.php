<x-guest-layout>
    <div
        class="fixed inset-0 min-h-screen w-screen grid grid-cols-1 lg:grid-cols-2 bg-background text-foreground transition-colors duration-300 z-50 overflow-hidden !p-0 !max-w-none">

        <div
            class="hidden lg:flex flex-col justify-center items-start px-12 xl:px-20 bg-secondary/30 border-r border-border h-full w-full">
            <div class="w-full max-w-sm mx-auto">

                <div class="mb-8">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo AcademiaPro"
                            class="h-16 w-auto object-contain">
                    </a>
                </div>

                <h1 class="text-2xl font-bold tracking-tight mb-3 text-foreground">
                    Bienvenue sur notre plateforme
                </h1>

                <p class="text-xs text-gray-500 dark:text-gray-400 mb-6 font-medium leading-relaxed">
                    Connectez-vous pour accéder à votre tableau de bord et découvrir toutes les fonctionnalités de notre
                    application.
                </p>

                <div class="space-y-3 w-full">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-card border border-border/60 shadow-sm">
                        <span
                            class="text-success text-xs flex items-center justify-center w-5 h-5 rounded-full bg-success/10">✓</span>
                        <span class="text-xs font-medium text-foreground">Interface intuitive et moderne</span>
                    </div>
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-card border border-border/60 shadow-sm">
                        <span
                            class="text-success text-xs flex items-center justify-center w-5 h-5 rounded-full bg-success/10">✓</span>
                        <span class="text-xs font-medium text-foreground">Sécurité renforcée avec chiffrement SSL</span>
                    </div>
                </div>

            </div>
        </div>

        <div
            class="flex flex-col justify-center items-center h-full w-full px-6 sm:px-12 bg-background overflow-y-auto">
            <div class="w-full max-w-sm mx-auto py-8">

                <div class="block lg:hidden text-center mb-8">
                    <a href="{{ url('/') }}" class="inline-block">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo AcademiaPro"
                            class="h-20 w-auto mx-auto object-contain">
                    </a>
                </div>

                <div class="text-center lg:text-left mb-6">
                    <h2 class="text-xl font-bold tracking-tight text-foreground">
                        Connectez-vous à votre compte
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">
                        Entrez vos identifiants pour accéder au tableau de bord
                    </p>
                </div>

                @if (session('status'))
                    <div
                        class="mb-4 text-xs font-medium p-3 rounded-xl bg-success/10 text-success border border-success/20 w-full">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="bg-card border border-border p-6 sm:p-8 rounded-2xl shadow-sm w-full box-border">
                    <form method="POST" action="{{ route('login') }}" class="w-full space-y-4">
                        @csrf

                        <div class="w-full space-y-1.5">
                            <label for="login" class="text-xs font-medium text-gray-500 block">
                                Login
                            </label>
                            <input id="login" type="text" name="login" value="{{ old('login') }}" required
                                autofocus placeholder="Ex:  ou 6XXXXXXXX"
                                class="block w-full px-3 py-2.5 bg-background border border-input rounded text-xs font-medium focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/50 transition-all text-foreground" />
                            @error('login')
                                <p class="text-[11px] font-medium text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full space-y-1.5">
                            <label for="password" class="text-xs font-medium text-gray-500 block">
                                Mot de passe
                            </label>

                            <div class="relative">
                                <input id="password" type="password" name="password" required
                                    autocomplete="current-password" placeholder="••••••••"
                                    class="block w-full px-3 py-2.5 pr-10 bg-background border border-input rounded text-xs font-medium focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/50 transition-all text-foreground" />

                                <button type="button" id="togglePassword"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-primary transition-colors">
                                
                                    <x-lucide-eye class="w-4 h-4" />
                                </button>
                            </div>

                            @error('password')
                                <p class="text-[11px] font-medium text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {

                                const passwordInput = document.getElementById('password');
                                const togglePassword = document.getElementById('togglePassword');

                                if (!passwordInput || !togglePassword) return;

                                togglePassword.addEventListener('click', () => {

                                    const icon = togglePassword.querySelector('i');

                                    if (passwordInput.type === 'password') {
                                        passwordInput.type = 'text';

                                        if (icon) {
                                            icon.classList.remove('fa-eye');
                                            icon.classList.add('fa-eye-slash');
                                        }
                                    } else {
                                        passwordInput.type = 'password';

                                        if (icon) {
                                            icon.classList.remove('fa-eye-slash');
                                            icon.classList.add('fa-eye');
                                        }
                                    }
                                });

                            });
                        </script>
                        <div class="flex flex-row items-center justify-between w-full pt-1 text-xs gap-2">
                            <label for="remember_me"
                                class="inline-flex items-center cursor-pointer select-none text-gray-500 font-medium">
                                <input id="remember_me" type="checkbox" name="remember"
                                    class="rounded border-input bg-background text-primary shadow-sm focus:ring-primary/30 w-3.5 h-3.5 transition-colors">
                                <span class="ms-2">Se souvenir de moi</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-danger hover:underline font-medium text-[11px]"
                                    href="{{ route('password.request') }}">
                                    Mot de passe oublié ?
                                </a>
                            @endif
                        </div>

                        <div class="pt-2 w-full">
                            <button type="submit"
                                class="block w-full bg-primary text-primary-foreground py-2.5 px-4 rounded font-bold text-xs transition-all text-center shadow-sm hover:opacity-95">
                                Se connecter
                            </button>
                        </div>



                    </form>
                </div>

                <div class="mt-6 text-center w-full">
                    <p class="text-[11px] font-medium text-gray-400">
                        Secure connection with SSL encryption
                    </p>
                </div>

            </div>
        </div>

    </div>
</x-guest-layout>
