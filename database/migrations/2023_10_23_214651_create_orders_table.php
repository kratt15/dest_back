<?php

use App\Models\Provider;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->dateTime('issue_date');
            $table->date('reception_date')->nullable();
            $table->date('predicted_date');
            $table->foreignIdFor(Store::class)->constrained()->cascadeOnDelete();
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeignIdFor(Store::class)->constrained()->cascadeOnDelete();
            $table->dropForeignIdFor(Provider::class)->constrained()->cascadeOnDelete();
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('orders');
    }
};
