<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("message_id")->unsigned();
            $table->string("file_name");
            $table->string("hash");
            $table->boolean("is_uploaded");
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("message_id")->references("id")->on("messages");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
