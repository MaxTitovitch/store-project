<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'last_name' => 'Main',
            'sex' => 'Мужчина',
            'date_of_birth' => '2000-01-01',
            'phone' => 293038811,
            'email' => 'maxtitovitch@mail.ru',
            'password' => Hash::make('password'),
            'role' => 'Главный администратор',
        ]);
    }
}
