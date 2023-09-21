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
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('linkedin_profile')->nullable();
            $table->string('title')->nullable();
            $table->string('company');
            $table->string('company_website')->nullable();
            $table->string('location')->nullable();
            $table->string('email');
            $table->boolean('leadlist_id')->default(0);
            $table->text('personalized_line')->nullable();
            $table->text('comment')->nullable();
            $table->string('verified')->nullable();
            $table->boolean('subscribe')->default(1);
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
        Schema::dropIfExists('leads');
    }
};
