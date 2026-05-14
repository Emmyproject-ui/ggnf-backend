<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add soft deletes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to donations table
        Schema::table('donations', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to volunteers table
        Schema::table('volunteers', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('volunteers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
