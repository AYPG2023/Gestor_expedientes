<x-layouts.app :title="__('Dashboard')">
    <div class="flex flex-col gap-6 h-full w-full flex-1 rounded-xl justify-center">

        {{-- Texto de bienvenida dinámico --}}
        <div class="w-full bg-gradient-to-r from-sky-500 to-blue-600 text-white rounded-xl shadow-lg p-8 relative overflow-hidden">
            <div class="absolute right-0 top-0 opacity-10 text-[10rem] font-bold pointer-events-none">UDEVIPO</div>

            {{-- Efecto máquina de escribir con cursor persistente --}}
            <h1 class="text-4xl font-extrabold mb-4 whitespace-nowrap overflow-hidden inline-block typing">
                Bienvenido al Gestor de Archivos
            </h1>

            <p class="text-lg text-white/90 max-w-3xl animate-fade-in-up">
                Esta plataforma facilita la carga, organización y control de expedientes ciudadanos. Incluye estadísticas en tiempo real, control de acceso personalizado y una gestión documental eficiente y segura.
            </p>
        </div>

        {{-- Imagen decorativa inferior (responsiva y más pequeña) --}}
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 shadow-md">
            <img src="{{ asset('img/dashboard_logo.jpeg') }}" alt="Imagen Gestor de Archivos"
                class="w-full h-[250px] sm:h-[300px] md:h-[280px] lg:h-[260px] xl:h-[250px] object-cover object-center" />
        </div>
    </div>

    {{-- Animaciones suaves con Tailwind + efecto de máquina de escribir --}}
    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    
        .animate-fade-in-up {
            animation: fade-in-up 0.5s ease-out forwards;
        }
    
        @keyframes typing {
            from { width: 0; }
            to { width: 100%; }
        }
    
        @keyframes blink {
            50% { border-color: transparent; }
        }
    
        .typing {
            width: 0;
            white-space: nowrap;
            overflow: hidden;
            
            animation:
                typing 7s steps(40, end) forwards,
                blink 1s step-end 6;
            animation-fill-mode: forwards;
        }
    </style>
    
</x-layouts.app>
