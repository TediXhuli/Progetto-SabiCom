<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->create([
            'name'=> 'Tedi',
            'email'=> 'tedi.xhuli@email.com',
            'password'=> 'Qwerty'
        ]);
        User::query()->create([
            'name'=> 'Teddy',
            'email'=> 'Teddy.xhuli@email.com',
            'password'=> 'abcdefgh'
        ]);
    }
}
