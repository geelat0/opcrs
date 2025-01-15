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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('user_name');
            $table->string('province');
            $table->string('position');
            $table->string('mobile_number');
            $table->unsignedBigInteger('role_id');
            $table->json('division_id');
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('proxy_password')->nullable();
            $table->date('last_date_change')->nullable();
            $table->date('expiration_date')->nullable();
            $table->string('twofa_secret', 255)->nullable();
            $table->rememberToken();
            $table->string('status');
            $table->string('created_by');
            $table->string('profile_image')->nullable();
            $table->boolean('is_change_password')->default(0);
            $table->boolean('is_two_factor_enabled')->default(0);
            $table->boolean('is_two_factor_verified')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('role_id')->references('id')->on('role');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            // $table->unsignedBigInteger('user_id');
            // $table->foreign('user_id')->references('id')->on('user');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->index();
            //$table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
