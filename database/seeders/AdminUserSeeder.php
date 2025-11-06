<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'moh@gmail.com'],
            [
                'name' => 'Mohammed',
                'password' => Hash::make('22**##'),
                'is_admin' => true,
            ]
        );
    }
}
