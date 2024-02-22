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
            $table->softDeletes();
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('providers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('customer', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
