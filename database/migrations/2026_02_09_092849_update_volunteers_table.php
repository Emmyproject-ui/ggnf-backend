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
        Schema::table('volunteers', function (Blueprint $table) {
            $table->text('skills')->nullable();
            $table->string('availability')->nullable();
            $table->string('phone')->nullable();
            // Making form_data nullable as we are switching to explicit columns
            $table->json('form_data')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropColumn(['skills', 'availability', 'phone']);
            // Revert nullable change if possible, but usually safe to keep nullable
            // $table->json('form_data')->nullable(false)->change();
        });
    }
};
