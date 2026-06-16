<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Mail\ReactivacionClienteMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EnviarCorreosReactivacion extends Command
{
    // Nombre para ejecutar el comando
    protected $signature = 'emails:reactivar-clientes';
    protected $description = 'Busca clientes que llevan 30 días sin comprar y les envía un correo.';

    public function handle()
    {
        // Fecha de hace exactamente 30 días
        $haceUnMes = Carbon::now()->subDays(30)->toDateString();

        // Buscamos usuarios que:
        // 1. Su ÚLTIMO pedido fue hace exactamente 30 días.
        // 2. NO se les ha enviado el correo de reactivación aún (campo null).
        $usuariosInactivos = User::whereHas('pedidos', function ($query) use ($haceUnMes) {
            // Agrupamos por usuario para saber la fecha de su última compra real
            $query->select('id_usuario')
                  ->selectRaw('MAX(fecha_pedido) as ultima_compra')
                  ->groupBy('id_usuario')
                  ->havingRaw('DATE(ultima_compra) = ?', [$haceUnMes]);
        })
        ->whereNull('reactivation_email_sent_at')
        ->get();

        foreach ($usuariosInactivos as $usuario) {
            // Enviamos el correo usando Colas (queue) para que sea rápido
            Mail::to($usuario->correo)->queue(new ReactivacionClienteMail($usuario));

            // Marcamos al usuario para no volver a escribirle mañana
            $usuario->update([
                'reactivation_email_sent_at' => Carbon::now()
            ]);
        }

        $this->info('Proceso terminado. Se enviaron ' . $usuariosInactivos->count() . ' correos.');
    }
}
