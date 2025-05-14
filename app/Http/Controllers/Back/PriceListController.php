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

        $dataTableHeader = $dataTable->header;
        $dataTableData = $dataTable->data;
        $arrayHeader = [];

        $table = '<table class="table" border="0">';
        $table .= '<thead id="modal-table-header">';
        $table .= '<tr>';
        foreach ($dataTableHeader as $header) {
            if ($header->id === 'id') {
                $table .= "<th>No</th>";
            } elseif ($header->checkbox) {
                $arrayHeader[] = $header->id;
                $table .= "<th>{$header->label}</th>";
            }
        }
        $table .= '</tr>';
        $table .= '</thead>';

        $table .= '<tbody id="modal-table-body">';
        $no = 1;
        foreach ($dataTableData as $dtbody) {
            $table .= '<tr>';
            $table .= "<td>{$no}</td>";
            foreach ($arrayHeader as $colId) {
                $value = $dtbody->$colId ?? '';
                $table .= "<td>{$value}</td>";
            }
            $table .= '</tr>';
            $no++;
        }
        $table .= '</tbody>';
        $table .= '</table>';

        // If you want to inspect during development:
        // dump($arrayHeader);
        // dump($table);
        // dd($dataTable);

        $pdf = Pdf::loadView('back.print.print-price-list', [
            'htmlContent' => $table,
            'footer' => $pricelist->footer_text,
        ]);

        $title = $pricelist->title . '.pdf';

        return $pdf->stream($title);
    }
}
