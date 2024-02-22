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
        Schema::table('calendar_store_user', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('store_id')->nullable()->change();
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_store_user', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('store_id')->change();
            $table->unsignedBigInteger('user_id')->change();
        });
    }
};
