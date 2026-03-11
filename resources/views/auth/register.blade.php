@extends('layouts.auth')

@section('content')
    <div class="w-full max-w-md mx-auto px-4  ">
        <div class="text-center mb-8 animate-float">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-navy-600 via-navy-700 to-navy-900 flex items-center justify-center shadow-lg shadow-navy-500/50 mb-4 border border-gold-400/50 animate-pulse-border relative group overflow-hidden">
                <div class="absolute inset-0 bg-gold-400/20 group-hover:scale-150 transition-transform duration-500 rounded-2xl blur-md"></div>
                <svg class="w-8 h-8 text-gold-400 relative z-10 transform group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-white via-gold-200 to-white tracking-tight drop-shadow-lg">SIPERA</h1>
            <p class="text-navy-200 text-sm mt-2 font-medium tracking-wide">Buat Akun Baru</p>
        </div>
        
        <div class="bg-white/10 backdrop-blur-2xl border border-white/20 rounded-3xl shadow-[0_0_40px_rgba(0,0,0,0.3)] p-8 relative overflow-hidden group/card hover:border-gold-400/30 transition-colors duration-500">
            <!-- Glass reflection effect -->
            <div class="absolute -top-24 -left-24 w-48 h-48 bg-white/10 rounded-full blur-2xl group-hover/card:bg-gold-500/10 transition-colors duration-500 pointer-events-none"></div>
            
            <h2 class="text-2xl font-bold text-white mb-8 relative z-10 flex items-center gap-2">
                <span class="w-8 h-1 bg-gold-400 rounded-full inline-block"></span>
                {{ __('Register') }}
            </h2>
            
            <form method="POST" action="{{ route('register') }}" class="space-y-5 relative z-10">
                @csrf
                <div class="relative group/input">
                    <label for="name" class="block text-xs font-bold text-navy-200 uppercase tracking-wider mb-1 px-1 transition-colors group-hover/input:text-gold-300">{{ __('Name') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-navy-300 group-hover/input:text-gold-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" autofocus
                            class="block w-full pl-11 pr-4 py-3 bg-navy-900/40 border @error('name') border-danger-400 @else border-white/10 @enderror rounded-xl text-white placeholder-navy-400 text-sm focus:ring-2 focus:ring-gold-400/50 focus:border-gold-400 focus:bg-navy-900/60 transition-all duration-300 hover:bg-navy-900/50 hover:border-white/20"
                            placeholder="Nama lengkap">
                    </div>
                    @error('name')<p class="text-sm text-danger-300 mt-1 pl-1 ">{{ $message }}</p>@enderror
                </div>
                
                <div class="relative group/input">
                    <label for="email" class="block text-xs font-bold text-navy-200 uppercase tracking-wider mb-1 px-1 transition-colors group-hover/input:text-gold-300">{{ __('Email Address') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-navy-300 group-hover/input:text-gold-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email"
                            class="block w-full pl-11 pr-4 py-3 bg-navy-900/40 border @error('email') border-danger-400 @else border-white/10 @enderror rounded-xl text-white placeholder-navy-400 text-sm focus:ring-2 focus:ring-gold-400/50 focus:border-gold-400 focus:bg-navy-900/60 transition-all duration-300 hover:bg-navy-900/50 hover:border-white/20"
                            placeholder="email@example.com">
                    </div>
                    @error('email')<p class="text-sm text-danger-300 mt-1 pl-1 ">{{ $message }}</p>@enderror
                </div>
                
                <div class="relative group/input" x-data="{ showPassword: false }">
                    <label for="password" class="block text-xs font-bold text-navy-200 uppercase tracking-wider mb-1 px-1 transition-colors group-hover/input:text-gold-300">{{ __('Password') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-navy-300 group-hover/input:text-gold-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" autocomplete="new-password"
                            class="block w-full pl-11 pr-12 py-3 bg-navy-900/40 border @error('password') border-danger-400 @else border-white/10 @enderror rounded-xl text-white placeholder-navy-400 text-sm focus:ring-2 focus:ring-gold-400/50 focus:border-gold-400 focus:bg-navy-900/60 transition-all duration-300 hover:bg-navy-900/50 hover:border-white/20"
                            placeholder="••••••••">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-navy-400 hover:text-white hover:scale-110 transition-all duration-200">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        </button>
                    </div>
                    @error('password')<p class="text-sm text-danger-300 mt-1 pl-1 ">{{ $message }}</p>@enderror
                </div>
                
                <div class="relative group/input" x-data="{ showPassword: false }">
                    <label for="password-confirm" class="block text-xs font-bold text-navy-200 uppercase tracking-wider mb-1 px-1 transition-colors group-hover/input:text-gold-300">{{ __('Confirm Password') }}</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-navy-300 group-hover/input:text-gold-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input id="password-confirm" :type="showPassword ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password"
                            class="block w-full pl-11 pr-12 py-3 bg-navy-900/40 border border-white/10 rounded-xl text-white placeholder-navy-400 text-sm focus:ring-2 focus:ring-gold-400/50 focus:border-gold-400 focus:bg-navy-900/60 transition-all duration-300 hover:bg-navy-900/50 hover:border-white/20"
                            placeholder="••••••••">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-navy-400 hover:text-white hover:scale-110 transition-all duration-200">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="relative group overflow-hidden w-full py-3.5 px-4 text-sm font-extrabold text-navy-900 bg-gradient-to-r from-gold-500 to-gold-400 rounded-xl hover:to-gold-300 transition-all shadow-[0_0_20px_rgba(234,179,8,0.3)] transform hover:-translate-y-1 hover:shadow-[0_0_25px_rgba(234,179,8,0.5)] mt-6">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        {{ __('Register') }}
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </span>
                    <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-transparent via-white/40 to-transparent -translate-x-[150%] group-hover:translate-x-[150%] transition-transform duration-700 ease-in-out"></div>
                </button>
                
                <div class="mt-4 border-t border-white/10 text-center pt-5">
                    <p class="text-navy-300 text-sm">Kembali ke 
                        <a href="{{ route('login') }}" class="text-white font-bold ml-1 hover:text-gold-400 transition-colors relative inline-block after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-full after:h-0.5 after:bg-gold-400 after:transform after:scale-x-0 cursor-pointer hover:after:scale-x-100 after:transition-transform after:duration-300">Login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection
