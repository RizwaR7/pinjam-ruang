@if(!request()->header('HX-Request'))
    <!-- Global Preloader -->
    <div id="global-loader" x-data="{ show: true }" x-init="
            setTimeout(() => {
                // Validasi state document saat script ini berjalan
                if (document.readyState === 'complete') {
                    show = false;
                } else {
                    // Jika belum complete, tunggu event load browser
                    window.addEventListener('load', () => { setTimeout(() => show = false, 250); });
                    // Fallback timeout jaga-jaga apabila window.load gagal ter-trigger
                    setTimeout(() => { show = false; }, 3000);
                }
            }, 10);
        " x-show="show" x-transition:leave="transition fade-out duration-700 ease-in-out"
        class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-slate-50 dark:bg-navy-950 backdrop-blur-xl">

        <div class="relative flex items-center justify-center">
            <!-- Outer ringing pulse -->
            <div class="absolute inset-0 w-24 h-24 bg-gold-500/20 rounded-full animate-ping"></div>

            <!-- Inner spinning container -->
            <div
                class="relative w-20 h-20 bg-white dark:bg-navy-800 rounded-2xl shadow-2xl flex items-center justify-center border border-slate-200 dark:border-navy-600 overflow-hidden">
                <!-- Shimmer effect -->
                <div
                    class="absolute inset-0 -translate-x-full animate-[shimmer_2s_infinite] bg-gradient-to-r from-transparent via-white/40 dark:via-white/5 to-transparent skew-x-12">
                </div>

                <!-- Logo Icon -->
                <svg class="w-10 h-10 text-gold-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                    </path>
                </svg>
            </div>
        </div>

        <!-- Text -->
        <div class="mt-8 text-center">
            <h3 class="text-xl font-extrabold text-navy-900 dark:text-white tracking-tight animate-pulse">SIRUANG</h3>
            <p class="text-xs font-semibold text-slate-500 dark:text-navy-300 tracking-widest uppercase mt-1">Memuat
                Sistem...</p>
        </div>

    </div>
@endif

<style>
    @keyframes shimmer {
        100% {
            transform: translateX(100%);
        }
    }
</style>