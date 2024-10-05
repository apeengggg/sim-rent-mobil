<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('m_users')->insert([
            [
                'user_id' => 'f57b21f9-f3d6-4958-b265-82175d160e6k',
                'role_id' => 'f57b21f9-f3d6-4958-b265-82175d160e6c',
                'nama' => 'Superadmin',
                'username' => 'admin',
                'password' => bcrypt('admin123'),
                'alamat' => 'Jl. Manggis 1',
                'telepon' => '081321876456',
                'status' => 1,
                'created_by' => 'SYS',
            ],
            [
                'user_id' => 'af361200-53be-4e06-b911-41ff2fb98487',
                'role_id' => 'd57b21f9-f3d6-4958-b265-82175d160e6c',
                'nama' => 'User',
                'username' => 'user',
                'password' => bcrypt('user1234'),
                'alamat' => 'Jl. Manggis 3',
                'telepon' => '081321876451',
                'status' => 1,
                'created_by' => 'SYS',
            ]
        ]);
    }
}
