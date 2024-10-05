<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MMerekMobilsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_merek_mobils')->insert([
            [
                'merek_mobil_id' => 'f7f35b4b-073c-4d49-82a6-96474fe828f4',
                'merek_mobil' => 'Honda',
                'status' => 1,
                'created_by' => 'SYS',
            ],
            [
                'merek_mobil_id' => 'f39754b9-2b1b-4527-bd2a-7cb472272044',
                'merek_mobil' => 'Hyundai',
                'status' => 1,
                'created_by' => 'SYS',
            ],
            [
                'merek_mobil_id' => '69248ad1-a04e-431c-8237-dd4255a1bc18',
                'merek_mobil' => 'Toyota',
                'status' => 1,
                'created_by' => 'SYS',
            ],
            [
                'merek_mobil_id' => '695dbf3f-a1ca-4bd0-9ee3-f2be2b0ac590',
                'merek_mobil' => 'Suzuki',
                'status' => 1,
                'created_by' => 'SYS',
            ],
            [
                'merek_mobil_id' => '64ea11f2-8a42-456e-8f2b-6797672a1426',
                'merek_mobil' => 'BMW',
                'status' => 1,
                'created_by' => 'SYS',
            ],
        ]);
    }
}
