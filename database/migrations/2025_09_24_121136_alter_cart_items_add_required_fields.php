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
        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'product_id')) {
                $table->foreignId('product_id')->after('id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('cart_items', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('cart_items', 'session_id')) {
                $table->string('session_id')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('cart_items', 'quantity')) {
                $table->unsignedInteger('quantity')->default(1)->after('session_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('cart_items', 'session_id')) {
                $table->dropColumn('session_id');
            }
            if (Schema::hasColumn('cart_items', 'user_id')) {
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('cart_items', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }
        });
    }
};
