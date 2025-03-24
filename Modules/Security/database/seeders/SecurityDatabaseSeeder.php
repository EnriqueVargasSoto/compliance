<?php

namespace Modules\Security\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Security\Models\Company;
use Modules\Security\Models\Module;
use Modules\Security\Models\Office;
use Modules\Security\Models\Person;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SecurityDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call([]);
        $dashboard = Module::create(['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard']);
        $seguridad = Module::create(['name' => 'Seguridad', 'icon' => 'security']);
        $lote = Module::create(['name' => 'Lotes','route' => 'branch', 'icon' => 'branch']);

        // Módulos dentro de Seguridad
        $modulos = Module::create(['name' => 'Módulos', 'parent_id' => $seguridad->id]);
        $usuarios = Module::create(['name' => 'Usuarios', 'parent_id' => $seguridad->id]);
        $rolesPermisos = Module::create(['name' => 'Roles y Permisos', 'parent_id' => $seguridad->id]);

        // Submódulos de Roles y Permisos
        $roles = Module::create(['name' => 'Roles', 'parent_id' => $rolesPermisos->id]);
        $permisos = Module::create(['name' => 'Permisos', 'parent_id' => $rolesPermisos->id]);

        // Permisos por módulo
        $modulesWithPermissions = [
            'modulos' => $modulos,
            'usuarios' => $usuarios,
            'roles_y_permisos' => $rolesPermisos,
            'roles' => $roles,
            'permisos' => $permisos,
            'lotes' => $lote,
        ];

        foreach ($modulesWithPermissions as $key => $module) {
            $permissions = [
                Permission::create(['name' => "$key.view", 'guard_name' => 'web']),
                Permission::create(['name' => "$key.create", 'guard_name' => 'web']),
                Permission::create(['name' => "$key.edit", 'guard_name' => 'web']),
                Permission::create(['name' => "$key.delete", 'guard_name' => 'web']),
            ];

            // Relacionar permisos con el módulo
            foreach ($permissions as $permission) {
                DB::table('module_has_permissions')->insert([
                    'module_id' => $module->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }

        // Permiso solo de visualización para Dashboard
        $dashboardPermission = Permission::create(['name' => 'dashboard.view', 'guard_name' => 'web']);

        DB::table('module_has_permissions')->insert([
            'module_id' => $dashboard->id,
            'permission_id' => $dashboardPermission->id,
        ]);

        //Crear Empresa
        $empresa = Company::create([
            'name' => 'Empresa de Prueba',
            'ruc' => '10708484216',
            'address' => 'Calle de la empresa',
            'phone' => '1234567890',
            'email' => 'company@gmail.com']);

        //Crear Oficinas
        $oficina1 = Office::create([
            'name' => 'Oficina 1',
            'company_id' => $empresa->id,
            'address' => 'Calle de la oficina 1',
            'phone' => '1234567890',
            'email' => 'oficina1@gmail.com']);

        $oficina2 = Office::create([
            'name' => 'Oficina 2',
            'company_id' => $empresa->id,
            'address' => 'Calle de la oficina 2',
            'phone' => '1234567890',
            'email' => 'oficina2@gmail.com']);

        // ✅ 5️⃣ Crear personas
        $person1 = Person::create(['names' => 'John', 'surnames' => 'Doe', 'dni' => '12345678', 'email' => 'user1@example.com']);
        $person2 = Person::create(['names' => 'Jane', 'surnames' => 'Smith', 'dni' => '87654321', 'email' => 'user2@example.com']);

        // ✅ 6️⃣ Crear usuarios
        $user1 = User::create([
            /* 'name' => 'user1', */
            'email' => 'user1@example.com',
            'password' => Hash::make(123456),
            'person_id' => $person1->id
        ]);

        $user2 = User::create([
            /* 'name' => 'user2', */
            'email' => 'user2@example.com',
            'password' => Hash::make(123456),
            'person_id' => $person2->id
        ]);

        // ✅ 7️⃣ Asignar oficinas a usuarios
        $user1->offices()->attach($oficina1->id);
        $user2->offices()->attach([$oficina1->id, $oficina2->id]);

        // ✅ 2️⃣ Crear roles
        $adminRole = Role::create(['name' => 'Administrator']);
        $analystRole = Role::create(['name' => 'Analyst']);
        $maintenanceRole = Role::create(['name' => 'Maintenance']);

        // ✅ 4️⃣ Asignar permisos a roles
        $adminRole->permissions()->attach([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25]);
        $analystRole->permissions()->attach([5,6,7,8,21,22,23,24,25]);
        $maintenanceRole->permissions()->attach([1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]);

        // ✅ 8️⃣ Asignar roles en oficinas
        $user1->rolesInOffice($oficina1->id)->attach($maintenanceRole->id);
        $user2->rolesInOffice($oficina1->id)->attach($adminRole->id);
        $user2->rolesInOffice($oficina2->id)->attach($analystRole->id);

        // ✅ 9️⃣ Asignar permisos adicionales (Lotes para user1)
        $user1->additionalPermissionsInOffice($oficina1->id)->attach(21);
    }
}
