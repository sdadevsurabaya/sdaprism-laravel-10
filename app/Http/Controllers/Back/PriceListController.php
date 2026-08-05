<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\PriceList;
use App\Services\PriceListBridgeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PriceListController extends Controller
{
    public function index()
    {
        return view('back.pricelist.price-list');
    }

    public function generate_pdf(Request $request)
    {
        $htmlContent = $request->input('htmlcontent');

        if (empty($htmlContent)) {
            return response()->json(['error' => 'HTML content is required.'], 422);
        }

        $data = [
            'foo' => 'bar'
        ];

        $pdf = Pdf::loadView('back.print.print-price-list', $data);

        return $pdf->stream('price-list.pdf');
    }

    public function viewPdf($id)
    {
        // Jika parameter adalah instance PriceList atau string 'api'
        $pricelistId = $id instanceof PriceList ? $id->id : $id;

        if ($pricelistId === 'api' || $pricelistId === '0' || $pricelistId === 0) {
            if (!Auth::user()?->canAccessRealtimePricelist()) {
                abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat Pricelist Realtime.');
            }


            $service = app(PriceListBridgeService::class);
            $response = $service->getAll();

            $items = [];
            if (isset($response['success']) && $response['success'] && !empty($response['data'])) {
                $items = $response['data'];
                usort($items, function ($a, $b) {
                    return strnatcasecmp($a['Kode'] ?? '', $b['Kode'] ?? '');
                });
            }

            $headers = [
                (object)['id' => 'kode',  'label' => 'KODE',  'checkbox' => true],
                (object)['id' => 'name',  'label' => 'NAME',  'checkbox' => true],
                (object)['id' => 'brand', 'label' => 'BRAND', 'checkbox' => true],
                (object)['id' => 'price', 'label' => 'PRICE', 'checkbox' => true],
            ];

            $body = array_map(function ($row) {
                return [
                    'kode'  => $row['Kode'] ?? '',
                    'name'  => $row['nama'] ?? '',
                    'brand' => $row['merk'] ?? '',
                    'price' => isset($row['Harga']) ? 'Rp ' . number_format((float)$row['Harga'], 0, ',', '.') : '0',
                ];
            }, $items);

            $pdf = Pdf::loadView('back.print.print-price-list', [
                'header'   => $headers,
                'logo'     => null,
                'currency' => 'IDR',
                'body'     => $body,
                'footer'   => 'Sumber: Remote API TokoSDA (Realtime)',
            ]);

            return $pdf->stream('Price-List-Realtime-API.pdf');
        }

        $pricelist = $id instanceof PriceList ? $id : PriceList::findOrFail($id);

        if (empty($pricelist->datatable_data)) {
            return response()->json(['error' => 'No data available.'], 422);
        }

        $dataTable = json_decode($pricelist->datatable_data);
        if (!$dataTable || !isset($dataTable->header) || !isset($dataTable->data)) {
            return response()->json(['error' => 'Invalid table structure.'], 422);
        }

        // Filter header yang hanya checkbox & id
        $headers = collect($dataTable->header)
            ->filter(fn($header) => $header->id !== 'id' && $header->checkbox)
            ->values();

        // Ambil hanya kolom id dari header yang akan ditampilkan
        $displayColumnIds = $headers->pluck('id')->all();

        // Path Image Logo
        $logoPath = $pricelist->headerLogo->logo_path ?? null;

        // Get Currency
        $currency = $pricelist->currency->symbol ?? 'Rp';

        // Siapkan data body yang sudah difilter hanya kolom yang ditampilkan
        $body = array_map(function ($row) use ($displayColumnIds) {
            $filtered = [];
            foreach ($displayColumnIds as $colId) {
                $filtered[$colId] = $row->$colId ?? '';
            }
            return $filtered;
        }, $dataTable->data);

        $pdf = Pdf::loadView('back.print.print-price-list', [
            'header'    => $headers,
            'logo'      => $logoPath,
            'currency'  => $currency,
            'body'      => $body,
            'footer'    => $pricelist->notes,
        ]);

        return $pdf->stream($pricelist->title . '.pdf');
    }
}
