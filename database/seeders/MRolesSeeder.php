<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('m_roles')->insert([
            [
                'role_id' => 'f57b21f9-f3d6-4958-b265-82175d160e6c',
                'role_name' => 'Super Admin',
                'created_by' => 'SYS',
            ],
            [
                'role_id' => 'd57b21f9-f3d6-4958-b265-82175d160e6c',
                'role_name' => 'User',
                'created_by' => 'SYS',
            ]
        ]);
    }
}
