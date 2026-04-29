<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */


// User.php ---> Relations ---> teams(), tasks(), joinRequests(), adminTeams()
// Team.php ---> Relatoins ---> users(), tasks(), joinRequests(), hasUser(), isAdmin(), admins()
// Task.php ---> Relations ---> team(), creator(), assigend_to(), 
// TeamJoinRequest ---> Relations ---> team(), user()

// Migrations
//  users ---> string('name'), string('email', unique), string('avatar', nullable), timestamp('email_verified_at', nullable), string('password', nullable), rememberToken(), timestampts()
//  teams ---> string('name'), text('description', nullable), string('join_code', nullable), foreingId('owner_id', constrained('users', onDelete('cascade'))), timestamps()
//  tasks ---> foreignId('team_id', constrained(), onDelete('cascade')), foreignId('user_id', constrained(), onDelete('cascade')), string('title'), text('description', nullable), enum('status', ['todo', 'in_progress', 'done'], default('todo')), date('end_date'), foreignId('assigned_to', constrained('users'), onDelete('set_null'), nullable), foreignId('created_by', constrained('users'), onDelete('cascade'), nullable), tmiestamps()

    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo');
            $table->date('end_date')->nullable();
            // $table->foreignId('done_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('cascade');
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
