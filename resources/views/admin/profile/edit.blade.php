@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6 ">
        <!-- Header -->
        <div class="flex flex-col gap-2">
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Edit Profil</h1>
            <p class="text-sm text-slate-500">Sesuaikan informasi biodata, email, kata sandi, dan foto profil Anda.</p>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl shadow-sm space-y-2">
                <h3 class="text-sm font-semibold text-rose-800 flex items-center gap-2">
                    <i data-feather="alert-circle" class="w-4 h-4"></i>
                    Terdapat kesalahan pengisian:
                </h3>
                <ul class="text-xs text-rose-600 list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white/80 backdrop-blur-xl border border-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" hx-boost="false">
                class="divide-y divide-slate-100">
                @csrf
                @method('PUT')

                <!-- Avatar & Overview Section -->
                <div class="p-6 md:p-8 flex flex-col md:flex-row gap-8 items-start relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-navy-50/50 to-transparent pointer-events-none">
                    </div>

                    <div class="relative group mx-auto md:mx-0 shrink-0">
                        <div
                            class="w-28 h-28 rounded-full border-4 border-white shadow-lg overflow-hidden bg-slate-100 flex items-center justify-center">
                            @if ($user->avatar)
                                <img src="{{ Storage::url($user->avatar) }}?v={{ time() }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0D8ABC&color=fff"
                                    alt="Avatar" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <!-- Upload overlay -->
                        <label for="avatar_upload"
                            class="absolute inset-0 bg-slate-900/60 rounded-full flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer backdrop-blur-sm">
                            <i data-feather="camera" class="w-6 h-6 mb-1"></i>
                            <span class="text-[10px] font-medium uppercase tracking-wider">Ubah Foto</span>
                        </label>
                        <input type="file" name="avatar" class="border p-2">
                        accept="image/jpeg, image/png, image/webp" onchange="previewImage(event)">
                    </div>

                    <div class="flex-1 space-y-4 z-10 w-full">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap <span
                                    class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500 transition-all font-medium text-slate-800 placeholder-slate-400">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email <span
                                    class="text-rose-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500 transition-all font-medium text-slate-800 placeholder-slate-400">
                        </div>
                    </div>
                </div>

                <!-- Password Change Section -->
                <div class="p-6 md:p-8 bg-slate-50/30">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i data-feather="lock" class="w-4 h-4 text-navy-500"></i>
                        Ubah Kata Sandi
                    </h3>
                    <p class="text-xs text-slate-500 mb-6">Kosongkan bagian ini jika Anda tidak ingin mengubah kata sandi.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1 md:col-span-2">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi Saat
                                Ini</label>
                            <input type="password" name="current_password"
                                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500 transition-all placeholder-slate-300">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi Baru</label>
                            <input type="password" name="password"
                                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500 transition-all placeholder-slate-300">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Konfirmasi Kata Sandi
                                Baru</label>
                            <input type="password" name="password_confirmation"
                                class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-gold-500/20 focus:border-gold-500 transition-all placeholder-slate-300">
                        </div>
                    </div>
                </div>

                <!-- Action Area -->
                <div
                    class="px-6 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-3xl">
                    <a href="{{ route('admin.home') }}"
                        class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-200/50 hover:text-slate-800 rounded-xl transition-all">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 text-sm font-bold text-white bg-navy-600 hover:bg-navy-700 hover:shadow-lg hover:shadow-navy-500/30 rounded-xl transition-all flex items-center gap-2">
                        <i data-feather="save" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function previewImage(event) {
                const input = event.target;
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        // Find the image element inside the avatar ring and replace its src
                        const img = input.closest('.relative.group').querySelector('img');
                        if (img) {
                            img.src = e.target.result;
                        }
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    @endpush
@endsection