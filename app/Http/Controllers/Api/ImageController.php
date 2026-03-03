<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function show($filename)
    {
        // Validar que sea un archivo de imagen
        if (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $filename)) {
            abort(404);
        }
        
        $path = public_path('productos/' . $filename);
        
        if (!file_exists($path)) {
            abort(404, 'Imagen no encontrada');
        }
        
        // Laravel ya aplica CORS automáticamente por el middleware
        return response()->file($path, [
            'Content-Type' => mime_content_type($path),
        ]);
    }
}