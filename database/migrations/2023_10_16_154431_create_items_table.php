<?php

use App\Models\Brands;
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
        Schema::create('items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name');
            $table->string('reference')->nullable();
            // $table->string('expiration_date');
            $table->string('cost');
            $table->string('price');
            $table->foreignIdFor(Category::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Provider::class)->constrained()->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeignIdFor(Category::class);
            $table->dropForeignIdFor(Provider::class);
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('items');
    }
};
