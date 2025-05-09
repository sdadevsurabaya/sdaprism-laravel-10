<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
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

    public function viewPdf(Request $request)
    {
        $htmlContent = $request->input('htmlcontent');
        // dd();

        // Validasi jika htmlContent kosong
        if (empty($htmlContent)) {
            return response()->json(['error' => 'HTML content is required.'], 422);
        }

        $pdf = Pdf::loadView('back.print.print-price-list', compact('htmlContent'));

        return $pdf->stream('document.pdf');
    }
}
