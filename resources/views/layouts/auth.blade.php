<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name', 'SIPERA') }} — Login</title>
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin_assets/images/favicon/favicon.ico') }}">
  <meta name="view-transition" content="same-origin" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <!-- AlpineJS for interactive components like showing/hiding password -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    /* View Transition Customization */
    ::view-transition-old(root),
    ::view-transition-new(root) {
      animation-duration: 0.3s;
    }

    .page-fade-out {
      opacity: 0;
      transition: opacity 0.25s ease-out;
    }
  </style>
  <style>
    @keyframes blob {

      0%,
      100% {
        transform: translate(0, 0) scale(1);
      }

      33% {
        transform: translate(30px, -50px) scale(1.1);
      }

      66% {
        transform: translate(-20px, 20px) scale(0.9);
      }
    }

    .animate-blob {
      animation: blob 7s infinite;
    }

    .animation-delay-2000 {
      animation-delay: 2s;
    }

    .animation-delay-4000 {
      animation-delay: 4s;
    }

    @keyframes float {
      0% {
        transform: translateY(0px);
      }

      50% {
        transform: translateY(-15px);
      }

      100% {
        transform: translateY(0px);
      }
    }

    .animate-float {
      animation: float 4s ease-in-out infinite;
    }

    @keyframes fade-in-up {
      0% {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
      }

      100% {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    . {
      animation: fade-in-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes gradient-xy {

      0%,
      100% {
        background-size: 400% 400%;
        background-position: 0% 0%;
      }

      50% {
        background-size: 200% 200%;
        background-position: 100% 100%;
      }
    }

    .animate-gradient-xy {
      animation: gradient-xy 15s ease infinite;
    }

    @keyframes pulse-border {
      0% {
        box-shadow: 0 0 0 0 rgba(234, 179, 8, 0.4);
      }

      70% {
        box-shadow: 0 0 0 10px rgba(234, 179, 8, 0);
      }

      100% {
        box-shadow: 0 0 0 0 rgba(234, 179, 8, 0);
      }
    }

    .animate-pulse-border {
      animation: pulse-border 2s infinite;
    }
  </style>
</head>

<body class="bg-slate-950 font-sans min-h-screen antialiased flex items-center justify-center relative overflow-hidden">

  <!-- Animated Background -->
  <div
    class="fixed inset-0 w-full h-full bg-gradient-to-br from-navy-900 via-slate-900 to-navy-950 animate-gradient-xy z-0">
  </div>

  <!-- Floating Blobs -->
  <div class="fixed inset-0 w-full h-full overflow-hidden pointer-events-none z-0">
    <div
      class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-gold-500/20 rounded-full mix-blend-screen filter blur-[100px] opacity-70 animate-blob">
    </div>
    <div
      class="absolute top-[20%] right-[-10%] w-96 h-96 bg-indigo-500/20 rounded-full mix-blend-screen filter blur-[100px] opacity-70 animate-blob animation-delay-2000">
    </div>
    <div
      class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-cyan-500/20 rounded-full mix-blend-screen filter blur-[100px] opacity-70 animate-blob animation-delay-4000">
    </div>
  </div>

  <div class="relative z-10 w-full">
    @yield('content')
  </div>

  <!-- SweetAlert2 for beautiful notifications -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        },
        background: 'rgba(15, 23, 42, 0.95)',
        color: '#fff',
        customClass: {
          popup: 'backdrop-blur-xl border border-white/10 shadow-2xl rounded-2xl'
        }
      });

      // Handle generic errors passed via session
      @if(session('error'))
        Toast.fire({
          icon: 'error',
          title: '{{ session('error') }}'
        });
      @endif

      // Handle success messages
      @if(session('success'))
        Toast.fire({
          icon: 'success',
          title: '{{ session('success') }}',
          iconColor: '#34d399'
        });
      @endif

      // Handle validation errors (specifically for login/register failures)
      @if($errors->any())
        Toast.fire({
          icon: 'warning',
          title: 'Validasi Gagal',
          text: 'Harap periksa kembali isian form Anda.',
          iconColor: '#fbbf24'
        });
      @endif
    });

    // SPA-like simple page transitions for links
    document.addEventListener('DOMContentLoaded', () => {
      const links = document.querySelectorAll('a[href]:not([target="_blank"]):not([href^="#"]):not([href^="javascript:"]):not([onclick])');
      links.forEach(link => {
        link.addEventListener('click', e => {
          if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
          if (link.origin !== window.location.origin) return;
          e.preventDefault();
          document.body.classList.add('page-fade-out');
          setTimeout(() => { window.location.href = link.href; }, 200);
        });
      });
    });
    window.addEventListener('pageshow', (e) => {
      if (e.persisted) document.body.classList.remove('page-fade-out');
    });
  </script>

</body>

</html>