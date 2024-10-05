<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MFasilitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_fasilitas')->insert([
            [
                'fasilitas_id' => 1,
                'fasilitas' => 'Ac',
                'created_by' => 'SYS',
            ],
            [
                'fasilitas_id' => 2,
                'fasilitas' => 'Radio',
                'created_by' => 'SYS',
            ],
            [
                'fasilitas_id' => 3,
                'fasilitas' => 'Sunroof',
                'created_by' => 'SYS',
            ],
            [
                'fasilitas_id' => 4,
                'fasilitas' => 'Android',
                'created_by' => 'SYS',
            ],
            [
                'fasilitas_id' => 5,
                'fasilitas' => 'P3K',
                'created_by' => 'SYS',
            ],
            [
                'fasilitas_id' => 6,
                'fasilitas' => 'Ban Serep',
                'created_by' => 'SYS',
            ],
            [
                'fasilitas_id' => 7,
                'fasilitas' => 'Dongkrak',
                'created_by' => 'SYS',
            ],
            [
                'fasilitas_id' => 8,
                'fasilitas' => 'Segitiga',
                'created_by' => 'SYS',
            ],
        ]);
    }
}
