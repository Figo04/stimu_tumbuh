<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Satu sheet = judul + baris heading + baris data.
 * Dipakai lima kali oleh DataPenelitianExport, jadi tidak perlu lima kelas sheet.
 */
class SheetTabel implements FromArray, ShouldAutoSize, WithHeadings, WithTitle
{
    /**
     * @param  array<int, string>  $heading
     * @param  array<int, array<int, mixed>>  $baris
     */
    public function __construct(
        private readonly string $judul,
        private readonly array $heading,
        private readonly array $baris,
    ) {}

    public function title(): string
    {
        return $this->judul;
    }

    public function headings(): array
    {
        return $this->heading;
    }

    public function array(): array
    {
        return $this->baris;
    }
}
