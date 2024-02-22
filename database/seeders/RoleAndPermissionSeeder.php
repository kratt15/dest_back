<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     *
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $AdminRole = Role::create(['name' => 'admin']);
        $GerantRole = Role::create(['name' => 'gerant']);
        $UserRole = Role::create(['name' => 'user']);

        $Permissions=[
            'customer-list',
            'customer-add',
            'customer-edit',
            'customer-delete',
            'providers-list',
            'providers-add',
            'providers-edit',
            'providers-delete',
            'article-list',
            'article-add',
            'article-edit',
            'article-delete',
            'magasin-list',
            'magasin-add',
            'magasin-edit',
            'magasin-delete',
            'achat-list',
            'achat-add',
            'achat-edit',
            'achat-delete',
            'commande-list',
            'commande-add',
            'commande-edit',
            'commande-delete',
            'payement-list',
            'payement-add',
            'payement-edit',
            'payement-delete',
            'categories-list',
            'categories-add',
            'categories-edit',
            'categories-delete',
            'users-list',
            'users-add',
            'users-edit',
            'users-delete',
            'gerant-list',
            'gerant-add',
            'gerant-edit',
            'gerant-delete',

            'access to everything.*',
        ];

        foreach ($Permissions as $Permission){
            Permission::create([
                'name' => $Permission
            ]);
        }

        $adminRole = Role::where('name', 'admin')->first();

        $permissions = Permission::all();

        $adminRole->givePermissionTo($permissions);

        $gerantRole = Role::where('name', 'gerant')->first();
        $userRole = Role::where('name', 'user')->first();

       // Définir des permissions spécifiques pour le rôle d'éditeur
        $gerantPermissions = [
             'commande-list',
            'commande-add',
            'commande-edit',
            'commande-delete',
            'achat-list',
            'achat-add',
           'achat-edit',
           'achat-delete',
           'article-list',
            'article-add',
            'article-edit',

            'magasin-list',

            'payement-list',
        ];
        $gerantRole->syncPermissions($gerantPermissions);

        // Définir des permissions spécifiques pour le rôle de spectateur
        $userPermissions = [ 'achat-list','commande-list','article-list','magasin-list', 'payement-list','customer-list'];
        $userRole->syncPermissions($userPermissions);


    }

}
