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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id')->nullable();
            $table->unsignedBigInteger('property_id')->nullable();
            $table->string('document_name');
            $table->string('document_type'); // agreement document / booking confirmation / sale document / invoice / ID proof
            $table->string('file_path');
            $table->string('uploaded_by');
            $table->timestamps();

            // Set up cascade delete if deal or property is deleted
            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
