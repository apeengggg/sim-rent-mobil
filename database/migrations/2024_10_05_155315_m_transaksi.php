<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_transaksi', function (Blueprint $table) {
            $table->string('transaksi_id', 100)->primary();
            $table->string('user_id', 100);
            $table->string('mobil_id', 100);
            $table->timestamp('tanggal_mulai');
            $table->timestamp('tanggal_selesai');
            $table->timestamp('tanggal_kembali')->nullable();
            $table->integer('is_return')->default(0);
            $table->integer('total_tarif');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 100);
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 100)->nullable();

            // Foreign key constraint
            $table->foreign('user_id')->references('user_id')->on('m_users')->onDelete('cascade');
            $table->foreign('mobil_id')->references('mobil_id')->on('m_mobils')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('r_transaksi');
    }
};
