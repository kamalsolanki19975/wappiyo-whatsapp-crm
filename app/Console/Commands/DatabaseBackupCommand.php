<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'wappiyo:backup {--retention=7 : Number of days of backups to retain} {--include-storage : Include storage/app/public in backup}';
    protected $description = 'Perform a full database and application storage backup for disaster recovery';

    public function handle()
    {
        $this->info('Starting Wappiyo Production Backup...');
        $startTime = microtime(true);

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $dbName = config('database.connections.mysql.database');
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $sqlFilename = "backup_{$dbName}_{$timestamp}.sql";
        $sqlPath = "{$backupDir}/{$sqlFilename}";
        $gzPath = "{$sqlPath}.gz";

        // Locate mysqldump binary
        $dumpBinary = $this->findMysqldumpBinary();
        if (!$dumpBinary) {
            $this->error('mysqldump binary could not be found.');
            return 1;
        }

        $this->info("Dumping database [{$dbName}] using {$dumpBinary}...");

        $command = sprintf(
            '%s --host=%s --port=%s --user=%s %s %s > %s',
            escapeshellcmd($dumpBinary),
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            !empty($dbPass) ? '--password=' . escapeshellarg($dbPass) : '',
            escapeshellarg($dbName),
            escapeshellarg($sqlPath)
        );

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(600);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->error('mysqldump failed: ' . $process->getErrorOutput());
            return 1;
        }

        if (!File::exists($sqlPath) || File::size($sqlPath) === 0) {
            $this->error('Dump file was not created or is empty.');
            return 1;
        }

        $rawSize = File::size($sqlPath);
        $this->info(sprintf('Database dump successful. Raw size: %s bytes.', number_format($rawSize)));

        // Compress SQL file
        $gzipProcess = Process::fromShellCommandline("gzip -9 " . escapeshellarg($sqlPath));
        $gzipProcess->run();

        $compressedSize = File::exists($gzPath) ? File::size($gzPath) : 0;
        $checksum = File::exists($gzPath) ? hash_file('sha256', $gzPath) : '';

        $this->info(sprintf('Compression complete. Archive size: %s bytes. SHA256: %s', number_format($compressedSize), $checksum));

        // Include storage files if requested
        $storageArchive = null;
        if ($this->option('include-storage')) {
            $storagePath = storage_path('app/public');
            if (File::exists($storagePath)) {
                $storageFilename = "storage_{$timestamp}.tar.gz";
                $storageArchive = "{$backupDir}/{$storageFilename}";
                $tarProcess = Process::fromShellCommandline(sprintf(
                    'tar -czf %s -C %s .',
                    escapeshellarg($storageArchive),
                    escapeshellarg($storagePath)
                ));
                $tarProcess->run();
                if ($tarProcess->isSuccessful()) {
                    $this->info("Storage files archive created: {$storageFilename}");
                }
            }
        }

        // Write metadata manifest
        $manifestPath = "{$backupDir}/backup_{$timestamp}.json";
        $manifest = [
            'timestamp' => $timestamp,
            'database' => $dbName,
            'dump_file' => basename($gzPath),
            'raw_bytes' => $rawSize,
            'compressed_bytes' => $compressedSize,
            'sha256' => $checksum,
            'storage_included' => $this->option('include-storage'),
            'storage_file' => $storageArchive ? basename($storageArchive) : null,
            'tables_count' => count(DB::select('SHOW TABLES')),
            'duration_seconds' => round(microtime(true) - $startTime, 2),
        ];

        File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT));

        // Retention policy cleanup
        $retentionDays = (int)$this->option('retention');
        $this->cleanOldBackups($backupDir, $retentionDays);

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Wappiyo Backup Completed in {$duration}s. Manifest written to {$manifestPath}");

        return 0;
    }

    protected function findMysqldumpBinary(): ?string
    {
        $candidates = [
            '/Applications/MAMP/Library/bin/mysql80/bin/mysqldump',
            '/Applications/MAMP/Library/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/usr/bin/mysqldump',
        ];

        foreach ($candidates as $candidate) {
            if (File::exists($candidate) && is_executable($candidate)) {
                return $candidate;
            }
        }

        $whichProcess = Process::fromShellCommandline('which mysqldump');
        $whichProcess->run();
        if ($whichProcess->isSuccessful()) {
            return trim($whichProcess->getOutput());
        }

        return null;
    }

    protected function cleanOldBackups(string $backupDir, int $days): void
    {
        if ($days <= 0) return;

        $threshold = time() - ($days * 86400);
        $files = File::files($backupDir);

        foreach ($files as $file) {
            if ($file->getMTime() < $threshold) {
                File::delete($file->getPathname());
                $this->line("Purged expired backup: {$file->getFilename()}");
            }
        }
    }
}
