<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CommentsPerUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comments:per-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List the number of comments made by each user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Load users and count their comments
        $users = User::withCount('comments')->get();

        if ($users->isEmpty()) {
            $this->warn('No users found.');
            return Command::SUCCESS;
        }

        $rows = $users->map(function ($user) {
            return [
                'User ID'        => $user->id,
                'Name'           => $user->name,
                'Comments Count' => $user->comments_count,
            ];
        });

        $this->info("Comments per user:");
        $this->table(['User ID', 'Name', 'Comments'], $rows);

        return Command::SUCCESS;
    }
}
