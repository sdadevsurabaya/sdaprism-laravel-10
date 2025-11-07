@extends('layouts.layout')

<style>
    table.dataTable th.dt-type-numeric,
    table.dataTable th.dt-type-date,
    table.dataTable td.dt-type-numeric,
    table.dataTable td.dt-type-date {
        text-align: left !important;
    }
</style>

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Data</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pricelist</li>
        </ol>
    </nav>

    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex w-100 justify-content-end">

                    {{-- Tombol Add New hanya untuk admin --}}
                    @if (Auth::user()->rolesUsers->first()?->roles->name === 'admin')
                        <a href="{{ route('pricelists.create') }}" type="button"
                            class="btn btn-outline-primary btn-icon-text me-2 mb-2 mb-md-0">
                            <i class="btn-icon-prepend" data-feather="plus"></i>
                            Add New
                        </a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table id="pricelist" class="table table-striped align-middle w-100 nowrap">
                        <thead class="text-start">
                            <tr>
                                <th class="dtr-control" data-priority="5">No</th> {{-- toggle + nomor --}}
                                <th data-priority="3">Date</th>
                                <th data-priority="1">Title</th> {{-- paling penting --}}
                                <th data-priority="4">Create By</th>
                                <th data-priority="2">Action</th> {{-- tetap tampil --}}
                            </tr>
                        </thead>
                        <tbody class="text-start">
                            @php $no = 1; @endphp
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->date }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td>
                                        <a href="{{ route('pricelists.show', $item->id) }}"
                                            class="btn btn-sm btn-primary btn-icon-text">
                                            <i data-feather="list" class="btn-icon-prepend"></i> List
                                        </a>
                                        @if (Auth::user()->rolesUsers->first()?->roles->name === 'admin')
                                            <a href="{{ route('pricelists.edit', $item->id) }}"
                                                class="btn btn-sm btn-primary btn-icon-text">
                                                <i data-feather="edit" class="btn-icon-prepend"></i> Edit
                                            </a>
                                        @endif
                                        <a href="{{ route('pricelist.pdf', $item->id) }}"
                                            class="btn btn-sm btn-primary btn-icon-text" target="_blank">
                                            <i data-feather="file" class="btn-icon-prepend"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dt = new DataTable('#pricelist', {
                responsive: {
                    details: {
                        type: 'column',
                        target: 0,
                        // Tampilkan HANYA kolom yang hidden (tanpa duplikasi kolom yang masih terlihat)
                        renderer: function(api, rowIdx, columns) {
                            const rows = columns
                                .filter(col => col.hidden)
                                .map(col => (
                                    '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col
                                    .columnIndex + '">' +
                                    '<td class="fw-semibold pe-3">' + col.title + ':</td>' +
                                    '<td>' + col.data + '</td>' +
                                    '</tr>'
                                ))
                                .join('');

                            // kalau tidak ada kolom hidden, jangan render apa-apa
                            return rows ? $('<table class="table table-sm mb-0"><tbody/>').append(
                                rows) : false;
                        }
                    }
                },
                columnDefs: [{
                        targets: 0,
                        className: 'dtr-control',
                        orderable: false
                    }, // kolom toggle
                    {
                        targets: 2,
                        responsivePriority: 1
                    }, // Title tetap prioritas utama
                    {
                        targets: 4,
                        responsivePriority: 2
                    }, // Action prioritas tinggi
                    {
                        targets: 1,
                        responsivePriority: 3
                    }, // Date
                    {
                        targets: 3,
                        responsivePriority: 4
                    } // Create By
                ],
                order: [
                    [1, 'desc']
                ],
                pagingType: 'simple_numbers',
                autoWidth: false
            });

            // pastikan search kosong tiap reload
            dt.search('').draw();
        });
    </script>
@endpush
