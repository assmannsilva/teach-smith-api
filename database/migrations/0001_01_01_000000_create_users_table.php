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
            $table->uuid("id")->primary();
            $table->rememberToken();
            $table->timestamps();
            $table->string('first_name',1024);
            $table->string('surname',1024);
            $table->string('first_name_index')->index()->nullable();
            $table->json('surname_tokens')->nullable();
            $table->string('role');
            $table->string('email',1024)->unique();
            $table->string('email_index')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->foreignUuid("organization_id")->nullable();
            $table->boolean("master")->default(false);
            $table->boolean("active")->default(true);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
    }
};
