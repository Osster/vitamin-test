<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_1_id')->unsigned();
            $table->bigInteger('user_2_id')->unsigned();
            $table->bigInteger('dialog_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("user_1_id")->references("id")->on("users");
            $table->foreign("user_2_id")->references("id")->on("users");
            $table->foreign("dialog_id")->references("id")->on("dialogs");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
