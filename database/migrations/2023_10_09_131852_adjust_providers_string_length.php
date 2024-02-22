<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('providers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('name_provider',70)->change();
            $table->string('name_resp',70)->change();
            $table->string('address_provider',200)->change();
            $table->string('phone_provider',20)->change();
            $table->string('email_provider',45)->change();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('providers', function (Blueprint $table) {

            $table->string('name_provider',255)->change();
            $table->string('name_resp',255)->change();
            $table->string('address_provider',255)->change();
            $table->string('phone_provider',255)->change();
            $table->string('email_provider',255)->change();

        });
    }
};
