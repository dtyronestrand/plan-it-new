<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->foreignId('calendar_id')->constrained('calendars')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->boolean('done')->default(false);
            $table->json('sub_tasks')->nullable();
            $table->json('attachments')->nullable();
            $table->dateTime('due_date')->nullable();
          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
