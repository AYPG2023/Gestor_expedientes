<x-layouts.app :title="__('Gestión de Archivos')">
    <div class="p-6 space-y-6">
        {{-- Título animado --}}
        <h2
            class="text-4xl font-extrabold bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-500 text-transparent bg-clip-text drop-shadow-lg animate-pulse">
            📁 Gestión de Expedientes.
        </h2>

        {{-- Formulario de subida --}}
        <form id="uploadForm" enctype="multipart/form-data"
            class="bg-white dark:bg-neutral-900 p-6 rounded-xl shadow-lg border border-gray-200 dark:border-neutral-700 space-y-4">
            @csrf
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <input type="file" name="files[]" id="fileInput" multiple
                    class="file:bg-indigo-600 file:hover:bg-indigo-700 file:text-white file:border-0 file:rounded file:px-4 file:py-2
                           dark:file:bg-purple-600 dark:file:hover:bg-purple-700 text-gray-800 dark:text-white bg-neutral-100 dark:bg-neutral-800 rounded w-full md:w-auto"
                    required>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    🚀 Subir Archivos
                </button>
            </div>

            <div id="progressContainer" class="space-y-4"></div>
            <div id="uploadMessage" class="mt-2 text-sm font-semibold hidden whitespace-pre-line"></div>
        </form>

        {{-- Filtro y búsqueda --}}
        <div
            class="bg-white dark:bg-neutral-900 border border-gray-200 dark:border-neutral-700 rounded-xl p-4 shadow-md">
            <form method="GET" action="{{ route('files.index') }}"
                class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar
                        por nombre</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Ej. expediente.pdf"
                        class="w-full px-4 py-2 rounded-lg border dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="fecha"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filtrar por
                        fecha</label>
                    <input type="date" name="fecha" id="fecha" value="{{ request('fecha') }}"
                        class="w-full px-4 py-2 rounded-lg border dark:bg-neutral-800 dark:border-neutral-700 dark:text-white focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="sm:self-end">
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded-lg transition">
                        🔍 Buscar
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabla de archivos --}}
        <div
            class="bg-white dark:bg-neutral-900 rounded-xl shadow-lg overflow-x-auto border border-gray-200 dark:border-neutral-700">
            <table
                class="min-w-full text-sm text-gray-800 dark:text-gray-200 divide-y divide-gray-200 dark:divide-neutral-700">
                <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                    <tr>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3 hidden md:table-cell">Icono</th>
                        <th class="px-4 py-3 hidden md:table-cell">Tamaño</th>
                        <th class="px-4 py-3 hidden md:table-cell">Subido por</th>
                        <th class="px-4 py-3 hidden md:table-cell">Fecha</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-800">
                    @forelse ($archivos as $archivo)
                        <tr class="hover:bg-gray-50 dark:hover:bg-neutral-800 transition-colors">
                            <td class="px-4 py-4 font-medium">{{ $archivo->nombre }}</td>
                            <td class="px-4 py-4 text-center hidden md:table-cell text-indigo-500 text-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5.121 17.804A13.937 13.937 0 0112 15c2.21 0 4.29.534 6.121 1.474M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </td>
                            <td class="px-4 py-4 hidden md:table-cell">
                                {{ number_format($archivo->tamano / 1024 / 1024, 2) }} MB</td>
                            <td class="px-4 py-4 hidden md:table-cell">{{ $archivo->usuario->name }}</td>
                            <td class="px-4 py-4 hidden md:table-cell">
                                {{ $archivo->subido_en->format('d/m/Y, g:i:s a') }}</td>
                            <td class="px-4 py-4 text-center space-y-1 sm:space-y-0 sm:space-x-2">
                                <a href="{{ asset('storage/' . $archivo->ruta) }}" target="_blank"
                                    class="inline-flex items-center gap-1 text-blue-500 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                                    👁 Ver
                                </a>
                                <a href="{{ asset('storage/' . $archivo->ruta) }}" download="{{ $archivo->nombre }}"
                                    class="inline-flex items-center gap-1 text-green-600 hover:text-green-800 dark:hover:text-green-400 font-medium">
                                    ⬇ Descargar
                                </a>
                                <button onclick="openRenameModal({{ $archivo->id }}, '{{ $archivo->nombre }}')"
                                    class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 dark:hover:text-indigo-400 font-medium cursor-pointer">
                                    ✏️ Renombrar
                                </button>
                                <button onclick="openMergeModal({{ $archivo->id }}, '{{ $archivo->nombre }}')"
                                    class="inline-flex items-center gap-1 text-purple-600 hover:text-purple-800 dark:hover:text-purple-400 font-medium cursor-pointer">
                                    🔗 Unir
                                </button>
                                <form method="POST" action="{{ route('files.destroy', $archivo) }}"
                                    onsubmit="return confirmarEliminacion(this);" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 dark:hover:text-red-400 font-medium cursor-pointer">
                                        ❌ Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 dark:text-gray-400 py-6 italic">
                                No hay archivos subidos aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Paginación --}}
            <div class="p-4">
                {{ $archivos->withQueryString()->links() }}
            </div>
        </div>
    </div>

    {{-- Script de subida con barra de progreso por archivo --}}
    <script>
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const files = document.getElementById('fileInput').files;
            const uploadMessage = document.getElementById('uploadMessage');
            const progressContainer = document.getElementById('progressContainer');
            progressContainer.innerHTML = '';
            uploadMessage.textContent = '';
            uploadMessage.classList.remove('text-green-500', 'text-red-500', 'dark:text-green-400',
                'dark:text-red-400');

            if (files.length > 5) {
                alert('Solo puedes subir hasta 5 archivos a la vez.');
                return;
            }

            // 🔄 Obtener lista de archivos existentes desde el backend
            let existentes = [];
            try {
                const response = await fetch('/archivos/nombres');
                existentes = await response.json();
            } catch (error) {
                alert('Error al verificar archivos existentes.');
                return;
            }

            let completados = 0;

            Array.from(files).forEach((file) => {
                if (existentes.includes(file.name)) {
                    const warning = document.createElement('div');
                    warning.textContent = `⚠️ El archivo "${file.name}" ya existe y no se subirá.`;
                    warning.classList.add('text-yellow-600', 'dark:text-yellow-400', 'text-sm',
                        'font-semibold');
                    progressContainer.appendChild(warning);
                    completados++;
                    if (completados === files.length) {
                        setTimeout(() => window.location.reload(), 4000);
                    }
                    return;
                }

                const formData = new FormData();
                formData.append('files[]', file);

                const wrapper = document.createElement('div');
                wrapper.classList.add('space-y-1');

                const label = document.createElement('div');
                label.textContent = `📄 ${file.name}`;
                label.classList.add('text-sm', 'font-medium', 'text-gray-700', 'dark:text-gray-300');

                const progressBar = document.createElement('div');
                progressBar.classList.add('bg-gray-300', 'rounded', 'overflow-hidden', 'h-3');
                const innerBar = document.createElement('div');
                innerBar.classList.add('bg-emerald-500', 'h-full');
                innerBar.style.width = '0%';
                progressBar.appendChild(innerBar);

                wrapper.appendChild(label);
                wrapper.appendChild(progressBar);
                progressContainer.appendChild(wrapper);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('files.store') }}', true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percent = (e.loaded / e.total) * 100;
                        innerBar.style.width = percent + '%';
                    }
                };

                xhr.onload = function() {
                    completados++;
                    uploadMessage.classList.remove('hidden');
                    if (xhr.status === 200) {
                        uploadMessage.classList.add('text-green-500', 'dark:text-green-400');
                        uploadMessage.textContent += `✅ ${file.name} subido correctamente.\n`;
                    } else {
                        uploadMessage.classList.add('text-red-500', 'dark:text-red-400');
                        uploadMessage.textContent += `❌ Error al subir ${file.name}.\n`;
                    }

                    if (completados === files.length) {
                        setTimeout(() => window.location.reload(), 4000);
                    }
                };

                xhr.onerror = function() {
                    completados++;
                    alert(`Error de red al subir ${file.name}`);
                    if (completados === files.length) {
                        setTimeout(() => window.location.reload(), 4000);
                    }
                };

                xhr.send(formData);
            });
        });

        function openRenameModal(id, nombre) {
            const modal = document.getElementById('modalRenombrar');
            const form = document.getElementById('formRenombrar');
            const input = document.getElementById('nuevo_nombre_input');

            input.value = nombre.replace(/\.[^/.]+$/, "");
            form.action = `{{ url('/archivos') }}/${id}/renombrar`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openMergeModal(id, nombre) {
            const modal = document.getElementById('modalUnir');
            const inputId = document.getElementById('archivo_principal_id');
            const nuevoNombre = document.getElementById('nuevo_nombre_unido');

            inputId.value = id;
            nuevoNombre.value = nombre.replace(/\.[^/.]+$/, '') + '_unido';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }



        function cerrarModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }


        function confirmarEliminacion(form) {
            event.preventDefault();

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 ml-2",
                    cancelButton: "bg-gray-300 text-black px-4 py-2 rounded-lg font-medium hover:bg-gray-400 mr-2"
                },
                buttonsStyling: false
            });

            swalWithBootstrapButtons.fire({
                title: "¿Estás seguro?",
                text: "¡No podrás revertir esta acción!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar 💣",
                cancelButtonText: "Cancelar 🙈",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    swalWithBootstrapButtons.fire({
                        title: "¡Boom! 💥",
                        text: "El archivo fue eliminado como un pro 😎🧹",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Espera un segundo antes de enviar el formulario
                    setTimeout(() => {
                        form.submit();
                    }, 1500);
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire({
                        title: "Cancelado 🙌",
                        text: "El archivo sigue vivito y coleando.",
                        icon: "error"
                    });
                }
            });

            return false;
        }
    </script>

    <!-- Modal Renombrar -->
    <div id="modalRenombrar" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
        <div class="bg-white dark:bg-neutral-800 rounded-xl p-6 w-full max-w-md shadow-lg space-y-4">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white">✏️ Renombrar Archivo</h3>
            <form method="POST" id="formRenombrar" action="">
                @csrf
                @method('PATCH') {{-- 👈 Esto indica que el método real es PATCH --}}
                <div>
                    <label for="nuevo_nombre_input"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nuevo nombre</label>
                    <input type="text" name="nuevo_nombre" id="nuevo_nombre_input"
                        class="w-full px-4 py-2 rounded-lg border dark:bg-neutral-700 dark:border-neutral-600 dark:text-white focus:ring-2 focus:ring-indigo-500"
                        required>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="cerrarModal('modalRenombrar')"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-neutral-700 dark:hover:bg-neutral-600 rounded-lg text-sm font-medium">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Unir Archivos -->
    <div id="modalUnir" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center z-50">
        <div class="bg-white dark:bg-neutral-800 rounded-xl p-6 w-full max-w-md shadow-lg space-y-4">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white">🔗 Unir Archivos</h3>
            <form method="POST" action="{{ route('files.merge') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="archivo_principal_id" id="archivo_principal_id">

                <div>
                    <label for="nuevo_documento"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Documento nuevo a unir
                        (PDF)</label>
                    <input type="file" name="nuevo_documento" id="nuevo_documento" accept="application/pdf"
                        class="w-full px-4 py-2 rounded-lg border dark:bg-neutral-700 dark:text-white dark:border-neutral-600 focus:ring-2 focus:ring-purple-500"
                        required>
                </div>

                <div>
                    <label for="nuevo_nombre_unido"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nuevo nombre
                        combinado</label>
                    <input type="text" name="nuevo_nombre" id="nuevo_nombre_unido"
                        class="w-full px-4 py-2 rounded-lg border dark:bg-neutral-700 dark:text-white dark:border-neutral-600 focus:ring-2 focus:ring-purple-500"
                        required>
                </div>

                <!-- ✅ Nuevo: checkbox para eliminar archivo original -->
                <div class="flex items-center gap-2 mt-2">
                    <input type="checkbox" name="eliminar_original" id="eliminar_original"
                        class="rounded text-purple-600 focus:ring-purple-500 border-gray-300 dark:bg-neutral-700 dark:border-neutral-600">
                    <label for="eliminar_original" class="text-sm text-gray-700 dark:text-gray-300">
                        Eliminar archivo original después de unir 🧹
                    </label>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button type="button" onclick="cerrarModal('modalUnir')"
                        class="px-4 py-2 bg-gray-300 dark:bg-neutral-600 rounded-lg text-sm font-medium">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium">
                        Unir
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
