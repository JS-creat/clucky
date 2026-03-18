<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::where('estado', true)
            ->orderBy('orden', 'asc')
            ->get();

        $banners = $banners->map(function ($banner) {
            return [
                'id' => $banner->id_banner,
                'titulo' => $banner->titulo,
                'subtitulo' => $banner->subtitulo,
                'descripcion' => $banner->descripcion,
                'etiqueta' => $banner->etiqueta,
                'texto_boton' => $banner->texto_boton,
                'url_boton' => $banner->url_boton,
                'imagen' => $banner->imagen 
                    ? url('/api/banner/' . $banner->imagen)  
                    : null,
                'orden' => $banner->orden,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $banners
        ]);
    }
}