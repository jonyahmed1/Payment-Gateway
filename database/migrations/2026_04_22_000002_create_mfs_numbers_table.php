<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('mfs_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mfs_agent_id')->constrained('mfs_agents')->cascadeOnDelete();
            $table->string('phone_number')->index();
            $table->string('label')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('mfs_numbers');
    }
};