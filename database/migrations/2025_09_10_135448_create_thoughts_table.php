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
        Schema::create('thoughts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('title_id')->constrained()->cascadeOnDelete();
            $table->integer('year');   // 必須
            $table->integer('month')->nullable();
            $table->integer('day')->nullable();
            $table->string('part')->nullable();
            $table->text('thought')->nullable();
            $table->foreignId('tag_id')->nullable()->constrained()->nullOnDelete();
            $table->text('link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thoughts');
    }
};
