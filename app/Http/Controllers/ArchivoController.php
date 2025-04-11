<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Archivo;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PdfReader;

class ArchivoController extends Controller
{
    public function index(Request $request)
    {
        $query = Archivo::with('usuario');

        // Filtro por nombre
        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // Filtro por fecha
        if ($request->filled('fecha')) {
            $query->whereDate('subido_en', $request->fecha);
        }

        // Paginación eficiente
        $archivos = $query->latest()->paginate(10)->appends($request->query());

        // Para respuestas JSON (por ejemplo: API o AJAX)
        if ($request->expectsJson() || $request->header('Accept') === 'application/json') {
            $archivos = Archivo::with('usuario')->latest()->get();
            return response()->json(['archivos' => $archivos]);
        }

        // Vista tradicional Blade
        return view('files.index', compact('archivos'));

        // Para la vista Blade tradicional
        $archivos = Archivo::with('usuario')->latest()->get();
        return view('files.index', compact('archivos'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'files.*' => 'required|file|max:2097152' // Máx. 10MB por archivo
        ]);

        $archivosSubidos = [];

        foreach ($request->file('files') as $file) {
            $nombreOriginal = $file->getClientOriginalName();

            // Validar si ya existe el mismo nombre en la base de datos
            $existe = Archivo::where('nombre', $nombreOriginal)->exists();

            if ($existe) {
                $archivosSubidos[] = [
                    'success' => false,
                    'message' => "El archivo '{$nombreOriginal}' ya fue subido previamente."
                ];
                continue;
            }

            // Guardar con nombre seguro (timestamp)
            $nombreFinal = time() . '_' . preg_replace('/\s+/', '_', $nombreOriginal);
            $ruta = $file->storeAs('expedientes', $nombreFinal, 'public');

            $archivo = Archivo::create([
                'nombre' => $nombreOriginal,
                'ruta' => $ruta,
                'tipo' => $file->getMimeType(),
                'tamano' => $file->getSize(),
                'user_id' => Auth::id(),
                'subido_en' => now()
            ]);

            $archivo->load('usuario');

            $archivosSubidos[] = [
                'success' => true,
                'archivo' => $archivo
            ];
        }

        return response()->json($archivosSubidos);
    }

    // Metodo para eliminar un archivo 
    public function destroy(Archivo $archivo)
    {

        // Verificar si el archivo existe antes de eliminarlo
        if (Storage::disk('public')->exists($archivo->ruta)) {
            Storage::disk('public')->delete($archivo->ruta);
        }

        try {
            $archivo->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el archivo de la base de datos.'], 500);
        }

        if (request()->expectsJson()) {
            return response()->json(['success' => 'Archivo eliminado correctamente.']);
        }

        return redirect()->route('files.index')->with('success', 'Archivo eliminado correctamente.');
    }


    public function unir(Request $request)
    {
        $request->validate([
            'archivo_principal_id' => 'required|exists:archivos,id',
            'nuevo_documento' => 'required|mimes:pdf|max:20480', // máx 20MB
            'nuevo_nombre' => 'required|string|max:255',
        ]);

        $fpdi = new Fpdi();

        // 📁 1. Agregar el archivo ya existente
        $archivoBase = Archivo::findOrFail($request->archivo_principal_id);
        $rutaBase = storage_path('app/public/' . $archivoBase->ruta);

        try {
            $pageCount = $fpdi->setSourceFile($rutaBase);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($tpl);
                $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error con el archivo base: ' . $e->getMessage()]);
        }

        // 📤 2. Subir nuevo documento y agregarlo al PDF
        $archivoNuevo = $request->file('nuevo_documento');
        $nombreOriginal = $archivoNuevo->getClientOriginalName();
        $rutaTemp = $archivoNuevo->storeAs('temp', uniqid() . '_' . $nombreOriginal, 'public');

        $rutaTempCompleta = storage_path('app/public/' . $rutaTemp);

        try {
            $pageCount = $fpdi->setSourceFile($rutaTempCompleta);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($tpl);
                $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $fpdi->useTemplate($tpl);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error con el archivo nuevo: ' . $e->getMessage()]);
        }

        // 🧾 3. Guardar nuevo archivo fusionado
        $nuevoNombre = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nuevo_nombre) . '.pdf';
        $rutaFinal = "expedientes/{$nuevoNombre}";

        try {
            $pdfOutput = $fpdi->Output('S');
            Storage::disk('public')->put($rutaFinal, $pdfOutput);

            // Guardar en la base de datos
            Archivo::create([
                'nombre' => $nuevoNombre,
                'ruta' => $rutaFinal,
                'tipo' => 'application/pdf',
                'tamano' => Storage::disk('public')->size($rutaFinal),
                'user_id' => Auth::id(),
                'subido_en' => now(),
            ]);

            // Limpieza
            Storage::delete('public/' . $rutaTemp);
            return redirect()->route('files.index')->with('success', '✅ Archivos unidos correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al generar el PDF combinado: ' . $e->getMessage()]);
        }
    }


    public function renombrar(Request $request, Archivo $archivo)
    {
        $request->validate([
            'nuevo_nombre' => 'required|string|max:255'
        ]);

        // 1. Obtener extensión original
        $extension = pathinfo($archivo->nombre, PATHINFO_EXTENSION);

        // 2. Sanitizar nombre y agregar extensión
        $nuevoNombre = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->nuevo_nombre) . '.' . $extension;

        // 3. Construir ruta nueva
        $nuevaRuta = 'expedientes/' . $nuevoNombre;

        // 4. Mover el archivo en el sistema de archivos
        if (Storage::disk('public')->exists($archivo->ruta)) {
            Storage::disk('public')->move($archivo->ruta, $nuevaRuta);
        } else {
            return redirect()->back()->withErrors(['error' => 'El archivo original no existe en el sistema de archivos.']);
        }

        // 5. Obtener tipo MIME actualizado
        $tipoMime = mime_content_type(storage_path('app/public/' . $nuevaRuta));

        // Verificar si el tipo MIME es válido
        if (!$tipoMime) {
            return redirect()->back()->withErrors(['error' => 'No se pudo determinar el tipo MIME del archivo renombrado.']);
        }

        // 6. Actualizar en la base de datos
        $archivo->update([
            'nombre' => $nuevoNombre,
            'ruta' => $nuevaRuta,
            'tipo' => $tipoMime,
        ]);

        return redirect()->route('files.index')->with('success', '✅ Archivo renombrado correctamente.');
    }
}
