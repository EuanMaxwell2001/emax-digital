<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('sector');
            $table->json('services');
            $table->unsignedSmallInteger('year')->nullable();
            $table->text('summary');
            $table->string('description', 300)->comment('Meta description, 120 to 160 characters');
            $table->string('cover_path')->nullable();
            $table->string('cover_alt')->default('');
            $table->json('cover_meta')->nullable()->comment('Width, height and generated variants');
            $table->text('context');
            $table->text('idea');
            $table->text('outcome');
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('project_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('image_path')->nullable();
            $table->json('image_meta')->nullable();
            $table->string('alt')->default('');
            $table->string('caption')->default('');
            $table->string('shape', 16)->default('wide');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_details');
        Schema::dropIfExists('projects');
    }
};
