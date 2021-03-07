<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDialogUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dialog_user', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("dialog_id")->unsigned();
            $table->bigInteger("user_id")->unsigned();
            $table->integer("unread_count")->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("dialog_id")->references("id")->on("dialogs");
            $table->foreign("user_id")->references("id")->on("users");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dialog_user');
    }
}
