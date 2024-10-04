<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MUsers extends Migration
{

    public function up()
    {
        Schema::create('m_users', function (Blueprint $table) {
            $table->string('user_id', 100)->primary();
            $table->string('role_id', 100);
            $table->string('nama', 100);
            $table->string('username', 10)->unique();
            $table->string('password', 255);
            $table->text('alamat');
            $table->string('telepon', 15);
            $table->string('no_sim', 20);
            $table->text('sim');
            $table->integer('status')->default(1);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 100);
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 100)->nullable();

            // Foreign key constraint
            $table->foreign('role_id')->references('role_id')->on('m_roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_users');
    }
}
