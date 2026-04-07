<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportSqliteData extends Command
{
    protected $signature = 'import:sqlite {path=database/database.sqlite}';
    protected $description = 'Import data from the SQLite file into current MySQL database';

    public function handle(): int
    {
        $path = $this->argument('path');
        if (! file_exists(base_path($path))) {
            $this->error("SQLite file not found: {$path}");
            return self::FAILURE;
        }

        // Create a temporary SQLite connection
        config(["database.connections.sqlite_import" => [
            'driver' => 'sqlite',
            'url' => null,
            'database' => base_path($path),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]]);

        $sqlite = DB::connection('sqlite_import');
        $mysql = DB::connection();

        $this->info('Starting import from SQLite to MySQL...');

        // Import users
        $users = $sqlite->table('users')->get();
        $importedUsers = 0;
        foreach ($users as $u) {
            // Upsert by email
            // Map role values to MySQL enum when available
            $role = $u->role ?? null;
            if ($role === 'turist') { $role = 'user'; }
            if ($role && !in_array($role, ['user','operator','admin'])) { $role = 'user'; }

            $data = [
                'name' => $u->name,
                'last_name' => $u->last_name ?? null,
                'email' => $u->email,
                'password' => $u->password, // keep existing hashed password
                'role' => $role ?? 'user',
                'Country' => $u->Country ?? null,
                'date_of_birth' => $u->date_of_birth ?? null,
                'first_time_preferences' => $u->first_time_preferences ?? 1,
                'email_verified_at' => $u->email_verified_at ?? null,
                'created_at' => $u->created_at ?? now(),
                'updated_at' => $u->updated_at ?? now(),
            ];
            $exists = $mysql->table('users')->where('email', $u->email)->exists();
            if ($exists) {
                $mysql->table('users')->where('email', $u->email)->update($data);
            } else {
                $mysql->table('users')->insert($data);
                $importedUsers++;
            }
        }
        $this->info("Users processed: {$users->count()}, new imported: {$importedUsers}");

        // Import preferences
        $preferences = $sqlite->table('preferences')->get();
        $importedPrefs = 0;
        foreach ($preferences as $p) {
            $data = [
                'name' => $p->name,
                'color' => $p->color ?? null,
                'image' => $p->image ?? null,
                'created_at' => $p->created_at ?? now(),
                'updated_at' => $p->updated_at ?? now(),
            ];
            $exists = $mysql->table('preferences')->where('name', $p->name)->exists();
            if ($exists) {
                $mysql->table('preferences')->where('name', $p->name)->update($data);
            } else {
                $mysql->table('preferences')->insert($data);
                $importedPrefs++;
            }
        }
        $this->info("Preferences processed: {$preferences->count()}, new imported: {$importedPrefs}");

        // Build preference name -> id map in MySQL
        $prefMap = $mysql->table('preferences')->pluck('id', 'name');
        // Build user email -> id map in MySQL
        $userMap = $mysql->table('users')->pluck('id', 'email');

        // Import pivot preference_user
        if ($sqlite->getSchemaBuilder()->hasTable('preference_user')) {
            $pivot = $sqlite->table('preference_user')->get();
            $insertedPivot = 0;
            foreach ($pivot as $pv) {
                // Need to resolve user_id and preference_id to MySQL ids.
                // Try to resolve via names: get preference name by old id.
                $prefName = $sqlite->table('preferences')->where('id', $pv->preference_id)->value('name');
                $email = $sqlite->table('users')->where('id', $pv->user_id)->value('email');
                if (! $prefName || ! $email) {
                    continue;
                }
                $newPrefId = $prefMap[$prefName] ?? null;
                $newUserId = $userMap[$email] ?? null;
                if (! $newPrefId || ! $newUserId) {
                    continue;
                }
                $exists = $mysql->table('preference_user')
                    ->where('user_id', $newUserId)
                    ->where('preference_id', $newPrefId)
                    ->exists();
                if (! $exists) {
                    $mysql->table('preference_user')->insert([
                        'user_id' => $newUserId,
                        'preference_id' => $newPrefId,
                    ]);
                    $insertedPivot++;
                }
            }
            $this->info("Pivot preference_user processed: {$pivot->count()}, new inserted: {$insertedPivot}");
        } else {
            $this->warn('SQLite does not have preference_user table, skipping pivot import.');
        }

        $this->info('Import complete.');
        return self::SUCCESS;
    }
}
