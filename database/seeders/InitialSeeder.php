<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InitialSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $subscriberRole = Role::firstOrCreate(['name' => 'subscriber']);

        // Admin
        $admin = User::firstOrCreate(
                    ['email' => 'admin@kuma.test'],
            [
                'name' => 'Administrador',
                'phone' => '840000000',
                'password' => Hash::make('password'),
                'status' => 'active'
            ]
        );
        $admin->assignRole($adminRole);

        // Subscritor teste
        $user = User::firstOrCreate(
            ['email' => 'user@kuma.test'],
            [
                'name' => 'Subscritor Teste',
                'phone' => '850000000',
                'password' => Hash::make('password'),
                'status' => 'pending'
            ]
        );
        $user->assignRole($subscriberRole);

        $this->command->info('✅ Admin (admin@kuma.test / password) criado');
    }
}


