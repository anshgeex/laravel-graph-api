<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_listing', function (Blueprint $table) {
            $table->id(); // Auto-incremental primary key
            $table->text('mail_id');
            $table->text('createdDateTime');
            $table->text('changeKey');
            $table->text('receivedDateTime');
            $table->text('subject');
            $table->text('bodyPreview');
            $table->text('webLink');
            $table->text('content');
            $table->text('sentDateTime');
            // Add other fields as needed

            $table->timestamps(); // Created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mail_listing');
    }
}
