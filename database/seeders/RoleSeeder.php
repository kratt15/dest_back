<?php

// namespace Database\Seeders;

// use App\Enums\RoleEnum;
// use Illuminate\Database\Seeder;
// use Spatie\Permission\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Spatie\Permission\Models\Permission;

// class RoleSeeder extends Seeder
// {
//     /**
//      * Run the database seeds.
//      */
//     public function run(): void
//     {


//         foreach (RoleEnum::cases() as $roleEnum) {
//             Role::create([
//                 'name' => $roleEnum->value
//             ]);
//         }
//         (Permission::create([

//             'name' => 'access to everything.*',

//         ]))->assgnRole(
//             Role::firstWhere('name', RoleEnum::ADMIN->value)
//         );
//     }
// }
