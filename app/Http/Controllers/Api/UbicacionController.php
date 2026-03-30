<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;
use App\Models\TipoDocumento;
use Illuminate\Http\JsonResponse;

class UbicacionController extends Controller
{
    public function tiposDocumento()
    {
        return response()->json(
            TipoDocumento::orderBy('nombre_tipo_documento')
                ->get(['id_tipo_documento', 'nombre_tipo_documento'])
        );
    }

    public function departamentos(): JsonResponse
    {
        $departamentos = Departamento::orderBy('nombre_departamento')
            ->get(['id_departamento', 'nombre_departamento']);

        return response()->json([
            'success' => true,
            'data' => $departamentos
        ]);
    }

    public function provincias($idDepartamento): JsonResponse
    {
        $provincias = Provincia::where('id_departamento', $idDepartamento)
            ->orderBy('nombre_provincia')
            ->get(['id_provincia', 'nombre_provincia']);

        return response()->json([
            'success' => true,
            'data' => $provincias
        ]);
    }

    public function distritos($idProvincia): JsonResponse
    {
        $distritos = Distrito::where('id_provincia', $idProvincia)
            ->orderBy('nombre_distrito')
            ->get(['id_distrito', 'nombre_distrito']);

        return response()->json([
            'success' => true,
            'data' => $distritos
        ]);
    }
}