<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin-user {email} {name} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update an admin user with the specified email, name, and password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name');
        $password = $this->argument('password');

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        // Update user if it already exists
        if ($user->wasRecentlyCreated === false) {
            $user->update([
                'name' => $name,
                'password' => Hash::make($password),
            ]);
            $this->info("Updated existing user: {$email}");
        } else {
            $this->info("Created new user: {$email}");
        }

        // Assign admin role
        $user->assignRole('admin');

        $this->info("User {$email} has been assigned the admin role.");
    }
}
