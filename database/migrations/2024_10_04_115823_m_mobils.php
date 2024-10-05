<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MMobils extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_mobils', function (Blueprint $table) {
            $table->string('mobil_id', 100)->primary();
            $table->string('merek_mobil_id', 100);
            $table->string('model', 100);
            $table->string('no_plat', 12);
            $table->string('warna', 12);
            $table->text('description');
            $table->integer('tarif');
            $table->integer('status');
            $table->longtext('foto');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 100);
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_mobils');
    }
};
