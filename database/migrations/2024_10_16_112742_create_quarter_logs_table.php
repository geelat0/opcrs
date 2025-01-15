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
        // Schema::create('quarter_logs', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('indicator_id');
        //     $table->string('Q1_target')->default('0');
        //     $table->string('Albay_target_Q1')->nullable();
        //     $table->string('Camarines_Sur_target_Q1')->nullable();
        //     $table->string('Camarines_Norte_target_Q1')->nullable();
        //     $table->string('Catanduanes_target_Q1')->nullable();
        //     $table->string('Masbate_target_Q1')->nullable();
        //     $table->string('Sorsogon_target_Q1')->nullable();

        //     $table->string('Q2_target')->default('0');
        //     $table->string('Albay_target_Q2')->nullable();
        //     $table->string('Camarines_Sur_target_Q2')->nullable();
        //     $table->string('Camarines_Norte_target_Q2')->nullable();
        //     $table->string('Catanduanes_target_Q2')->nullable();
        //     $table->string('Masbate_target_Q2')->nullable();
        //     $table->string('Sorsogon_target_Q2')->nullable();

        //     $table->string('Q3_target')->default('0');
        //     $table->string('Albay_target_Q3')->nullable();
        //     $table->string('Camarines_Sur_target_Q3')->nullable();
        //     $table->string('Camarines_Norte_target_Q3')->nullable();
        //     $table->string('Catanduanes_target_Q3')->nullable();
        //     $table->string('Masbate_target_Q3')->nullable();
        //     $table->string('Sorsogon_target_Q3')->nullable();

        //     $table->string('Q4_target')->default('0');
        //     $table->string('Albay_target_Q4')->nullable();
        //     $table->string('Camarines_Sur_target_Q4')->nullable();
        //     $table->string('Camarines_Norte_target_Q4')->nullable();
        //     $table->string('Catanduanes_target_Q4')->nullable();
        //     $table->string('Masbate_target_Q4')->nullable();
        //     $table->string('Sorsogon_target_Q4')->nullable();

        //     $table->string('created_by');
        //     $table->string('updated_by')->nullable();
        //     $table->timestamps();

        // });

        Schema::create('quarter_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indicator_id');
        
            foreach (['Q1', 'Q2', 'Q3', 'Q4'] as $quarter) {
                $table->string("{$quarter}_target")->nullable();
                foreach (['Albay', 'Camarines_Sur', 'Camarines_Norte', 'Catanduanes', 'Masbate', 'Sorsogon'] as $region) {
                    $table->string("{$region}_target_{$quarter}")->nullable();
                }
            }
        
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarter_logs');
    }
};
