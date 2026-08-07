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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('author_id')
                ->constrained('admins')
                ->restrictOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
        
            $table->longText('content');
            $table->string('featured_image')->nullable();

            $table->string('status', 20)->default('draft');
            $table->text('rejection_reason')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable();
           

            $table->unsignedBigInteger('view_count')->default(0);
            $table->boolean('is_featured')->default(false);

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->string('type');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index(['author_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['is_featured', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
