<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nickname', 30)->nullable()->comment('昵称');
            $table->string('avatar', 190)->nullable()->comment('头像');
            $table->string('password', 100)->comment('密码');
            $table->string('session_key', 60)->nullable()->comment('接口凭证');
            $table->tinyInteger('status')->default(0)->comment('状态；0禁用，1正常');
            $table->timestamps();
            $table->softDeletes();
            $table->index([
                'session_key',
                'status',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_users');
    }
}
