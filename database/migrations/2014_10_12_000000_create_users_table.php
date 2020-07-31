<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('del_flg')->default(0);
            $table->timestamps();
        });

        DB::table('users')->insert([
            ['name' => 'admin', 'age' => 20, 'email' => 'hoangnguyenit98@gmail.com', 'password' => '$2y$12$JfJzUcbyUnJEQHTm3yJIDu4JLEE.gXfKBA8T6QvYCwsIsI/FxTksG'],
            ['name' => 'user', 'age' => 20, 'email' => 'hoangnd.it98@gmail.com', 'password' => '$2y$12$JfJzUcbyUnJEQHTm3yJIDu4JLEE.gXfKBA8T6QvYCwsIsI/FxTksG'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
