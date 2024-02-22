<?php

use App\Models\Category;
use App\Models\Provider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            //   $table->foreign('category_id')->nullable()->change();
            // $table->foreign('provider_id')->nullable()->change();
            $table->unsignedBigInteger('category_id')->nullable()->change();
            $table->unsignedBigInteger('provider_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            //
            // $table->foreign('category_id')->change();
            // $table->foreign('provider_id')->change();

            $table->unsignedBigInteger('category_id')->change();
            $table->unsignedBigInteger('provider_id')->change();
        });
    }
};
