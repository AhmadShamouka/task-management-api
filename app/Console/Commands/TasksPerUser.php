<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class TasksPerUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:per-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List the number of tasks for each user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch all users with their task counts
        $users = User::withCount('tasks')->get();

        if ($users->isEmpty()) {
            $this->warn('No users found.');
            return Command::SUCCESS;
        }

        // Prepare table data
        $rows = $users->map(function ($user) {
            return [
                'User ID'   => $user->id,
                'Name'      => $user->name,
                'Task Count'=> $user->tasks_count,
            ];
        });

        // Display the output table
        $this->info("Tasks per user:");
        $this->table(['User ID', 'Name', 'Tasks'], $rows);

        return Command::SUCCESS;
    }
}
