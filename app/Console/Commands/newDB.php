<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class newDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'menambahkan db baru';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $database = env('DB_DATABASE');
        if (empty($database)) {
            return     $this->error('Silahkan isi nama database pada file .env');
        }
        try {
            $query = "create database \"{$database}\"";
            DB::connection('default_pgsql')->getPdo()->exec($query);

            $this->info("Database {$database} berhasil dibuat");
        } catch (\Exception $e) {
            $this->error("Gagal membuat database {$e->getMessage()}");
        }
    }
}
