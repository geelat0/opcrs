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
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indicator_id');
            $table->unsignedBigInteger('user_id');
            $table->longText('file')->nullable();
            $table->decimal('Albay_accomplishment')->nullable();
            $table->decimal('Camarines_Sur_accomplishment')->nullable();
            $table->decimal('Camarines_Norte_accomplishment')->nullable();
            $table->decimal('Catanduanes_accomplishment')->nullable();
            $table->decimal('Masbate_accomplishment')->nullable();
            $table->decimal('Sorsogon_accomplishment')->nullable();
            $table->decimal('total_accomplishment');
            $table->longText('accomplishment_text');
            $table->unsignedTinyInteger('months')->nullable();
            $table->year('year')->nullable();
            $table->string('status')->default('Completed');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('indicator_id')->references('id')->on('success_indc');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
