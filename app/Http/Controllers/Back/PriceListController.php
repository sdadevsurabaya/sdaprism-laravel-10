<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PriceListController extends Controller
{
    public function index()
    {
        return view('back.pricelist.price-list');
    }

    public function printPDF(Request $request)
    {
        $htmlContent = $request->htmlcontent;
        // dd($htmlContent);

        $pdf = Pdf::loadView('back.print.print-price-list', compact('htmlContent'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('price-list.pdf');
    }
}
