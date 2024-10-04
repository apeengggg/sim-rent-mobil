<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MUserDatas extends Migration
{

    public function up()
    {
        Schema::create('m_user_datas', function (Blueprint $table) {
            $table->string('user_data_id', 100)->primary();
            $table->string('no_sim', 100)->unique();
            $table->text('foto_sim');
            $table->string('user_id', 100);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 100);
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 100)->nullable();

            // Foreign key constraint
            $table->foreign('user_id')->references('user_id')->on('m_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_user_datas');
    }
}
