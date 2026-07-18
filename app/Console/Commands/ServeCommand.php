<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ServeCommand extends Command
{
    protected $signature = 'panel:serve {--port=8050}';
    protected $description = 'Start MariaDB and the development server for DragoraPanel';

    public function handle()
    {
        $port = $this->option('port');

        $this->line('');
        $this->line('  Home:     http://localhost:' . $port);
        $this->line('  Login:    http://localhost:' . $port . '/auth/login');
        $this->line('  Panel:    http://localhost:' . $port . '/panel');
        $this->line('');
        $this->warn('Starting PHP development server...');
        $this->line('  Press Ctrl+C to stop');
        $this->line('');

        passthru('php artisan serve --port=' . $port, $exitCode);
        return $exitCode;
    }
}
