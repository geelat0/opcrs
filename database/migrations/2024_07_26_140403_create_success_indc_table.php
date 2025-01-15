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
        Schema::create('success_indc', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('org_id');

            $table->string('target')->default('0');

            $table->unsignedBigInteger('quarter_logs_id')->nullable();
            $table->string('Q1_target')->nullable();
            $table->string('Q2_target')->nullable();
            $table->string('Q3_target')->nullable();
            $table->string('Q4_target')->nullable();

            $table->string('Albay_target')->nullable();
            $table->string('Camarines_Sur_target')->nullable();
            $table->string('Camarines_Norte_target')->nullable();
            $table->string('Catanduanes_target')->nullable();
            $table->string('Masbate_target')->nullable();
            $table->string('Sorsogon_target')->nullable();

            $table->decimal('Albay_budget', 20, 3)->nullable();
            $table->decimal('Camarines_Sur_budget', 20, 3)->nullable();
            $table->decimal('Camarines_Norte_budget', 20, 3)->nullable();
            $table->decimal('Catanduanes_budget', 20, 3)->nullable();
            $table->decimal('Masbate_budget', 20, 3)->nullable();
            $table->decimal('Sorsogon_budget', 20, 3)->nullable();

            $table->longText('measures');
            $table->decimal('alloted_budget', 20, 3);
            $table->json('division_id')->nullable();
            $table->string('status')->default('Active');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('org_id')->references('id')->on('org_otc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('success_indc');
    }
};
