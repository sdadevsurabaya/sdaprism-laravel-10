@extends('layouts.layout')

@section('title','Price List')

@section('css')
<link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" />
<style>
  /* Bebaskan text table supaya tidak overflow */
  table.dataTable td, table.dataTable th { white-space: normal; word-break: break-word; }
  /* Kartu mobile */
  .card-price{font-weight:700}
  .card-title{font-size:1rem;margin-bottom:.25rem}
  .card-subtle{color:#6c757d;font-size:.85rem}
  .sticky-actions{position:sticky;top:.5rem;z-index:3}
</style>
@endsection

@section('content')
<div class="container-fluid">
  @if($data->isEmpty())
    <div class="alert alert-warning mb-0">Price list tidak ditemukan atau akses ditolak.</div>
  @else
    @php
      $pl   = $data->first();
      $json = json_decode($pl->datatable_data, true) ?: ['header'=>[], 'data'=>[]];
      $cols = collect($json['header'] ?? []);
      $rows = $json['data']   ?? [];
      $mobilePrimary = ['brand','description','part_no.','price'];
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

    @if(!empty($pl->notes))
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

    {{-- ===== MOBILE: Card List (hanya tampil di < md) ===== --}}
    <div id="mobileCards" class="d-block d-md-none">
      <div id="cardsContainer" class="row g-2"></div>
      @if(!empty($pl->footer_text))
        <div class="mt-2">{!! $pl->footer_text !!}</div>
      @endif
    </div>

    {{-- Panel filter per kolom (mobile – collapse) --}}
    <div class="collapse mt-2 d-md-none" id="mobileFilters">
      <div class="card card-body">
        <div class="row g-2">
          @foreach($cols as $i => $col)
            @if(empty($col['hidden']))
              <div class="col-12">
                <label class="form-label small mb-1">{{ $col['label'] }}</label>
                <input type="text" class="form-control form-control-sm mobile-col-filter"
                       data-col-index="{{ $i }}" placeholder="Filter {{ $col['label'] }}">
              </div>
            @endif
          @endforeach
        </div>
      </div>
    </div>

    {{-- ===== DESKTOP: DataTable (hanya tampil di ≥ md) ===== --}}
    <div class="card d-none d-md-block">
      <div class="card-body">
        <div class="table-responsive">
          <table id="priceTable" class="table table-striped table-bordered align-middle w-100">
            <thead>
              <tr>
                @foreach($cols as $col)
                  <th data-id="{{ $col['id'] }}" @if(!empty($col['hidden'])) data-hidden="1" @endif>
                    {{ $col['label'] }}
                  </th>
                @endforeach
              </tr>
              <tr class="filters d-none">
                @foreach($cols as $col)
                  <th>
                    <input type="text" class="form-control form-control-sm column-search @if(!empty($col['hidden'])) d-none @endif"
                           placeholder="Cari {{ $col['label'] }}">
                  </th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach($rows as $r)
                <tr>
                  @foreach($cols as $col)
                    @php $key=$col['id']; $val=$r[$key]??''; @endphp
                    <td>
                      @if($key==='price')
                        {{ number_format((float)$val,0,',','.') }}
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
                <tr><td colspan="{{ $cols->count() }}">{!! $pl->footer_text !!}</td></tr>
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
<script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // data dari server
  const rowsData = @json($rows);
  const headers  = @json($cols->values());
  const mobilePrimary = @json($mobilePrimary);

  const tableEl   = document.getElementById('priceTable');
  const globalEl  = document.getElementById('globalSearch');
  const filterRow = tableEl ? tableEl.querySelector('thead tr.filters') : null;
  const btnToggle = document.getElementById('btnToggleFilters');

  // === breakpoint: mobile < md
  const isMobile = window.matchMedia('(max-width: 767.98px)').matches;

  /* =======================
   *  MOBILE: CARD LIST
   * ======================= */
  (function wireMobileCards(){
    const wrap = document.getElementById('cardsContainer');
    if (!wrap) return;

    function render(list){
      wrap.innerHTML = '';
      list.forEach(row => {
        const brand = row['brand'] ?? '-';
        const desc  = row['description'] ?? '-';
        const part  = row['part_no.'] ?? row['part_no'] ?? '-';
        const price = row['price'] ?? '';
        const priceFmt = (price!=='' && !isNaN(price)) ? new Intl.NumberFormat('id-ID').format(Number(price)) : price;

        const el = document.createElement('div');
        el.className = 'col-12';
        el.innerHTML = `
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div class="me-3">
                  <div class="card-title mb-0">${brand}</div>
                  <div class="card-subtle mb-1">${part}</div>
                </div>
                <div class="card-price">Rp ${priceFmt}</div>
              </div>
              <div class="mt-1">${desc}</div>
              <div class="mt-2 small text-muted">
                ${
                  headers.filter(h => !mobilePrimary.includes(h.id) && !h.hidden)
                         .map(h => `<div><strong>${h.label}:</strong> ${row[h.id] ?? '-'}</div>`).join('')
                }
              </div>
            </div>
          </div>`;
        wrap.appendChild(el);
      });
    }

    function apply(){
      const q = (globalEl?.value || '').toLowerCase();
      const colFilters = {};
      document.querySelectorAll('.mobile-col-filter').forEach(i => {
        colFilters[i.dataset.colIndex] = (i.value || '').toLowerCase();
      });

      const out = rowsData.filter(row => {
        const hay = [row['brand']||'', row['description']||'',
                     row['part_no.']||row['part_no']||'', String(row['price']||'')].join(' ').toLowerCase();
        if (q && !hay.includes(q)) return false;
        for (const [idx,val] of Object.entries(colFilters)) {
          if (!val) continue;
          const key = headers[idx].id;
          const cell = String(row[key] ?? '').toLowerCase();
          if (!cell.includes(val)) return false;
        }
        return true;
      });

      render(out);
    }

    // initial & listeners
    render(rowsData);
    if (globalEl) globalEl.addEventListener('keyup', apply);
    document.querySelectorAll('.mobile-col-filter').forEach(inp => {
      ['keyup','change'].forEach(ev => inp.addEventListener(ev, apply));
    });
  })();

  /* =======================
   *  MOBILE: ONLY collapse
   * ======================= */
  if (isMobile) {
    if (btnToggle) {
      btnToggle.addEventListener('click', function () {
        const mobileCollapse = document.getElementById('mobileFilters');
        if (mobileCollapse && typeof bootstrap !== 'undefined') {
          const c = bootstrap.Collapse.getOrCreateInstance(mobileCollapse);
          c.toggle();
        }
      });
    }
    return; // penting: JANGAN init DataTables di mobile
  }

  /* =======================
   *  DESKTOP: DATATABLES
   * ======================= */
  function buildColumnDefs() {
    const ths = tableEl.querySelectorAll('thead tr:first-child th');
    return Array.from(ths).map((th, idx) => ({
      targets: idx, visible: th.dataset.hidden !== '1'
    }));
  }

  const isV2 = (typeof window.DataTable === 'function') && !window.jQuery;
  const columnDefs = buildColumnDefs();

  if (isV2) {
    const dt = new DataTable(tableEl, {
      pageLength: 25,
      orderCellsTop: true,
      fixedHeader: true,
      columnDefs,
      initComplete: function () {
        const api = this.api();
        tableEl.api = api;

        // filter per kolom (desktop)
        api.columns().every(function () {
          const colIdx = this.index();
          const inp = tableEl.querySelector('thead tr.filters th:nth-child('+(colIdx+1)+') input');
          if (!inp) return;
          ['keyup','change'].forEach(ev => inp.addEventListener(ev, () => {
            this.search(inp.value).draw();
          }));
        });

        // global (desktop)
        if (globalEl) globalEl.addEventListener('keyup', () => api.search(globalEl.value).draw());
      }
    });
  } else if (window.jQuery && typeof jQuery.fn.DataTable === 'function') {
    const $ = window.jQuery;
    const dt = $(tableEl).DataTable({
      pageLength: 25,
      orderCellsTop: true,
      fixedHeader: true,
      columnDefs,
      initComplete: function () {
        const api = this.api();
        tableEl.api = api;

        // per kolom
        api.columns().every(function () {
          const col = this;
          const inp = $(tableEl).find('thead tr.filters th').eq(col.index()).find('input');
          if (!inp.length) return;
          inp.on('keyup change', function () { col.search(this.value).draw(); });
        });

        // global
        if (globalEl) $(globalEl).on('keyup change', function () {
          api.search(this.value).draw();
        });
      }
    });
  }

  // Toggle filters (desktop)
  function toggleFiltersRow() {
    if (!filterRow) return;
    filterRow.classList.toggle('d-none');
    if (tableEl && tableEl.api) {
      try { tableEl.api.columns.adjust().draw(false); }
      catch(e) { try { tableEl.api.draw(false); } catch(_) {} }
    }
  }
  if (btnToggle) btnToggle.addEventListener('click', toggleFiltersRow);
});
</script>
@endpush
