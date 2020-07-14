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
            $table->string('age');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('del_flg')->default(0);
            $table->timestamps();
        });

        DB::table('users')->insert([
            ['name' => 'admin', 'age' => '20', 'email' => 'admin@gmail.com', 'password' => '$2y$10$o27VhrkdSnaA5T8mbwKswOul9oej2S9y5pA5XS2d5Sw75u7bryT0G'],
            ['name' => 'user', 'age' => '20', 'email' => 'user@gmail.com', 'password' => '$2y$10$o27VhrkdSnaA5T8mbwKswOul9oej2S9y5pA5XS2d5Sw75u7bryT0G'],
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
