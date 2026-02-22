<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {

            $table->id(); // id רגיל

            $table->foreignId('task_id')->constrained()->cascadeOnDelete();

            $table->uuid('user_id')->nullable();

            $table->string('action'); // task_completed

            $table->json('meta')->nullable();

            $table->timestamp('occurred_at');

            $table->timestamps();

            $table->index('task_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};