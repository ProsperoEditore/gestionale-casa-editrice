<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'prosperoeditore@gmail.com'],
            [
                'name' => 'SuperAdmin',
                'password' => Hash::make('permesivatralaperdutagenteIII1'), 
                'ruolo' => 'admin'
            ]
        );
    }
}
