<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class CreateLender extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!User::whereEmail('lenderuser@gmail.com')->value('id')){
            $user = User::create([
                'first_name' => 'Lender',
                'last_name' => 'User',
                'email' => 'lenderuser@gmail.com',
                'role' => 'lender',
                'password' => Hash::make('Lender100%'),
            ]);
            $user->createToken('auth_token', [$user->role])->plainTextToken;
        }
    }
}
