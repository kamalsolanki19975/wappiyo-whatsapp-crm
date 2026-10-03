<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class DatabaseRestoreCommand extends Command
{
    protected $signature = 'wappiyo:restore {--file= : Path to backup .sql or .sql.gz file} {--target-db= : Target database to restore into} {--force : Force restore without confirmation}';
    protected $description = 'Restore a database backup from a .sql or .sql.gz archive';

    public function handle()
    {
        $this->info('Starting Wappiyo Database Restore...');
        $startTime = microtime(true);

        $file = $this->option('file');
        $backupDir = storage_path('app/backups');

        if (!$file) {
            // Find latest backup .sql.gz in backup dir
            $files = glob("{$backupDir}/*.sql.gz");
            if (empty($files)) {
                $this->error("No backup archives found in {$backupDir}");
                return 1;
            }
            rsort($files);
            $file = $files[0];
            $this->info("No file specified. Using latest backup: " . basename($file));
        }

        if (!File::exists($file)) {
            $this->error("Backup file does not exist: {$file}");
            return 1;
        }

        $targetDb = $this->option('target-db') ?: config('database.connections.mysql.database');
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $mysqlBinary = $this->findMysqlBinary();
        if (!$mysqlBinary) {
            $this->error('mysql binary could not be found.');
            return 1;
        }

        // Decompress if gzipped to a temp location
        $isGz = str_ends_with($file, '.gz');
        $restoreSqlPath = $file;

        if ($isGz) {
            $tempSql = storage_path('app/backups/temp_restore_' . time() . '.sql');
            $this->info("Decompressing archive to temporary file: " . basename($tempSql));
            $gunzipProcess = Process::fromShellCommandline(sprintf(
                'gzip -dc %s > %s',
                escapeshellarg($file),
                escapeshellarg($tempSql)
            ));
            $gunzipProcess->run();

            if (!$gunzipProcess->isSuccessful() || !File::exists($tempSql)) {
                $this->error('Decompression failed.');
                return 1;
            }
            $restoreSqlPath = $tempSql;
        }

        $this->info("Restoring SQL dump into [{$targetDb}] using {$mysqlBinary}...");

        $command = sprintf(
            '%s --host=%s --port=%s --user=%s %s %s < %s',
            escapeshellcmd($mysqlBinary),
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            !empty($dbPass) ? '--password=' . escapeshellarg($dbPass) : '',
            escapeshellarg($targetDb),
            escapeshellarg($restoreSqlPath)
        );

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(600);
        $process->run();

        // Clean up temp file
        if ($isGz && File::exists($restoreSqlPath)) {
            File::delete($restoreSqlPath);
        }

        if (!$process->isSuccessful()) {
            $this->error('Restore failed: ' . $process->getErrorOutput());
            return 1;
        }

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Database restore into [{$targetDb}] completed successfully in {$duration}s.");

        return 0;
    }

    protected function findMysqlBinary(): ?string
    {
        $candidates = [
            '/Applications/MAMP/Library/bin/mysql80/bin/mysql',
            '/Applications/MAMP/Library/bin/mysql',
            '/opt/homebrew/bin/mysql',
            '/usr/local/bin/mysql',
            '/usr/bin/mysql',
        ];

        foreach ($candidates as $candidate) {
            if (File::exists($candidate) && is_executable($candidate)) {
                return $candidate;
            }
        }

        $whichProcess = Process::fromShellCommandline('which mysql');
        $whichProcess->run();
        if ($whichProcess->isSuccessful()) {
            return trim($whichProcess->getOutput());
        }

        return null;
    }
}
