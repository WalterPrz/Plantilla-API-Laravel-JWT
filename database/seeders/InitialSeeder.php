<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\TipoPermiso;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InitialSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('permiso_rol')->truncate();
        DB::table('rol_user')->truncate();
        DB::table('rols')->truncate();
        DB::table('users')->truncate();
        DB::table('permisos')->truncate();
        DB::table('tipo_permisos')->truncate();

        $tipo_permisos = [
            [
                'nombre' => "BACKEND",
                'permisos' => [
                    ['nombre' => "UPDATE_USUARIO"],
                    ['nombre' => "CREATE_USUARIO"],
                    ['nombre' => "DESTROY_USUARIO"],
                    ['nombre' => "LIST_USUARIO"],
                    ['nombre' => "CREATE_TIPO_PERMISO"],
                    ['nombre' => "UPDATE_TIPO_PERMISO"],
                    ['nombre' => "DESTROY_TIPO_PERMISO"],
                    ['nombre' => "LIST_TIPO_PERMISO"],

                    ['nombre' => "CREATE_ROL"],
                    ['nombre' => "UPDATE_ROL"],
                    ['nombre' => "DESTROY_ROL"],
                    ['nombre' => "LIST_ROL"],

                    ['nombre' => "CREATE_PERMISO"],
                    ['nombre' => "UPDATE_PERMISO"],
                    ['nombre' => "DESTROY_PERMISO"],
                    ['nombre' => "LIST_PERMISO"],

                    ['nombre' => "UPDATE_PASSWORD_USER"],
                    ['nombre' => "SHOW_INFO_USER"],
                    ['nombre' => "UPDATE_INFO_USER"],

                    ['nombre' => "PERMISO_ROL_UPDATE"],
                    ['nombre' => "CREATE_USUARIO"],
                    ['nombre' => "SHOW_USUARIO"],
                    ['nombre' => "SHOW_ROL"],
                ]
            ],
            ['nombre' => "FRONTEND", 'permisos' => []],
            ['nombre' => "FEATURE", 'permisos' => []],
        ];
        foreach ($tipo_permisos as $data) {
            $modelo = TipoPermiso::create(['nombre' => $data['nombre']]);
            $modelo->permisos()->createMany($data['permisos']);
        }
        $rols = [
            ['nombre' => "ADMIN", "array_permisos" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 20, 21, 22, 23]],
            ['nombre' => "BASICO", "array_permisos" => [17, 18, 19]],
            ['nombre' => "EMPLEADO", "array_permisos" => []],
        ];
        foreach ($rols as $data) {
            $modelo = Rol::create(['nombre' => $data['nombre']]);
            $modelo->permisos()->attach($data['array_permisos']);
        }
        $user = User::create(
            [
                'name' => "admin",
                'email' => env('EMAIL_ADMIN', 'admin@example.com'),
                'active' => true,
                'password' => Hash::make(env('PASSWORD_ADMIN', 'admin')),
            ]
        );
        $user->rols()->attach([1, 2]);
    }
}
