<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('uid')->comment('用户ID');
            $table->string('username', 60)->comment('登录名；邮箱手机号');
            $table->string('mode', 10)->comment('账号类型');
            $table->timestamps();
            $table->softDeletes();
            $table->index([
                'uid',
                'username',
                'mode',
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
        Schema::dropIfExists('c_accounts');
    }
}
