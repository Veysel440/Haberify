<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $perms = [
            'articles.create','articles.update','articles.publish','articles.read_full',
            'comments.moderate','categories.manage','tags.manage','analytics.view'
        ];
        foreach ($perms as $p) { \Spatie\Permission\Models\Permission::firstOrCreate(['name'=>$p]); }

        $roles = [
            'admin' => $perms,
            'editor' => ['articles.update','articles.publish','comments.moderate','articles.read_full'],
            'author' => ['articles.create','articles.update','articles.read_full'],
            'subscriber' => [],
        ];

        foreach ($roles as $r => $rp) {
            $role = \Spatie\Permission\Models\Role::firstOrCreate(['name'=>$r]);
            $role->syncPermissions($rp);
        }

        $admin = \App\Models\User::firstOrCreate(
            ['email'=>'admin@haberify.local'],
            ['name'=>'Admin','password'=>bcrypt('Admin123!')]
        );
        $admin->assignRole('admin');
    }
}
