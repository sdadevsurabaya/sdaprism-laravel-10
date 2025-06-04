<?php
namespace Database\Seeders;

use App\Models\Roles;
use App\Models\RolesUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Role jika belum ada
        $adminRole = Roles::firstOrCreate(['name' => 'admin']);
        $staffRole = Roles::firstOrCreate(['name' => 'staff']);

        // Buat Users
        $users = [
            [
                'name'     => 'admin',
                'email'    => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role'     => $adminRole,
            ],
            [
                'name'     => 'Agus Sudiyento',
                'email'    => 'Agus@sda.com',
                'password' => Hash::make('adminSDA123'),
                'role'     => $adminRole,
            ],
            [
                'name'     => 'staff',
                'email'    => 'staff@gmail.com',
                'password' => Hash::make('staffSDA123'),
                'role'     => $staffRole,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate([
                'email' => $userData['email'],
            ], [
                'name'     => $userData['name'],
                'password' => $userData['password'],
            ]);

            // Hubungkan dengan role lewat pivot user_roles
            RolesUser::updateOrInsert(
                ['users_id' => $user->id],
                ['roles_id' => $userData['role']->id]
            );
        }
    }
}
