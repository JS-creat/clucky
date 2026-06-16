<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// PROGRAMACIÓN DE TAREAS (CRON JOBS)

// Tu nuevo comando se ejecutará automáticamente todos los días a las 8:00 AM
Schedule::command('emails:reactivar-clientes')->dailyAt('08:00');
