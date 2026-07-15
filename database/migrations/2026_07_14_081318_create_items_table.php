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
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_id')->index();
            $table->uuid('parent_id')->nullable()->index();
            $table->foreignUuid('object_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete()->index();
            $table->enum('type', [
                'file',
                'folder',
            ])->index();
            $table->text('name_blob');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->softDeletes()->index('deleted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
