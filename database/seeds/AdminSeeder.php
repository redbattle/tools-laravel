<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('a_users')->insert([
            [
                'username' => 'root',
                'password' => '$2y$10$50XDO4rIj9iBde0QT1RN..D79QDoATpp7DsAPmMYndvv8P3Gwqs3e',
                'nickname' => 'Developer',
                'status' => '1',
            ],
        ]);

    }
}
