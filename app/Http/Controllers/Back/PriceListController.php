<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\PriceList;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use misterspelik\LaravelPdf\Facades\Pdf as FacadesPdf;
use misterspelik\LaravelPdf\Pdf as LaravelPdfPdf;

class PriceListController extends Controller
{
    public function index()
    {
        return view('back.pricelist.price-list');
    }

    public function generate_pdf(Request $request)
    {
        $htmlContent = $request->input('htmlcontent');
        // dd();

        // Validasi jika htmlContent kosong
        if (empty($htmlContent)) {
            return response()->json(['error' => 'HTML content is required.'], 422);
        }

        $data = [
            'foo' => 'bar'
        ];

        // Kirim ke view Blade, pastikan view ini menampilkan konten HTML yang disisipkan
        $pdf = Pdf::loadView('back.print.print-price-list', $data);

        return $pdf->stream('price-list.pdf');
    }

    public function viewPdf(PriceList $pricelist)
    {
        if (empty($pricelist->datatable_data)) {
            return response()->json(['error' => 'No data available.'], 422);
        }

        $dataTable = json_decode($pricelist->datatable_data);
        if (!$dataTable || !isset($dataTable->header) || !isset($dataTable->data)) {
            return response()->json(['error' => 'Invalid table structure.'], 422);
        }

        // Filter header yang hanya checkbox & id
        $headers = collect($dataTable->header)
            ->filter(fn($header) => $header->id !== 'id' || ($header->checkbox ?? false))
            ->values();

        // Ambil hanya kolom id dari header yang akan ditampilkan
        $displayColumnIds = $headers->pluck('id')->all();

        // Siapkan data body yang sudah difilter hanya kolom yang ditampilkan
        $body = array_map(function ($row) use ($displayColumnIds) {
            $filtered = [];
            foreach ($displayColumnIds as $colId) {
                $filtered[$colId] = $row->$colId ?? '';
            }
            return $filtered;
        }, $dataTable->data);

        $pdf = Pdf::loadView('back.print.print-price-list', [
            'header' => $headers,
            'body' => $body,
            'footer' => $pricelist->footer_text,
        ]);

        return $pdf->stream($pricelist->title . '.pdf');
    }
}
