<?php

namespace App\Console\Commands;

use App\Exports\DataPenelitianExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

/** Pendamping tombol Export di dashboard (PRD §6) — untuk backup manual/terjadwal dari server. */
class ExportDataPenelitian extends Command
{
    protected $signature = 'export:data-penelitian {--disk= : Nama disk tujuan (default: disk penyimpanan utama)}';

    protected $description = 'Menulis file Excel 5 sheet (PRD §3.4) ke storage.';

    public function handle(): int
    {
        $disk = $this->option('disk');
        $nama = DataPenelitianExport::namaFile('xlsx');

        Excel::store(new DataPenelitianExport, $nama, $disk);

        $this->info('Tersimpan: '.Storage::disk($disk)->path($nama));

        return self::SUCCESS;
    }
}
