<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeUser extends Command
{
    protected $signature = 'archery:make-user {email} {name} {role=coach : admin|coach|athlete} {--password=}';
    protected $description = 'Buat akun admin/pelatih (atlet mendaftar sendiri lewat /register)';

    public function handle(): int
    {
        $role = $this->argument('role');
        if (! in_array($role, ['admin', 'coach', 'athlete'], true)) {
            $this->error('Role harus admin, coach, atau athlete.');

            return self::FAILURE;
        }
        $password = $this->option('password') ?: $this->secret('Password (min 8 karakter)');
        if (strlen((string) $password) < 8) {
            $this->error('Password minimal 8 karakter.');

            return self::FAILURE;
        }
        User::updateOrCreate(['email' => $this->argument('email')], [
            'name' => $this->argument('name'), 'role' => $role, 'password' => Hash::make($password),
        ]);
        $this->info("Akun {$role} {$this->argument('email')} siap.");

        return self::SUCCESS;
    }
}
