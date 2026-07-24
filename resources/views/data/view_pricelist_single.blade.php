@extends('layouts.layout')

@section('title', 'Price List')

@section('css')
<link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet" />
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

            /* .table-responsive {
                            font-size: 0.6rem !important;
                        } */
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

            <div
                class="d-flex flex-column flex-md-row justify-content-center align-items-md-center gap-2 py-3 mb-3 border-bottom">
                <div>
                    <h4 class="mb-0">{{ $pl->title }}</h4>
                </div>
            </div>


            {{-- @if (!empty($pl->notes))
                <div class="alert alert-info py-2">{!! $pl->notes !!}</div>
            @endif --}}

            {{-- Toolbar global search + tombol Filters --}}
            <div class="d-flex justify-content-end mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="input-group sticky-actions">
                        <input id="globalSearch" type="text" class="form-control" placeholder="Cari apa saja">

                        <span class="input-group-text">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-search" viewBox="0 0 16 16">
                                <path
                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                            </svg>
                        </span>
                    </div>
                    <button id="btnToggleFilters" class="btn btn-outline-secondary">Filters</button>
                </div>
            </div>

            {{-- Tabel & Mobile Card List --}}
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="p-3">
                            {{-- Mobile Card List --}}
                            <div class="d-block d-md-none">
                                <div id="mobileCardsContainer" class="d-flex flex-column gap-3"></div>
                                <div id="mobileSinglePaginationWrapper" class="d-flex flex-column align-items-center gap-2 mt-4">
                                    <small id="mobileSinglePageInfo" class="text-muted"></small>
                                    <nav>
                                        <ul id="mobileSinglePaginationNav" class="pagination pagination-sm mb-0 flex-wrap justify-content-center"></ul>
                                    </nav>
                                </div>
                            </div>

                            {{-- Desktop Table --}}
                            <div class="table-responsive d-none d-md-block">
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
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
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
                        className: 'dt-body-right',
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
                dom: 'lrtip',
                deferRender: true,
                searchDelay: 400,
                orderMulti: false,
                processing: true,
                stateSave: true,
                autoWidth: false,
                paging: true,
                columnDefs: [{
                        targets: hiddenTargets,
                        visible: false
                    },
                    {
                        targets: [1, 3],
                        className: 'dt-body-center'
                    }
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

            // Toggle filter per kolom
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

            /** ====== MOBILE CARD LIST RENDERER ====== */
            let mobileCurrentPage = 1;
            const mobileItemsPerPage = 10;

            function renderMobileCards() {
                const container = document.getElementById('mobileCardsContainer');
                const pageInfo = document.getElementById('mobileSinglePageInfo');
                const pageNav = document.getElementById('mobileSinglePaginationNav');
                if (!container) return;

                const filteredRows = dt ? dt.rows({ search: 'applied' }).data().toArray() : dataConverted;
                const totalItems = filteredRows.length;
                const totalPages = Math.ceil(totalItems / mobileItemsPerPage) || 1;

                if (mobileCurrentPage > totalPages) mobileCurrentPage = totalPages;
                if (mobileCurrentPage < 1) mobileCurrentPage = 1;

                const start = (mobileCurrentPage - 1) * mobileItemsPerPage;
                const end = start + mobileItemsPerPage;
                const pageRows = filteredRows.slice(start, end);

                container.innerHTML = '';

                if (pageRows.length === 0) {
                    container.innerHTML = `<div class="text-center py-4 text-muted"><p class="mb-0">Tidak ada data item.</p></div>`;
                } else {
                    pageRows.forEach((row, idx) => {
                        const cardNum = start + idx + 1;
                        let titleHtml = '';
                        let detailsHtml = '';

                        headerLabels.forEach((label, i) => {
                            if (hiddenFlags[i]) return;
                            const k = keys[i];
                            const val = row[k + '__disp'] || row[k] || '-';
                            const isPrice = priceCols.some(p => p.key === k);

                            if (!titleHtml && (label.toLowerCase() === 'name' || label.toLowerCase() === 'nama' || label.toLowerCase() === 'description')) {
                                titleHtml = `<h6 class="fw-bold text-dark mb-2"><i data-feather="box" class="icon-sm me-1 text-primary"></i>${val}</h6>`;
                            } else {
                                detailsHtml += `
                                    <div class="d-flex justify-content-between align-items-center fs-13px py-1 border-bottom border-light">
                                        <span class="text-secondary">${label}:</span>
                                        <span class="${isPrice ? 'fw-bold text-primary' : 'fw-semibold text-dark'}">${val}</span>
                                    </div>`;
                            }
                        });

                        if (!titleHtml) {
                            titleHtml = `<h6 class="fw-bold text-dark mb-2"><i data-feather="box" class="icon-sm me-1 text-primary"></i>Item #${cardNum}</h6>`;
                        }

                        const cardHtml = `
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-soft-primary px-2 py-1 rounded-pill">#${cardNum}</span>
                                    </div>
                                    ${titleHtml}
                                    ${detailsHtml}
                                </div>
                            </div>`;
                        container.insertAdjacentHTML('beforeend', cardHtml);
                    });
                }

                if (typeof feather !== 'undefined') feather.replace();

                if (pageInfo) {
                    if (totalItems === 0) {
                        pageInfo.textContent = 'Tidak ada data ditemukan';
                    } else {
                        pageInfo.textContent = `Menampilkan ${start + 1}-${Math.min(end, totalItems)} dari ${totalItems} data`;
                    }
                }

                if (pageNav) {
                    pageNav.innerHTML = '';
                    if (totalPages <= 1) return;

                    const prevLi = document.createElement('li');
                    prevLi.className = `page-item ${mobileCurrentPage === 1 ? 'disabled' : ''}`;
                    prevLi.innerHTML = `<a class="page-link" href="javascript:void(0)">&laquo;</a>`;
                    prevLi.addEventListener('click', () => {
                        if (mobileCurrentPage > 1) {
                            mobileCurrentPage--;
                            renderMobileCards();
                        }
                    });
                    pageNav.appendChild(prevLi);

                    // Truncated pagination range logic
                    const pages = [];
                    if (totalPages <= 5) {
                        for (let i = 1; i <= totalPages; i++) pages.push(i);
                    } else {
                        pages.push(1);
                        if (mobileCurrentPage > 3) pages.push('...');
                        const startPage = Math.max(2, mobileCurrentPage - 1);
                        const endPage = Math.min(totalPages - 1, mobileCurrentPage + 1);
                        for (let i = startPage; i <= endPage; i++) {
                            if (!pages.includes(i)) pages.push(i);
                        }
                        if (mobileCurrentPage < totalPages - 2) {
                            if (!pages.includes('...')) pages.push('...');
                        }
                        if (!pages.includes(totalPages)) pages.push(totalPages);
                    }

                    pages.forEach(p => {
                        const li = document.createElement('li');
                        if (p === '...') {
                            li.className = 'page-item disabled';
                            li.innerHTML = `<span class="page-link">&hellip;</span>`;
                        } else {
                            li.className = `page-item ${p === mobileCurrentPage ? 'active' : ''}`;
                            li.innerHTML = `<a class="page-link" href="javascript:void(0)">${p}</a>`;
                            li.addEventListener('click', () => {
                                mobileCurrentPage = p;
                                renderMobileCards();
                            });
                        }
                        pageNav.appendChild(li);
                    });

                    const nextLi = document.createElement('li');
                    nextLi.className = `page-item ${mobileCurrentPage === totalPages ? 'disabled' : ''}`;
                    nextLi.innerHTML = `<a class="page-link" href="javascript:void(0)">&raquo;</a>`;
                    nextLi.addEventListener('click', () => {
                        if (mobileCurrentPage < totalPages) {
                            mobileCurrentPage++;
                            renderMobileCards();
                        }
                    });
                    pageNav.appendChild(nextLi);
                }
            }

            dt.on('draw', function() {
                renderMobileCards();
            });
            renderMobileCards();
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
