<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreatePlatformAdminCommand extends Command
{
    protected $signature = 'platform:create-admin
        {email : E-Mail-Adresse des Betreiber-Kontos}
        {--name= : Anzeigename des Betreibers}
        {--password= : Passwort; ohne Option wird es sicher abgefragt}';

    protected $description = 'Erstellt oder aktualisiert ein Plattform-Administratorkonto';

    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));
        $password = $this->option('password') ?: $this->secret('Passwort');

        if (! is_string($password) || strlen($password) < 8) {
            $this->error('Das Passwort muss mindestens 8 Zeichen lang sein.');

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name = $this->option('name') ?: ($user->name ?: 'Nexora Betreiber');
        $user->password = Hash::make($password);
        $user->is_platform_admin = true;
        $user->save();

        $this->info("Betreiberkonto für {$user->email} ist bereit.");
        $this->line('Anmeldung: /login · Betreiberbereich: /admin/kunden');

        return self::SUCCESS;
    }
}
