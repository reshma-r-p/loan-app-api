<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateAdmin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!User::whereEmail('adminuser@gmail.com')->value('id')){
            $user = User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'adminuser@gmail.com',
                'role' => 'admin',
                'password' => Hash::make('Admin100%'),
            ]);
            $user->createToken('auth_token', [$user->role])->plainTextToken;
        }
    }
}
