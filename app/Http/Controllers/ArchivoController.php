<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Archivo;

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
}
