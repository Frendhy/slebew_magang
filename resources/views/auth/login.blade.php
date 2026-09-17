<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manufaktur Lanjutan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            900: '#112338',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans h-screen flex items-center justify-center relative overflow-hidden">
    <!-- Abstract Background -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-brand-100 blur-3xl opacity-60"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 rounded-full bg-blue-100 blur-3xl opacity-60"></div>

    <div class="z-10 w-full max-w-md p-8 bg-white backdrop-blur-md rounded-2xl shadow-sm border border-slate-200 text-center">
        <div class="w-16 h-16 bg-white border border-slate-200 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
            <svg class="w-10 h-10 text-brand-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
        <h2 class="text-3xl font-extrabold text-slate-900 mb-2">Login Akun</h2>
        <p class="text-slate-500 mb-8">Masuk untuk mengakses sistem manufaktur PT. Dunia Kimia Jaya</p>

        @if($errors->any())
            <div class="bg-red-500/20 border border-red-500/50 text-red-400 p-3 rounded-xl mb-6 text-sm text-left">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login" class="space-y-6 text-left">
            @csrf
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-600">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-white text-slate-900 placeholder-slate-400 py-3 px-4 focus:ring-brand-500 focus:border-brand-500 shadow-sm transition-colors" placeholder="user@dkj.com">
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-600">Kata Sandi</label>
                <input type="password" id="password" name="password" required class="mt-2 block w-full rounded-xl border border-slate-300 bg-white text-slate-900 placeholder-slate-400 py-3 px-4 focus:ring-brand-500 focus:border-brand-500 shadow-sm transition-colors" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-[0_0_15px_rgba(14,165,233,0.3)] text-lg font-bold text-white bg-brand-500 hover:bg-brand-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 focus:ring-offset-slate-50 transition-colors">
                Masuk ke Sistem
            </button>
        </form>
    </div>
</body>
</html>
