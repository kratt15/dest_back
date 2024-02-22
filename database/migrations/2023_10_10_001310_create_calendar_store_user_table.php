<?php

use App\Models\Calendar;
use App\Models\User;
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
        Schema::create('calendar_store_user', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->foreignIdFor(Calendar::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Store::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->primary(['calendar_id', 'user_id', 'store_id']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_store_user', function (Blueprint $table) {
            $table->dropForeignIdFor(Calendar::class);
            $table->dropForeignIdFor(Store::class);
            $table->dropForeignIdFor(User::class);
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('calendar_user_store');
    }
};
