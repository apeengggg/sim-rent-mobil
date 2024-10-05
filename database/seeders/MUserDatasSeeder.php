<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MUserDatasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $filePath1 = public_path('creta.jpg');
        $filePath2 = public_path('rush.png');

        if (!file_exists($filePath1)) {
            throw new \Exception("File not found: " . $filePath1);
        }

        if (!file_exists($filePath2)) {
            throw new \Exception("File not found: " . $filePath2);
        }

        // Encode the file to base64
        $base64File1 = "data:image/jpeg;base64,".base64_encode(file_get_contents($filePath1));
        $base64File2 = "data:image/png;base64,".base64_encode(file_get_contents($filePath2));

        DB::table('m_user_datas')->insert([
            [
                'user_data_id' => 'cb0dec9c-83eb-45f6-ba9d-ffa6298d8bd0',
                'no_sim' => '1234567890123521',
                'foto_sim' => $base64File1,
                'user_id' => 'af361200-53be-4e06-b911-41ff2fb98487',
                'created_by' => 'SYS',
            ],
            [
                'user_data_id' => 'cb0dec9c-83eb-45f6-ba9d-ffa6298d8bd1',
                'no_sim' => '1234567890123523',
                'foto_sim' => $base64File2,
                'user_id' => 'f57b21f9-f3d6-4958-b265-82175d160e6k',
                'created_by' => 'SYS',
            ]
        ]);
    }
}
