<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdmin extends Command
{
    protected $signature = 'travelorio:admin
        {email : Login email}
        {--name= : Display name}
        {--role=owner : owner or editor}
        {--password= : Password (asked for when omitted)}';

    protected $description = 'Create or update an admin panel user';

    public function handle(): int
    {
        $role = $this->option('role');

        if (! array_key_exists($role, User::ROLES)) {
            $this->error('Role must be owner or editor.');

            return self::FAILURE;
        }

        $password = $this->option('password') ?: $this->secret('Password (min 10 characters)');

        if (strlen((string) $password) < 10) {
            $this->error('The password must be at least 10 characters.');

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => $this->argument('email')]);
        $user->name = $this->option('name') ?: ($user->name ?: 'Admin');
        $user->role = $role;
        $user->password = Hash::make($password);
        $user->save();

        $this->info(($user->wasRecentlyCreated ? 'Created' : 'Updated')." {$role} {$user->email}. Log in at ".url('/admin'));

        return self::SUCCESS;
    }
}
