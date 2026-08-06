<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Currency;
use App\Models\HeaderLogo;
use App\Models\PriceList;
use App\Models\User;
use App\Services\PriceListBridgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PricelistController extends Controller
{
    /**
     * Service Instance untuk API Price List
     *
     * @var PriceListBridgeService
     */
    protected PriceListBridgeService $priceListService;

    /**
     * Inject PriceListBridgeService
     */
    public function __construct(PriceListBridgeService $priceListService)
    {
        $this->priceListService = $priceListService;
    }

    public function index()
    {
        // Route lama tetap ada di kode namun dialihkan langsung ke halaman Detail Pricelist Realtime yang baru
        return redirect()->route('pricelists.realtime');

        /* Kode lama tersimpan aman sebagai fallback & referensi:
        $data = PriceList::all();
        if (Auth::user()?->canAccessRealtimePricelist()) {
            $apiItem = new PriceList([
                'title'               => 'Price Lists',
                'date'                => date('Y-m-d'),
                'show_payment_method' => 0,
                'created_at'          => now(),
            ]);
            $apiItem->id = 'api';
            $data->prepend($apiItem);
        }
        $whitelistedUserIds = \App\Models\PricelistApiWhitelist::pluck('user_id')->toArray();
        $staffUsers         = \App\Models\User::all()->filter(fn($u) => !$u->hasRole('admin'));
        return view('data.view_pricelist', compact('data', 'whitelistedUserIds', 'staffUsers'));
        */
    }

    /**
     * Halaman Baru: Langsung Menampilkan Detail Pricelist Realtime
     */
    public function realtime(Request $request)
    {
        if (!Auth::user()?->canAccessRealtimePricelist()) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk melihat Pricelist Realtime.');
        }

        // Jika tombol perbarui ditekan (?realtime=1), ambil data baru lalu alihkan ke URL bersih
        if ($request->has('realtime') && $request->realtime == '1') {
            $this->priceListService->getAll(true);

            return redirect()->route('pricelists.realtime')
                ->with('success', 'Data pricelist berhasil diperbarui!');
        }

        $response = $this->priceListService->getAll(false);

        $items = [];
        if (isset($response['success']) && $response['success'] && !empty($response['data'])) {
            $items = $response['data'];

            // Urutkan data berdasarkan 'Kode' secara natural & case-insensitive
            usort($items, function ($a, $b) {
                return strnatcasecmp($a['Kode'] ?? '', $b['Kode'] ?? '');
            });
        }

        // Format data agar sesuai dengan skema tabel & view_pricelist_realtime
        $formattedData = [
            'header' => [
                ['id' => 'kode',  'label' => 'KODE',  'hidden' => false, 'checkbox' => true],
                ['id' => 'name',  'label' => 'NAME',  'hidden' => false, 'checkbox' => true],
                ['id' => 'brand', 'label' => 'BRAND', 'hidden' => false, 'checkbox' => true],
                ['id' => 'price', 'label' => 'PRICE', 'hidden' => false, 'checkbox' => true],
            ],
            'data' => array_map(function ($item) {
                return [
                    'kode'  => $item['Kode'] ?? '',
                    'name'  => $item['nama'] ?? '',
                    'brand' => $item['merk'] ?? '',
                    'price' => $item['Harga'] ?? 0,
                ];
            }, $items),
        ];

        $pl = new PriceList([
            'title'               => 'Price Lists',
            'date'                => date('Y-m-d'),
            'currency_id'         => null,
            'notes'               => '<p>Data bersumber langsung dari API bridge remote server (Realtime TokoSDA).</p>',
            'datatable_data'      => json_encode($formattedData),
        ]);
        $pl->id = 'api';

        $data = collect([$pl]);
        return view('data.view_pricelist_realtime', compact('data'));
    }



    public function create()
    {
        $logo     = HeaderLogo::all();
        $currency = Currency::all();
        return view('forms.pricelist', compact('logo', 'currency'));
    }

    public function template_pdf()
    {
        return view('template.pricelist-template');
    }

    public function store(Request $request)
    {
        $rules = [
            'header_logo_id'      => 'nullable|string|max:255',
            'title'               => 'nullable|string|max:255',
            'footer_text'         => 'nullable|string|max:255',
            'date'                => 'nullable|date',
            'currency_id'         => 'nullable|string|max:255',
            'show_payment_method' => 'required',
            'payment_method'      => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
            'datatable_data'      => 'nullable|json',
        ];

        if ($request->has('date')) {
            $date            = strtotime($request->date);
            $formattedDate   = date('Y-m-d', $date);
            $request['date'] = $formattedDate;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = PriceList::create([
                'user_id'             => Auth::id(),
                'header_logo_id'      => $request->header_logo_id,
                'title'               => $request->title,
                'footer_text'         => $request->footer_text,
                'date'                => $request->date,
                'currency_id'         => $request->currency_id,
                'show_payment_method' => $request->show_payment_method,
                'payment_method'      => $request->payment_method,
                'notes'               => $request->notes,
                'datatable_data'      => $request->datatable_data,
            ]);

            ActivityLogger::log(
                'Pricelist Data',
                'Create',
                "Membuat Pricelist baru: " . ($data->title ?? "ID #{$data->id}"),
                ['pricelist_id' => $data->id, 'title' => $data->title]
            );

            return redirect()->route('pricelists.edit', $data)
                ->with('success', 'Data successfully created');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // Khusus jika item yang diminta adalah ID 'api' atau 0
        if ($id === 'api' || $id === '0' || $id === 0) {
            if (!Auth::user()?->canAccessRealtimePricelist()) {
                return redirect()->route('pricelists.index')
                    ->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk melihat Pricelist Realtime.');
            }

            // Ambil data langsung dari API PriceList (v2.0)

            $response = $this->priceListService->getAll();

            $items = [];
            if (isset($response['success']) && $response['success'] && !empty($response['data'])) {
                $items = $response['data'];

                // Urutkan data berdasarkan 'Kode' secara natural & case-insensitive
                usort($items, function ($a, $b) {
                    return strnatcasecmp($a['Kode'] ?? '', $b['Kode'] ?? '');
                });
            }

            // Format data agar sesuai dengan skema tabel & view_pricelist_single
            $formattedData = [
                'header' => [
                    ['id' => 'kode',  'label' => 'KODE',  'hidden' => false, 'checkbox' => true],
                    ['id' => 'name',  'label' => 'NAME',  'hidden' => false, 'checkbox' => true],
                    ['id' => 'brand', 'label' => 'BRAND', 'hidden' => false, 'checkbox' => true],
                    ['id' => 'price', 'label' => 'PRICE', 'hidden' => false, 'checkbox' => true],
                ],
                'data' => array_map(function ($item) {
                    return [
                        'kode'  => $item['Kode'] ?? '',
                        'name'  => $item['nama'] ?? '',
                        'brand' => $item['merk'] ?? '',
                        'price' => $item['Harga'] ?? 0,
                    ];
                }, $items),
            ];

            $pl = new PriceList([
                'title'               => 'Price Lists',
                'date'                => date('Y-m-d'),
                'currency_id'         => null,
                'notes'               => '<p>Data bersumber langsung dari API bridge remote server (Realtime).</p>',
                'datatable_data'      => json_encode($formattedData),
            ]);
            $pl->id = 'api';

            $data = collect([$pl]);
            return view('data.view_pricelist_single', compact('data'));
        }

        // Panggilan database biasa untuk item pricelist Excel
        $pl = PriceList::where('id', $id)->firstOrFail();
        $data = collect([$pl]);
        return view('data.view_pricelist_single', compact('data'));
    }

    public function edit($id)
    {
        if ($id === 'api' || $id === '0' || $id === 0) {
            return redirect()->route('pricelists.show', 'api')
                ->with('info', 'Pricelist API terhubung langsung ke server remote secara realtime.');
        }

        $pricelist = PriceList::findOrFail($id);
        $logo     = HeaderLogo::all();
        $currency = Currency::all();
        return view('forms.pricelist', compact('logo', 'currency', 'pricelist'));
    }

    public function update(Request $request, $id)
    {
        if ($id === 'api' || $id === '0' || $id === 0) {
            return redirect()->route('pricelists.show', 'api');
        }

        $pricelist = PriceList::findOrFail($id);


        $rules = [
            'header_logo_id'      => 'nullable|string|max:255',
            'title'               => 'nullable|string|max:255',
            'footer_text'         => 'nullable|string|max:255',
            'date'                => 'nullable|date',
            'currency_id'         => 'nullable|string|max:255',
            'show_payment_method' => 'required',
            'payment_method'      => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
            'datatable_data'      => 'nullable|json',
        ];

        if ($request->has('date')) {
            $date            = strtotime($request->date);
            $formattedDate   = date('Y-m-d', $date);
            $request['date'] = $formattedDate;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $oldData = $pricelist->only(['title', 'footer_text', 'date', 'currency_id', 'payment_method', 'notes']);

            $dataToUpdate = [
                'user_id'             => Auth::id(),
                'header_logo_id'      => $request->header_logo_id,
                'title'               => $request->title,
                'footer_text'         => $request->footer_text,
                'date'                => $request->date,
                'currency_id'         => $request->currency_id,
                'show_payment_method' => $request->show_payment_method,
                'payment_method'      => $request->payment_method,
                'notes'               => $request->notes,
                'datatable_data'      => $request->datatable_data,
            ];

            $changes = collect($dataToUpdate)->filter(function ($value, $key) use ($pricelist) {
                return $pricelist->$key != $value;
            });

            if ($changes->isNotEmpty()) {
                $pricelist->update($dataToUpdate);

                ActivityLogger::log(
                    'Pricelist Data',
                    'Update',
                    "Mengubah data Pricelist: " . ($pricelist->title ?? "ID #{$pricelist->id}"),
                    [
                        'pricelist_id' => $pricelist->id,
                        'old_values'   => $oldData,
                        'new_values'   => collect($dataToUpdate)->only(['title', 'footer_text', 'date', 'currency_id', 'payment_method', 'notes'])->toArray()
                    ]
                );

                return redirect()->route('pricelists.edit', $pricelist->id)
                    ->with('success', 'Data successfully updated')
                    ->with('open_pdf', route('pricelist.pdf', $pricelist->id));
            } else {
                return redirect()->route('pricelists.edit', $pricelist->id)
                    ->with('success', 'No changes made, opened latest PDF')
                    ->with('open_pdf', route('pricelist.pdf', $pricelist->id));
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        if ($id === 'api' || $id === '0' || $id === 0) {
            return redirect()->back()->with('error', 'Data Price List API tidak dapat dihapus.');
        }


        try {
            $pricelist = PriceList::findOrFail($id);
            $title = $pricelist->title ?? "ID #{$pricelist->id}";
            $deletedId = $pricelist->id;
            $pricelist->delete();

            ActivityLogger::log(
                'Pricelist Data',
                'Delete',
                "Menghapus Pricelist: {$title}",
                ['deleted_id' => $deletedId, 'title' => $title]
            );

            return redirect()->route('pricelists.index')
                ->with('success', 'Data successfully deleted');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete data: ' . $e->getMessage());
        }
    }
}
