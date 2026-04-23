<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // who requested/submitted it (could be admin or end-user)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('mfs_agent_id')->constrained('mfs_agents')->cascadeOnDelete();
            $table->foreignId('mfs_number_id')->constrained('mfs_numbers')->cascadeOnDelete();
            $table->enum('type', ['deposit','withdraw'])->index();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 8)->default('BDT');
            $table->string('trx_id'); // external provider trx id
            $table->string('status')->default('pending');
            $table->json('metadata')->nullable();
            $table->foreignId('maker_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checker_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Deduplication: prevent duplicate trx across same MFS agent
            $table->unique(['trx_id','mfs_agent_id'], 'trx_agent_unique');

            // We can also index by status for fast queries
            $table->index('status');
        });
    }
    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};