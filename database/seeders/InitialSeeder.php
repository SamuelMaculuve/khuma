<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Companies;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class InitialSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $subscriberRole = Role::firstOrCreate(['name' => 'subscriber']);
        $salesRole = Role::firstOrCreate(['name' => 'salesperson']);

        $adminCompany = Companies::firstOrCreate(
            ['email' => 'admin@kuma.test'],
            [
                'name' => 'Admin Company',
                'tax_number' => '12345678901',
                'address' => 'Rua do Admin, 123',
            ]
        );

        $admin = User::firstOrCreate(
            ['email' => 'admin@kuma.test'],
            [
                'name' => 'Administrador',
                'phone' => '840000000',
                'password' => Hash::make('password'),
                'status' => 'active',
                'company_id' => $adminCompany->id,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        $userCompany = Companies::firstOrCreate(
            ['email' => 'user@kuma.test'],
            [
                'name' => 'User Company',
                'tax_number' => '98765432101',
                'address' => 'Rua do Usuário, 456',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'user@kuma.test'],
            [
                'name' => 'Subscritor Teste',
                'phone' => '850000000',
                'password' => Hash::make('password'),
                'status' => 'pending',
                'company_id' => $userCompany->id, 
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole($subscriberRole);

        $this->command->info('Admin (admin@kuma.test / password) criado com empresa');
        $this->command->info('User (user@kuma.test / password) criado com empresa');
    }
}