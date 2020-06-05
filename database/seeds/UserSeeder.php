<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->name = 'Admin';
        $user->last_name = 'Main';
        $user->sex = 'Мужчина';
        $user->date_of_birth = '2000-01-01';
        $user->phone = 293038811;
        $user->email = env('MAIL_USERNAME');
        $user->password = Hash::make(env('MAIL_PASSWORD'));
        $user->role = 'Главный администратор';
        $user->save();
    }
}
