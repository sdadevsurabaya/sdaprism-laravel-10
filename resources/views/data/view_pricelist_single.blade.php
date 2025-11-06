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

            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
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
                        <div class="card-body">
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
            /** ====== DATA & KONVERSI ====== */
            const rowsData = @json($rows ?? []);
            const headersObj = @json($headers ?? []);
            const currency = @json($pl->currency->code ?? 'IDR');

            const headerLabels = headersObj.map(h => h.label || '');
            const hiddenFlags = headersObj.map(h => !!(h.hidden));


            function toKey(label) {
                return String(label || '')
                    .trim().toLowerCase()
                    .replace(/\s+/g, ' ')
                    .replace(/[^\w]+/g, '_')
                    .replace(/^_+|_+$/g, '');
            }

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

            function renderCurrency(val) {
                const num = Number((val ?? '').toString().replace(/[^\d.-]/g, ''));
                if (!isFinite(num)) return val ?? '';
                try {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: currency || 'IDR',
                        maximumFractionDigits: 0
                    }).format(num);
                } catch (_) {
                    return num.toLocaleString('id-ID');
                }
            }

            const dataConverted = convertRows(rowsData, headerLabels);

            /** ====== DATATABLES: non-responsive (tidak collapse) + performa ====== */
            const priceTable = $('#priceTable');

            const dtColumns = headerLabels.map(lbl => {
                const key = toKey(lbl);
                const isPrice = /(^|[^a-z])(price|harga)([^a-z]|$)/i.test(lbl);
                return isPrice ? {
                    data: key,
                    render: d => renderCurrency(d),
                    defaultContent: ''
                } : {
                    data: key,
                    defaultContent: ''
                };
            });

            // Kolom hidden langsung by index (tanpa offset kolom kontrol)
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

            const dt = priceTable.DataTable({
                data: dataConverted,
                columns: dtColumns,
                dom: 'lrtip',
                // Performa untuk data besar
                deferRender: true,
                searchDelay: 400,
                orderMulti: false,
                processing: true,
                stateSave: true,
                pageLength: 25,
                lengthMenu: [25, 50, 100, 250, 500, 1000],

                // NON-RESPONSIVE: tidak collapse child rows di mobile
                responsive: false,

                // Hindari reflow berlebihan
                autoWidth: false,
                columnDefs: [{
                        targets: hiddenTargets,
                        visible: false,

                    },
                    {
                        targets: 4, // PRICE
                        className: '. dt-body-right' // rata kanan header & body
                    },
                    {
                        targets: [1, 3], // PRICE
                        className: 'dt-body-center' // rata kanan header & body
                    }
                ],
                order: [
                    [firstVisibleCol, 'asc']
                ],
            });

            $('#globalSearch').val(dt.search())

            // Global search
            $('#globalSearch').on('keyup change', function() {
                dt.search(this.value).draw();
            });

            // Toggle filter per kolom
            const filterRow = document.querySelector('#priceTable thead tr.filters');
            $('#btnToggleFilters').on('click', function() {
                if (!filterRow) return;
                filterRow.classList.toggle('d-none');
                dt.columns.adjust().draw(false);
            });

            console.log(dt.search());

            // Wiring filter per kolom
            if (filterRow) {
                $('#priceTable thead tr.filters th').each(function(i) {
                    const input = $(this).find('input');
                    if (!input.length) return;
                    input.on('keyup change', function() {
                        dt.column(i).search(this.value).draw();
                    });
                });
            }
        });
    </script>
@endpush
