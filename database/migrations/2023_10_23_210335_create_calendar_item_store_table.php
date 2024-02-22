<?php

use App\Models\Calendar;
use App\Models\Item;
use App\Models\Store;
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
        Schema::create('calendar_item_store', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->foreignIdFor(Calendar::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Item::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Store::class)->constrained()->cascadeOnDelete();
            $table->primary(['calendar_id', 'item_id', 'store_id']);
            $table->integer('quantity');
            $table->integer('destination_store');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_item_store', function (Blueprint $table) {
            $table->dropForeignIdFor(Calendar::class)->constrained()->cascadeOnDelete();
            $table->dropForeignIdFor(Item::class)->constrained()->cascadeOnDelete();
            $table->dropForeignIdFor(Store::class)->constrained()->cascadeOnDelete();
            $table->dropPrimary(['calendar_id', 'item_id', 'store_id']);
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('calendar_item_store');
    }
};
