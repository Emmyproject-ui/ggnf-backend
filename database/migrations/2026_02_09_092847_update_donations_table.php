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
        Schema::table('donations', function (Blueprint $table) {
            // Renaming reference to payment_reference for clarity if not existing
            // Since sqlite renameColumn might be tricky without doctrine/dbal in some versions,
            // let's check if we can just add payment_reference and copy data, or if we just add it.
            // Laravel 10+ supports renameColumn on SQLite natively.
            // Assuming recent Laravel.
            $table->renameColumn('reference', 'payment_reference');
            $table->string('cause')->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->renameColumn('payment_reference', 'reference');
            $table->dropColumn('cause');
        });
    }
};
