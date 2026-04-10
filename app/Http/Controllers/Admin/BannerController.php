<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('orden')->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    /*public function create()
    {
        return view('admin.banners.create');
    }*/

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'      => 'required|max:100',
            'subtitulo'   => 'nullable|max:150',
            'descripcion' => 'nullable',
            'etiqueta'    => 'nullable|max:50',
            'texto_boton' => 'nullable|max:50',
            'url_boton'   => 'nullable',
            'imagen'      => 'required|image|max:2048',
            'orden'       => 'integer',
            'estado'      => 'boolean',
        ]);

        $archivo = time() . '_' . $request->file('imagen')->getClientOriginalName();
        $request->file('imagen')->move(public_path('banners'), $archivo);
        $data['imagen'] = $archivo; // solo guarda el nombre, ej: "1234_foto.jpg"

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner creado correctamente');
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $data = $request->validate([
            'titulo'      => 'required|max:100',
            'subtitulo'   => 'nullable|max:150',
            'descripcion' => 'nullable',
            'etiqueta'    => 'nullable|max:50',
            'texto_boton' => 'nullable|max:50',
            'url_boton'   => 'nullable',
            'orden'       => 'integer',
            'estado'      => 'boolean',
            'imagen'      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            // Elimina la imagen anterior si existe
            $rutaAnterior = public_path('banners/' . $banner->imagen);
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }

            $archivo = time() . '_' . $request->file('imagen')->getClientOriginalName();
            $request->file('imagen')->move(public_path('banners'), $archivo);
            $data['imagen'] = $archivo;
        }

        $banner->update($data);

        return back()->with('success', 'Banner actualizado correctamente');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        $ruta = public_path('banners/' . $banner->imagen);
        if (file_exists($ruta)) {
            unlink($ruta);
        }

        $banner->delete();
        return back()->with('success', 'Banner eliminado');
    }

    public function toggle(Banner $banner)
    {
        $banner->update(['estado' => !$banner->estado]);
        return back()->with('success', 'Estado del banner actualizado.');
    }
}
