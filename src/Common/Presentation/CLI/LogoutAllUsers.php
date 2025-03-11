<?php

namespace Src\Common\Presentation\CLI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class LogoutAllUsers
 *
 * This command is used to log out all users by truncating the `personal_access_tokens` table.
 * It requires the person running the command to authenticate using their email and password,
 * ensuring that only authorized users can perform this operation. Once authenticated, the
 * command will delete all access tokens, effectively logging out all users.
 *
 * @author Harry(hareom284)
 * @date October 11, 2024
 */
class LogoutAllUsers extends Command
{

    protected $signature = 'users:logout-all';


    protected $description = 'Log out all users by deleting their personal access tokens';


    public function handle()
    {
        if (!$this->confirm('Are you sure you want to log out all users?')) {
            $this->info('Operation canceled.');
            return 0;
        }

        $email = $this->ask('Please enter your email');
        $password = $this->secret('Please enter your password');

        if (!auth()->attempt(['email' => $email, 'password' => $password])) {
            $this->error('Invalid email or password. Operation aborted.');
            return 1;
        }

        DB::table('personal_access_tokens')->truncate();


        $this->info("All users have been logged out successfully by: " . auth()->user()->email);

        info('All users have been logged out successfully by:',[auth()->user()->email]);

        return 0;
    }
}
