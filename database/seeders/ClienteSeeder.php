<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Juan',
                'apellido_paterno' => 'Pérez',
                'apellido_materno' => 'García',
                'correo' => 'juan.perez@example.com',
            ],
            [
                'nombre' => 'María',
                'apellido_paterno' => 'González',
                'apellido_materno' => 'López',
                'correo' => 'maria.gonzalez@example.com',
            ],
            [
                'nombre' => 'Carlos',
                'apellido_paterno' => 'Rodríguez',
                'apellido_materno' => 'Martínez',
                'correo' => 'carlos.rodriguez@example.com',
            ],
            [
                'nombre' => 'Ana',
                'apellido_paterno' => 'Fernández',
                'apellido_materno' => 'Sánchez',
                'correo' => 'ana.fernandez@example.com',
            ],
            [
                'nombre' => 'Luis',
                'apellido_paterno' => 'Ramírez',
                'apellido_materno' => 'Torres',
                'correo' => 'luis.ramirez@example.com',
            ],
            [
                'nombre' => 'Elena',
                'apellido_paterno' => 'Vargas',
                'apellido_materno' => 'Castillo',
                'correo' => 'elena.vargas@example.com',
            ],
            [
                'nombre' => 'Miguel',
                'apellido_paterno' => 'Herrera',
                'apellido_materno' => 'Morales',
                'correo' => 'miguel.herrera@example.com',
            ],
            [
                'nombre' => 'Sofia',
                'apellido_paterno' => 'Jiménez',
                'apellido_materno' => 'Ruiz',
                'correo' => 'sofia.jimenez@example.com',
            ],
            [
                'nombre' => 'Diego',
                'apellido_paterno' => 'Mendoza',
                'apellido_materno' => 'Ortega',
                'correo' => 'diego.mendoza@example.com',
            ],
            [
                'nombre' => 'Valentina',
                'apellido_paterno' => 'Castro',
                'apellido_materno' => 'Ramos',
                'correo' => 'valentina.castro@example.com',
            ]
        ];

        foreach ($clientes as $clienteData) {
            Cliente::create($clienteData);
        }

        $this->command->info('Se crearon ' . count($clientes) . ' clientes de ejemplo');
    }
}
