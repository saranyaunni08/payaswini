<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_agents', function (Blueprint $table) {
            $table->id('agent_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('agent_code')->default('AGT000');
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->string('assigned_branch')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->enum('profile_status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Drop dependent tables or constraints
        Schema::dropIfExists('loans'); // Drop loans table first
        Schema::dropIfExists('documents'); // Drop documents table if it references agent_id

        // Drop collection_agents table
        Schema::dropIfExists('collection_agents');
    }
};