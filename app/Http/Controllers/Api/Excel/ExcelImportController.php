<?php

namespace App\Http\Controllers\Api\Excel;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportController extends Controller
{
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
