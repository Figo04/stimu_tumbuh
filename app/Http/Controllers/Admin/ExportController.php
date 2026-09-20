<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DataPenelitianExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Dua tombol Export di dashboard admin (PRD §3.2): Excel 5 sheet & CSV ringkas. */
class ExportController extends Controller
{
    public function excel(): BinaryFileResponse
    {
        return Excel::download(new DataPenelitianExport, DataPenelitianExport::namaFile('xlsx'));
    }

    public function csv(): StreamedResponse
    {
        [$heading, $baris] = (new DataPenelitianExport)->csvRingkas();

        return response()->streamDownload(function () use ($heading, $baris) {
            $keluaran = fopen('php://output', 'w');

            // BOM + pemisah ';' → Excel dengan locale Indonesia (koma = desimal) membuka file ini
            // langsung terkolom rapi. Ganti ke ',' tanpa BOM bila file dipakai di SPSS/R.
            fwrite($keluaran, "\xEF\xBB\xBF");
            fputcsv($keluaran, $heading, separator: ';', escape: '');

            foreach ($baris as $b) {
                fputcsv($keluaran, array_map(self::nilai(...), $b), separator: ';', escape: '');
            }

            fclose($keluaran);
        }, DataPenelitianExport::namaFile('csv'), ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Skor ditulis dengan koma desimal — satu file CSV ini memang untuk dibuka di Excel. */
    private static function nilai(mixed $nilai): string
    {
        return is_float($nilai) ? number_format($nilai, 2, ',', '') : (string) $nilai;
    }
}
