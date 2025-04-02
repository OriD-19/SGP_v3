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
        Schema::create('user_stories', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->text("description");
            $table->datetime("due_date");

            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('sprint_id')->nullable()->constrained('sprints')->onDelete('cascade');
            $table->foreignId('priority_id')->nullable()->constrained('priorities')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_stories');
    }
};
