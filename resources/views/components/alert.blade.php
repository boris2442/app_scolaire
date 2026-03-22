@if (session()->has('success') || session()->has('error') || $errors->any())
    <div id="alert-container" class="fixed top-20 right-4 z-[100] space-y-3 w-80 animate-fade-in-down">

        {{-- CAS SUCCÈS --}}
        @if (session('success'))
            <div
                class="alert-item bg-card/80 backdrop-blur-md border border-success/20 border-l-4 border-l-success p-4 rounded-xl shadow-2xl flex items-start gap-3 transition-all duration-500">
                <div class="text-success mt-0.5"><i class="fas fa-check-circle text-lg"></i></div>
                <div class="flex-1">
                    <p class="text-[10px] font-black uppercase tracking-widest text-success">Succès</p>
                    <p class="text-sm font-medium text-foreground">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()"
                    class="text-muted-foreground hover:text-foreground transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        @endif

        {{-- CAS ERREUR (Validation ou Session) --}}
        @if (session('error') || $errors->any())
            <div
                class="alert-item bg-card/80 backdrop-blur-md border border-danger/20 border-l-4 border-l-danger p-4 rounded-xl shadow-2xl flex items-start gap-3">
                <div class="text-danger mt-0.5"><i class="fas fa-exclamation-triangle text-lg"></i></div>
                <div class="flex-1">
                    <p class="text-[10px] font-black uppercase tracking-widest text-danger">Attention</p>
                    <ul class="text-sm font-medium text-foreground list-none">
                        @if (session('error'))
                            <li>{{ session('error') }}</li>
                        @else
                            <li>Veuillez corriger les erreurs.</li>
                        @endif
                    </ul>
                </div>
                <button onclick="this.parentElement.remove()" class="text-muted-foreground hover:text-foreground">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        @endif
    </div>

    <script>
        // Disparition automatique après 4 secondes
        document.querySelectorAll('.alert-item').forEach(el => {
            setTimeout(() => {
                el.style.opacity = '0';
                el.style.transform = 'translateX(50px)';
                setTimeout(() => el.remove(), 500);
            }, 4000);
        });
    </script>
@endif
