@extends('layouts.layout')

@section('title', 'Price List')

@section('css')

@endsection

@section('content')
    <style>
        /* Full width & wrap yang rapi */
        #priceTable {
            table-layout: fixed;
            /* kunci lebar kolom agar wrapping konsisten */
            width: 100% !important;
        }

        /* Header: single-line + ellipsis, tidak ikut wrap */
        #priceTable thead th {
            white-space: nowrap !important;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Body: bebas wrap, termasuk kata panjang/URL/part number */
        #priceTable tbody td {
            white-space: normal !important;
            word-break: break-word;
            /* fallback */
            overflow-wrap: anywhere;
            /* modern */
        }

        @media (max-width: 767.98px) {
            #priceTable col {
                width: auto !important;
            }

            #priceTable tbody td {
                font-size: 9px !important;
            }
        }
    </style>
    <div class="container-fluid">
        @if ($data->isEmpty())
            <div class="alert alert-warning mb-0">Price list tidak ditemukan atau akses ditolak.</div>
        @else
            @php
                $pl = $data->first();
                $json = json_decode($pl->datatable_data, true) ?: ['header' => [], 'data' => []];

                // Normalisasi header: dukung format array atau string polos
                $headers = collect($json['header'] ?? [])
                    ->map(function ($h) {
                        if (is_array($h)) {
                            return [
                                'label' => $h['label'] ?? ($h['name'] ?? ($h['title'] ?? '')),
                                'hidden' => (int) !!($h['hidden'] ?? ($h['is_hidden'] ?? false)),
                            ];
                        }
                        return ['label' => (string) $h, 'hidden' => 0];
                    })
                    ->values();

                $rows = $json['data'] ?? [];
            @endphp

            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 py-3">
                <div class="d-flex justify-content-between w-100 align-content-center">
                    <h4 class="mb-1">{{ $pl->title }}</h4>
                    {{-- <div class="text-muted small">
                        Tanggal: {{ \Illuminate\Support\Carbon::parse($pl->date)->format('d M Y') }}
                        @if ($pl->currency)
                            • Mata uang: {{ $pl->currency->code ?? ($pl->currency->name ?? $pl->currency_id) }}
                        @endif
                    </div> --}}
                </div>
                {{-- <div class="text-end">
                    @if ($pl->show_payment_method)
                        <div class="small">Metode Pembayaran: {{ $pl->payment_method ?: '-' }}</div>
                    @endif
                </div> --}}
            </div>

            {{-- @if (!empty($pl->notes))
                <div class="alert alert-info py-2">{!! $pl->notes !!}</div>
            @endif --}}

            {{-- Toolbar global search + tombol Filters --}}
            <div class="d-flex justify-content-end mb-2">
                <div class="col-12 col-md-6">
                    <div class="input-group sticky-actions">
                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-search" viewBox="0 0 16 16">
                                <path
                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                            </svg>
                        </span>
                        <input id="globalSearch" type="text" class="form-control" placeholder="Cari apa saja">
                        <button id="btnToggleFilters" class="btn btn-outline-secondary">Filters</button>
                    </div>
                </div>
            </div>

            {{-- Tabel: tanpa dt-responsive & tanpa nowrap (tidak collapse di mobile) --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="p-3">
                            <h4 class="card-title mb-3">Daftar Item Price List</h4>
                            <div class="table-responsive">
                                <table id="priceTable" class="table table-bordered w-100">
                                    <colgroup>
                                        <col style="width:15%"> <!-- KODE -->
                                        <col style="width:55%"> <!-- NAME (lebih lebar) -->
                                        <col style="width:15%"> <!-- BRAND -->
                                        <col style="width:15%"> <!-- PRICE -->
                                    </colgroup>
                                    <thead class="table-light">
                                        <tr>
                                            @foreach ($headers as $h)
                                                {{-- @dump($h) --}}
                                                <th class="{{ $h['label'] !== 'Name' ? 'text-center' : '' }}"
                                                    data-hidden="{{ $h['hidden'] ? 1 : 0 }}">{{ $h['label'] }}</th>
                                            @endforeach
                                        </tr>
                                        <tr class="filters d-none">
                                            @foreach ($headers as $h)
                                                <th>
                                                    <input type="text" class="form-control form-control-sm"
                                                        placeholder="Filter {{ $h['label'] }}">
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            /** ====== DATA ====== */
            const rowsData = @json($rows ?? []);
            const headersObj = @json($headers ?? []);
            const currency = @json($pl->currency->code ?? 'IDR');

            const headerLabels = headersObj.map(h => h.label || '');
            const hiddenFlags = headersObj.map(h => !!(h.hidden));

            const toKey = (label) => String(label || '')
                .trim().toLowerCase().replace(/\s+/g, ' ')
                .replace(/[^\w]+/g, '_').replace(/^_+|_+$/g, '');

            function convertRows(rows, headers) {
                const keys = headers.map(toKey);
                return (rows || []).map(row => {
                    if (Array.isArray(row)) {
                        const o = {};
                        for (let i = 0; i < keys.length; i++) o[keys[i]] = row[i] ?? '';
                        return o;
                    }
                    const o = {};
                    for (let i = 0; i < headers.length; i++) {
                        const label = headers[i];
                        const k = keys[i];
                        o[k] = row[label] ?? row[k] ?? '';
                    }
                    return o;
                });
            }

            const dataConverted = convertRows(rowsData, headerLabels);
            const keys = headerLabels.map(toKey);

            /** ====== DETEKSI KOLOM HARGA + PRE-FORMAT (ORTHOGONAL) ====== */
            const priceCols = []; // { idx, key }
            headerLabels.forEach((lbl, i) => {
                if (/(^|[^a-z])(price|harga)([^a-z]|$)/i.test(lbl)) priceCols.push({
                    idx: i,
                    key: keys[i]
                });
            });

            // Formatter currency (sekali buat semua)
            const nf = (function() {
                try {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: currency || 'IDR',
                        maximumFractionDigits: 0
                    });
                } catch (_) {
                    return new Intl.NumberFormat('id-ID');
                }
            })();

            // Tambahkan field orthogonal utk setiap kolom harga: __sort (number), __disp (string)
            for (let r = 0; r < dataConverted.length; r++) {
                const row = dataConverted[r];
                for (const {
                        key
                    }
                    of priceCols) {
                    const raw = Number(String(row[key] ?? '').replace(/[^\d.-]/g, ''));
                    const n = Number.isFinite(raw) ? raw : 0;
                    row[key + '__sort'] = n;
                    row[key + '__disp'] = Number.isFinite(raw) ? nf.format(n) : (row[key] ?? '');
                }
            }

            /** ====== DEFINISI KOLOM ====== */
            const dtColumns = headerLabels.map((lbl, i) => {
                const key = keys[i];
                const isPrice = priceCols.some(p => p.key === key);
                if (isPrice) {
                    return {
                        data: {
                            _: key + '__disp',
                            sort: key + '__sort',
                            type: key + '__sort',
                            filter: key + '__disp'
                        },
                        className: 'dt-body-right', // <— perbaikan: tidak pakai titik
                        defaultContent: ''
                    };
                }
                return {
                    data: key,
                    defaultContent: ''
                };
            });

            /** ====== KOLOM TERSEMBUNYI ====== */
            const hiddenTargets = [];
            hiddenFlags.forEach((flag, i) => {
                if (flag) hiddenTargets.push(i);
            });

            let firstVisibleCol = 0;
            for (let i = 0; i < hiddenFlags.length; i++) {
                if (!hiddenFlags[i]) {
                    firstVisibleCol = i;
                    break;
                }
            }

            /** ====== INIT DATATABLE (SCROLLER) ====== */
            const priceTable = $('#priceTable');
            const dt = priceTable.DataTable({
                data: dataConverted,
                columns: dtColumns,

                // UI minimal (tanpa search bawaan, kita pakai #globalSearch)
                dom: 'lrtip',

                // PERFORMA
                deferRender: true, // penting utk scroller
                searchDelay: 400,
                orderMulti: false,
                processing: true,
                stateSave: true,
                autoWidth: false,
                paging: true, // scroller = tanpa paging

                columnDefs: [{
                        targets: hiddenTargets,
                        visible: false
                    },
                    {
                        targets: [1, 3],
                        className: 'dt-body-center'
                    } // opsional: contoh dari kode awalmu
                ],
                order: [
                    [firstVisibleCol, 'asc']
                ],
                language: {
                    processing: '<div style="display:flex;align-items:center;gap:.5rem;">' +
                        '<span class="dt-spinner"></span> Memuat…' +
                        '</div>'
                }
            });

            // GLOBAL SEARCH (di luar tabel)
            $('#globalSearch').val(dt.search());
            $('#globalSearch').on('keyup change', function() {
                dt.search(this.value).draw();
            });

            // Toggle filter per kolom (kalau ada baris .filters di thead)
            const filterRow = document.querySelector('#priceTable thead tr.filters');
            $('#btnToggleFilters').on('click', function() {
                if (!filterRow) return;
                filterRow.classList.toggle('d-none');
                dt.columns.adjust().draw(false);
            });
            if (filterRow) {
                $('#priceTable thead tr.filters th').each(function(i) {
                    const $input = $(this).find('input');
                    if (!$input.length) return;
                    $input.on('keyup change', function() {
                        dt.column(i).search(this.value).draw();
                    });
                });
            }
        });
    </script>

    <style>
        /* spinner kecil untuk state "processing" */
        .dt-spinner {
            width: 18px;
            height: 18px;
            border: 3px solid #e5e7eb;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: dtspin 1s linear infinite
        }

        @keyframes dtspin {
            to {
                transform: rotate(360deg)
            }
        }
    </style>
@endpush
