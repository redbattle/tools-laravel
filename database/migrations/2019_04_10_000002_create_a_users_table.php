<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username', 50)->unique()->comment('登录名');
            $table->string('password', 100)->comment('密码');
            $table->string('nickname', 30)->comment('昵称');
            $table->string('session_key', 60)->nullable()->comment('会话key');
            $table->tinyInteger('status')->default(0)->comment('状态；0禁用，1正常');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['username', 'status', 'session_key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('a_users');
    }
}
