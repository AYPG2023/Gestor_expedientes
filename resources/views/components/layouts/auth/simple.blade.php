<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    <style>
        @keyframes fly {
            0% {
                transform: translateX(-100px) translateY(0) rotate(10deg);
                opacity: 0;
            }
            20% {
                opacity: 0.8;
            }
            80% {
                opacity: 0.8;
            }
            100% {
                transform: translateX(100vw) translateY(-100px) rotate(-15deg);
                opacity: 0;
            }
        }

        .paper-plane {
            position: absolute;
            animation: fly 15s linear infinite;
            z-index: 20;
            width: 40px;
            height: 40px;
            color: rgba(77, 86, 86, 0.8);
        }

        
        .dark .logo-img {
            mix-blend-mode: normal;
        }
    </style>
</head>
<body class="relative min-h-screen w-full bg-white antialiased dark:bg-gradient-to-b dark:from-neutral-950 dark:to-neutral-900 overflow-hidden">

    {{-- Elementos decorativos --}}
    <div class="absolute top-[-50px] left-[-50px] w-72 h-72 bg-blue-500 opacity-20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-[-80px] right-[-80px] w-96 h-96 bg-indigo-400 opacity-20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute top-1/2 left-1/2 w-[600px] h-[600px] bg-gradient-to-r from-purple-500 via-blue-500 to-indigo-500 opacity-10 rounded-full blur-[140px] transform -translate-x-1/2 -translate-y-1/2"></div>

    {{-- Avión de papel animado --}}
    <div class="paper-plane" style="top: 20%; left: -40px; animation-delay: 2s;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
            <path fill="currentColor" d="M476.2 3.4L11.4 223.5c-15.1 7-14 29.3 1.7 34.6l111.4 38.2 52.4 158.2c5.5 16.6 26.9 20.6 37.7 6.9l62.4-78.6 100.7 84.2c11.3 9.4 28.5 2.5 30.7-12.2L508.6 34c2.3-15.8-13-28-27.4-30.6zm-94 396.8l-94.5-79-54.4 68.5-43.6-131.7 209.6-166-134.7 173.7 117.6 97.4z"/>
        </svg>
    </div>
    
    <div class="paper-plane" style="top: 70%; left: -40px; animation-delay: 7s;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
            <path fill="currentColor" d="M476.2 3.4L11.4 223.5c-15.1 7-14 29.3 1.7 34.6l111.4 38.2 52.4 158.2c5.5 16.6 26.9 20.6 37.7 6.9l62.4-78.6 100.7 84.2c11.3 9.4 28.5 2.5 30.7-12.2L508.6 34c2.3-15.8-13-28-27.4-30.6zm-94 396.8l-94.5-79-54.4 68.5-43.6-131.7 209.6-166-134.7 173.7 117.6 97.4z"/>
        </svg>
    </div>

    
    <div class="absolute top-0 left-0 right-0 z-10 px-6 py-4">
        <x-dual-logos
            left-logo="img/Logotipo-CIV.png"
            right-logo="img/udevipo.png"
            left-size="h-24"
            right-size="h-24"
            container-class="w-full flex justify-between items-start"
        />
    </div>

    {{-- Login --}}
    <div class="min-h-screen flex items-center justify-center px-4 pt-36 relative z-10">
        <div class="w-full max-w-xl p-10 bg-white/80 dark:bg-white/10 border border-white/20 shadow-[0_20px_40px_rgba(0,0,0,0.3)] backdrop-blur-md rounded-4xl">
            {{ $slot }}
        </div>
    </div>

    @fluxScripts
</body>
</html>