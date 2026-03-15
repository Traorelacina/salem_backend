<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si l'admin existe déjà
        $admin = User::where('email', 'admin@salem-technology.com')->first();

        if (!$admin) {
            User::create([
                'name' => 'Administrateur',
                'email' => 'admin@salem-technology.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]);

            $this->command->info('✅ Admin user created successfully!');
        } else {
            $this->command->info('⚠️ Admin user already exists!');
        }

        // Optionnel : créer un deuxième admin pour test
        $testAdmin = User::where('email', 'test@salem-technology.com')->first();

        if (!$testAdmin) {
            User::create([
                'name' => 'Test Admin',
                'email' => 'test@salem-technology.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            $this->command->info('✅ Test admin user created successfully!');
        }
    }
}