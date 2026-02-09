<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Foro;
use App\Models\Reserva;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear el usuario Administrador (si no existe)
        $admin = User::firstOrCreate(
            ['email' => 'sicom2025@gmail.com'],
            [
                'name' => 'Admin Sicom',
                'password' => Hash::make('sicom2026'),
            ]
        );

        // 2. Crear foros de ejemplo
        $foroTV = Foro::create([
            'nombre' => 'Foro A (Televisión)',
            'descripcion' => 'Equipado con cámaras 4K y set de iluminación.',
        ]);

        $foroRadio = Foro::create([
            'nombre' => 'Foro B (Radio)',
            'descripcion' => 'Consola de audio y 4 micrófonos profesionales.',
        ]);

        // 3. Crear Reservas de prueba
        // Reserva para HOY a las 10:00 AM
        Reserva::create([
            'foro_id' => $foroTV->id,
            'user_id' => $admin->id,
            'start_time' => Carbon::today()->setHour(10)->setMinute(0),
            'end_time' => Carbon::today()->setHour(12)->setMinute(0),
        ]);

        // Reserva para MAÑANA a las 3:00 PM
        Reserva::create([
            'foro_id' => $foroRadio->id,
            'user_id' => $admin->id,
            'start_time' => Carbon::tomorrow()->setHour(15)->setMinute(0),
            'end_time' => Carbon::tomorrow()->setHour(17)->setMinute(0),
        ]);
    }
}