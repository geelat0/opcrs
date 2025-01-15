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
        Schema::create('report', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id');
            $table->unsignedBigInteger('indicator_id');
            $table->string('actual_accomplishment')->nullable();
            $table->string('rating')->nullable();
            $table->string('remarks')->nullable();
            $table->string('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('org_id')->references('id')->on('org_otc');
            $table->foreign('indicator_id')->references('id')->on('success_indc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opcr_report');
    }
};


