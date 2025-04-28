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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')
                  ->constrained('users', 'id')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('convestation_id')
                  ->constrained('conversations', 'id')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->text('text');
            $table->boolean('is_read');
            $table->boolean('deleted_for_sender');
            $table->boolean('deleted_for_recipient');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
