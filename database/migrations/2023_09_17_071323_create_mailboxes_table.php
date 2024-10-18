<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mailboxes', function (Blueprint $table) {
            $table->id();
            $table->string('mail_from_name');
            $table->string('mail_username');
            $table->string('mail_password');
            $table->string('mail_smtp_host');
            $table->string('mail_imap_host');
            $table->string('mail_smtp_port');
            $table->string('mail_imap_port');
            $table->string('status')->default('off');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mailboxes');
    }
};
