<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cupon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CuponController extends Controller
{
    /**
     * Listado paginado de cupones con estadísticas
     */
    public function index()
    {
        $cupones = Cupon::withCount('usuariosAsignados')
            ->orderBy('fecha_vencimiento', 'desc')
            ->paginate(10);

        // Estadísticas para las cards (count en toda la tabla, no solo paginados)
        $estadisticas = [
            'total' => Cupon::count(),
            'activos' => Cupon::where('estado_cupon', true)
                ->whereDate('fecha_vencimiento', '>=', now())
                ->count(),
            'vencidos' => Cupon::whereDate('fecha_vencimiento', '<', now())->count(),
            'inactivos' => Cupon::where('estado_cupon', false)->count(),
        ];

        // Lista de usuarios para el selector de asignación
        $usuarios = User::orderBy('nombres', 'asc')
            ->select('id_usuario', 'nombres', 'correo')
            ->get();

        return view('admin.cupones.index', compact('cupones', 'estadisticas', 'usuarios'));
    }

    /**
     * Almacenar nuevo cupón
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->reglasValidacion(), $this->mensajesValidacion());

        DB::transaction(function () use ($data) {
            $cupon = Cupon::create([
                'codigo_cupon' => strtoupper(trim($data['codigo_cupon'])),
                'descripcion' => $data['descripcion'] ?? null,
                'tipo_descuento' => $data['tipo_descuento'],
                'valor_descuento' => $data['valor_descuento'],
                'monto_compra_minima' => $data['monto_compra_minima'],
                'fecha_vencimiento' => $data['fecha_vencimiento'],
                'estado_cupon' => true,
                'uso_maximo_global' => $data['uso_maximo_global'] ?? null,
                'uso_maximo_por_usuario' => $data['uso_maximo_por_usuario'] ?? null,
                'usos_actuales' => 0,
            ]);

            // Asignar usuarios si es privado
            if ($data['tipo_asignacion'] === 'usuarios' && !empty($data['usuarios_asignados'])) {
                $cupon->asignarUsuarios($data['usuarios_asignados']);
            }
        });

        return redirect()
            ->route('admin.cupones.index')
            ->with('success', 'Cupón "' . strtoupper(trim($data['codigo_cupon'])) . '" creado exitosamente.');
    }

    /**
     * Actualizar cupón existente
     */
    public function update(Request $request, $id)
    {
        $cupon = Cupon::findOrFail($id);

        $data = $request->validate(
            $this->reglasValidacion($cupon->id_cupon),
            $this->mensajesValidacion()
        );

        DB::transaction(function () use ($cupon, $data) {
            $cupon->update([
                'codigo_cupon' => strtoupper(trim($data['codigo_cupon'])),
                'descripcion' => $data['descripcion'] ?? null,
                'tipo_descuento' => $data['tipo_descuento'],
                'valor_descuento' => $data['valor_descuento'],
                'monto_compra_minima' => $data['monto_compra_minima'],
                'fecha_vencimiento' => $data['fecha_vencimiento'],
                'uso_maximo_global' => $data['uso_maximo_global'] ?? null,
                'uso_maximo_por_usuario' => $data['uso_maximo_por_usuario'] ?? null,
            ]);

            // Sincronizar asignaciones
            if ($data['tipo_asignacion'] === 'todos') {
                $cupon->desasignarUsuarios();
            } elseif ($data['tipo_asignacion'] === 'usuarios') {
                $cupon->asignarUsuarios($data['usuarios_asignados'] ?? []);
            }
        });

        return redirect()
            ->route('admin.cupones.index')
            ->with('success', 'Cupón actualizado exitosamente.');
    }

    /**
     * Activar / Desactivar cupón
     */
    public function toggle($id)
    {
        $cupon = Cupon::findOrFail($id);
        $cupon->update(['estado_cupon' => !$cupon->estado_cupon]);

        $estado = $cupon->estado_cupon ? 'activado' : 'desactivado';
        return back()->with('success', 'Cupón ' . $estado . ' exitosamente.');
    }

    /**
     * Eliminar cupón
     */
    public function destroy($id)
    {
        $cupon = Cupon::findOrFail($id);
        $codigo = $cupon->codigo_cupon;

        $cupon->delete();

        return back()->with('success', 'Cupón "' . $codigo . '" eliminado permanentemente.');
    }

    // ============================================
    // MÉTODOS PRIVADOS
    // ============================================

    /**
     * Reglas de validación centralizadas
     */
    private function reglasValidacion(?int $idCuponExcluir = null): array
    {
        $reglaUnico = 'required|string|max:50|regex:/^[A-Z0-9_\\-]+$/i';
        if ($idCuponExcluir) {
            $reglaUnico .= '|unique:cupones,codigo_cupon,' . $idCuponExcluir . ',id_cupon';
        } else {
            $reglaUnico .= '|unique:cupones';
        }

        return [
            'codigo_cupon' => $reglaUnico,
            'descripcion' => 'nullable|string|max:255',
            'tipo_descuento' => 'required|in:porcentaje,monto_fijo',
            'valor_descuento' => 'required|numeric|min:0.01',
            'monto_compra_minima' => 'required|numeric|min:0',
            'fecha_vencimiento' => 'required|date',
            'uso_maximo_global' => 'nullable|integer|min:1',
            'uso_maximo_por_usuario' => 'nullable|integer|min:1',
            'tipo_asignacion' => 'required|in:todos,usuarios',
            'usuarios_asignados' => 'required_if:tipo_asignacion,usuarios|array',
            'usuarios_asignados.*' => 'exists:usuarios,id_usuario',
        ];
    }

    /**
     * Mensajes de validación personalizados
     */
    private function mensajesValidacion(): array
    {
        return [
            'codigo_cupon.required' => 'El código del cupón es obligatorio.',
            'codigo_cupon.unique' => 'Este código de cupón ya existe.',
            'codigo_cupon.regex' => 'El código solo puede contener letras, números, guiones y guiones bajos.',
            'codigo_cupon.max' => 'El código no puede tener más de 50 caracteres.',
            'valor_descuento.required' => 'El valor del descuento es obligatorio.',
            'valor_descuento.min' => 'El descuento debe ser mayor a 0.',
            'monto_compra_minima.required' => 'El monto mínimo de compra es obligatorio.',
            'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria.',
            'tipo_asignacion.required' => 'Debes seleccionar el tipo de asignación.',
            'usuarios_asignados.required_if' => 'Debes seleccionar al menos un usuario.',
        ];
    }
}
