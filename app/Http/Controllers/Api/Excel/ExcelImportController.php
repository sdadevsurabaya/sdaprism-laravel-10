<?php

namespace App\Http\Controllers\Api\Excel;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportController extends Controller
{
    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,xls,csv',
    //     ]);

    //     $file = $request->file('file');
    //     $data = Excel::toArray([], $file)[0];

    //     // Ambil header dari baris pertama
    //     $headers = array_map(function ($header) {
    //         return [
    //             'id' => Str::slug($header, '_'),
    //             'label' => $header,
    //             'hidden' => false,
    //             'checkbox' => true,
    //         ];
    //     }, $data[0]);

    //     // Ambil data (mulai dari baris ke-2)
    //     $rows = array_slice($data, 1);

    //     // Format data sebagai array of associative array
    //     $formattedData = array_map(function ($row) use ($headers) {
    //         $item = [];
    //         foreach ($headers as $index => $header) {
    //             $item[$header['id']] = $row[$index] ?? '';
    //         }
    //         return $item;
    //     }, $rows);

    //     return response()->json([
    //         'header' => $headers,
    //         'data' => $formattedData
    //     ]);
    // }

    public function import(Request $request)
    {
        $file = $request->file('file');
        if (!$file) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        // Load Excel spreadsheet
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (empty($rows)) {
            return response()->json(['error' => 'Empty file'], 400);
        }

        // Ambil header
        $headerRow = array_shift($rows); // Baris pertama sebagai header
        $headers = [];
        foreach ($headerRow as $colId => $colName) {
            if ($colName !== null && $colName !== '') {
                $headers[] = [
                    'id' => strtolower(str_replace(' ', '_', $colName)),
                    'label' => $colName,
                    'hidden' => false,
                    'checkbox' => true
                ];
            }
        }

        // Ambil data
        $data = [];
        foreach ($rows as $row) {
            $entry = [];
            foreach ($headerRow as $colId => $colName) {
                if ($colName !== null && $colName !== '') {
                    $key = strtolower(str_replace(' ', '_', $colName));
                    $entry[$key] = $row[$colId] ?? '';
                }
            }
            $data[] = $entry;
        }

        return response()->json([
            'header' => $headers,
            'data' => $data
        ]);
    }
}
