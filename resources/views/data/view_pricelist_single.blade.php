@extends('layouts.layout')

@section('title', 'Price List')

@section('css')
  {{-- DataTables + Responsive (jQuery) --}}
  <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" />
  {{-- Jika punya theme responsive css terpisah, aktifkan baris di bawah --}}
  {{-- <link href="{{ URL::asset('/assets/libs/datatables/responsive.bootstrap5.min.css') }}" rel="stylesheet" /> --}}

  <style>
    table.dataTable td, table.dataTable th { white-space: normal; word-break: break-word; }
    .card-price{font-weight:700}
    .card-title{font-size:1rem;margin-bottom:.25rem}
    .card-subtle{color:#0a81e9;font-size:.85rem}
    .sticky-actions{position:sticky;top:.5rem;z-index:3}
  </style>
@endsection

@section('content')
<div class="container-fluid">
  @if ($data->isEmpty())
    <div class="alert alert-warning mb-0">Price list tidak ditemukan atau akses ditolak.</div>
  @else
    @php
      $pl   = $data->first();
      $json = json_decode($pl->datatable_data, true) ?: ['header'=>[], 'data'=>[]];
      $cols = collect($json['header'] ?? []);
      $rows = $json['data']   ?? [];
      // untuk prioritas responsive (yang penting ditampilkan duluan)
      $priorityOrder = ['brand','description','part_no.','price'];
    @endphp

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
      <div>
        <h4 class="mb-1">{{ $pl->title }}</h4>
        <div class="text-muted small">
          Tanggal: {{ \Illuminate\Support\Carbon::parse($pl->date)->format('d M Y') }}
          @if($pl->currency) • Mata uang: {{ $pl->currency->code ?? ($pl->currency->name ?? $pl->currency_id) }} @endif
        </div>
      </div>
      <div class="text-end">
        @if($pl->show_payment_method)
          <div class="small">Metode Pembayaran: {{ $pl->payment_method ?: '-' }}</div>
        @endif
      </div>
    </div>

    @if (!empty($pl->notes))
      <div class="alert alert-info py-2">{!! $pl->notes !!}</div>
    @endif

    {{-- Toolbar global search + tombol Filters --}}
    <div class="row g-2 align-items-center mb-2">
      <div class="col-12 col-md-6">
        <div class="input-group sticky-actions">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input id="globalSearch" type="text" class="form-control" placeholder="Cari apa saja (brand, description, part no., price)">
          <button id="btnToggleFilters" class="btn btn-outline-secondary">Filters</button>
        </div>
      </div>
    </div>

    {{-- ===== Tabel Responsive (+) di kolom pertama ===== --}}
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="priceTable" class="table table-bordered dt-responsive w-100">
            <thead class="table-light">
              <tr>
                <th></th> {{-- kolom tombol + --}}
                @foreach($cols as $col)
                  <th data-id="{{ $col['id'] }}" @if(!empty($col['hidden'])) data-hidden="1" @endif>
                    {{ $col['label'] }}
                  </th>
                @endforeach
              </tr>
              <tr class="filters d-none">
                <th></th> {{-- empty filter untuk kolom tombol + --}}
                @foreach($cols as $col)
                  <th>
                    <input type="text"
                      class="form-control form-control-sm column-search @if(!empty($col['hidden'])) d-none @endif"
                      placeholder="Cari {{ $col['label'] }}">
                  </th>
                @endforeach
              </tr>
            </thead>

            <tbody style="word-wrap:break-word;">
              @foreach($rows as $r)
                <tr>
                  <td></td> {{-- tempat tombol + --}}
                  @foreach($cols as $col)
                    @php $key = $col['id']; $val = $r[$key] ?? ''; @endphp
                    <td>
                      @if($key === 'price')
                        {{ is_numeric($val) ? number_format((float)$val,0,',','.') : $val }}
                      @else
                        {{ $val }}
                      @endif
                    </td>
                  @endforeach
                </tr>
              @endforeach
            </tbody>

            <tfoot>
              @if(!empty($pl->footer_text))
                <tr><td colspan="{{ $cols->count() + 1 }}">{!! $pl->footer_text !!}</td></tr>
              @endif
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  @endif
</div>
@endsection

@push('scripts')
  {{-- Pastikan jQuery sudah di-load di layout sebelum baris ini --}}
  <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
  {{-- Jika responsive JS terpisah, aktifkan baris di bawah --}}
  {{-- <script src="{{ URL::asset('/assets/libs/datatables/dataTables.responsive.min.js') }}"></script> --}}

  <script>
  $(function () {
    const $table = $('#priceTable');
    const $global = $('#globalSearch');
    const $filterRow = $table.find('thead tr.filters');
    const $btnToggle = $('#btnToggleFilters');

    // siapkan columnDefs dari data-hidden & responsive priority
    const ths = $table.find('thead tr:first th').toArray();
    const columnDefs = [];

    // kolom 0 adalah tombol detail
    columnDefs.push({ className: 'dtr-control', orderable: false, targets: 0 });

    // atur visibility dan priority untuk kolom data (mulai index 1)
    const priorityOrder = @json($priorityOrder);
    const idByIndex = ths.map((th, i) => $(th).data('id') || null); // [null, 'id', 'brand', ...]
    for (let i = 1; i < ths.length; i++) {
      const $th = $(ths[i]);
      const hidden = $th.data('hidden') === 1 || $th.data('hidden') === '1';
      const colId = idByIndex[i];

      const def = { targets: i, visible: !hidden };
      // beri prioritas tinggi utk kolom penting agar tampil duluan saat viewport sempit
      if (colId && priorityOrder.includes(colId)) {
        def.responsivePriority = 1; // kecil = lebih prioritas; biar dibuka duluan
      }
      columnDefs.push(def);
    }

    const dt = $table.DataTable({
      responsive: { details: { type: 'column', target: 0 } },
      columnDefs,
      order: [[1, 'asc']],      // urut berdasarkan kolom pertama data (bukan tombol)
      pageLength: 25,
      orderCellsTop: true
    });

    // Global search
    $global.on('keyup change', function(){
      dt.search(this.value).draw();
    });

    // Per-kolom search
    dt.columns().every(function (idx) {
      // idx==0 adalah kolom tombol +, lewati
      if (idx === 0) return;
      const $inp = $filterRow.find('th').eq(idx).find('input');
      if (!$inp.length) return;
      $inp.on('keyup change', () => this.search($inp.val()).draw());
    });

    // Toggle baris filter
    $btnToggle.on('click', function(){
      $filterRow.toggleClass('d-none');
      dt.columns.adjust().draw(false);
    });
  });
  </script>
@endpush
