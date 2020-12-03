<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@waswar.my',
            'password' => Hash::make('@dminP@sswd'),
            'verified' => 1,
            'email_verified_at' => date("Y-m-d H:i:s"),
        ]);
        
        $role = 'Admin';

        $attach_role = Role::where('name', $role)->get('id')->first();

        $user->roles()->attach($attach_role);

        $profile = new Profile([
            'image' => '/default/avatar.png'
        ]);

        $user->profile()->save($profile);
    }
}
