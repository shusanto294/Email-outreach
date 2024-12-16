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
        Schema::table('replies', function (Blueprint $table) {
            $table->string('from_name')->nullable()->change();
            $table->string('from_address')->nullable()->change();
            $table->string('to')->nullable()->change();
            $table->text('subject')->nullable()->change();
            $table->longText('body')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('replies', function (Blueprint $table) {
            $table->string('from_name')->nullable(false)->change();
            $table->string('from_address')->nullable(false)->change();
            $table->string('to')->nullable(false)->change();
            $table->text('subject')->nullable(false)->change();
            $table->longText('body')->nullable(false)->change();
        });
    }
};
