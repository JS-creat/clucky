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

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|max:100',
            'subtitulo' => 'nullable|max:150',
            'descripcion' => 'nullable',
            'etiqueta' => 'nullable|max:50',
            'texto_boton' => 'nullable|max:50',
            'url_boton' => 'nullable|url',
            'imagen' => 'required|image|max:2048',
            'orden' => 'integer',
            'estado' => 'boolean'
        ]);

        $data['imagen'] = $request->file('imagen')->store('banners', 'public');

        Banner::create($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner creado correctamente');
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $data = $request->validate([
            'titulo' => 'required|max:100',
            'imagen' => 'nullable|image'
        ]);

        if ($request->hasFile('imagen')) {
            Storage::disk('public')->delete($banner->imagen);
            $data['imagen'] = $request->file('imagen')->store('banners', 'public');
        }

        $banner->update($data);

        return back()->with('success', 'Actualizado');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        Storage::disk('public')->delete($banner->imagen);
        $banner->delete();

        return back()->with('success', 'Eliminado');
    }
}