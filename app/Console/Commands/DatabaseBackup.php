<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DatabaseBackup extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup database (SQLite in prod, MySQL in local)';

    public function handle()
    {
        $connection = config('database.default');

        if ($connection === 'sqlite') {
            $this->backupSqlite();
        } elseif ($connection === 'mysql') {
            $this->backupMysql();
        } else {
            $this->error("Unsupported DB connection: {$connection}");
        }
    }

    protected function backupSqlite()
    {
        $source = database_path('database.sqlite');
        $fileName = 'sqlite_' . now()->format('Y-m-d_H-i-s') . '.sqlite';
        $backupFolder = '/home/ubuntu/backups/';
        $destination = $backupFolder . $fileName;

        // Backup folder create
        if (!is_dir($backupFolder)) {
            mkdir($backupFolder, 0755, true);
        }

        copy($source, $destination);
        $this->info("SQLite backup saved: " . $destination);

        // Delete old backup files (7 days old)
        $files = glob($backupFolder . 'sqlite_*.sqlite');
        $now = time();
        foreach ($files as $file) {
            if (is_file($file) && $now - filemtime($file) >= 7 * 24 * 60 * 60) {
                unlink($file);
                $this->info("Deleted old backup: $file");
            }
        }
    }

    protected function backupMysql()
    {
        $db = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $fileName = 'mysql_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $backupFolder = '/home/ubuntu/backups/';
        $destination = $backupFolder . $fileName;

        if (!is_dir($backupFolder)) {
            mkdir($backupFolder, 0755, true);
        }

        // mysqldump command
        $command = "mysqldump -h {$host} -u {$user} " . ($pass ? "-p'{$pass}' " : '') . "{$db} > {$destination}";
        system($command);

        $this->info("MySQL backup saved: " . $destination);

        // Delete old backup files (7 days old)
        $files = glob($backupFolder . 'mysql_*.sql');
        $now = time();
        foreach ($files as $file) {
            if (is_file($file) && $now - filemtime($file) >= 7 * 24 * 60 * 60) {
                unlink($file);
                $this->info("Deleted old backup: $file");
            }
        }
    }
}
