<?php

use App\Models\Item;
use App\Models\Purchase;
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
        Schema::create('item_purchase', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->foreignIdFor(Item::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Purchase::class)->constrained()->cascadeOnDelete();
            $table->primary(['item_id', 'purchase_id']);
            $table->integer('quantity');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_purchase');
    }
};
