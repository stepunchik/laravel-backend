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
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->change();
            $table->boolean('deleted_for_sender')->default(false)->change();
            $table->boolean('deleted_for_recipient')->default(false)->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_read')->change();
            $table->boolean('deleted_for_sender')->change();
            $table->boolean('deleted_for_recipient')->change();
        });
    }
};
