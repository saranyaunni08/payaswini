<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id('permission_id');
            $table->foreignId('role_id')->constrained('roles', 'role_id')->onDelete('cascade');
            $table->boolean('can_add_customer')->default(false);
            $table->boolean('can_add_agent')->default(false);
            $table->boolean('can_edit_delete')->default(false);
            $table->integer('edit_delete_time_limit')->default(24);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};