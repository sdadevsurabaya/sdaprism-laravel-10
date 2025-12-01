@extends('layouts.layout')

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Forms</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pricelist</li>
        </ol>
    </nav>
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-body">

                <!-- Tampilkan pesan sukses -->
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <!-- redirect ke halaman pdf -->
                @if (session('open_pdf'))
                    <script>
                        window.open("{{ session('open_pdf') }}", "_blank");
                    </script>
                @endif

                <!-- Tampilkan semua error -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="d-flex justify-content-between">
                    <a href="{{ route('pricelists.index') }}" id="save" class="btn btn-outline-secondary">Kembali</a>
                    <div class="btn-group" role="group" aria-label="Default button group">

                        @if (isset($pricelist))
                            <button id="btnSave" class="btn btn-outline-success"><i class="btn-icon-prepend"
                                    data-feather="save"></i> Update Data & Print <i class="btn-icon-prepend"
                                    data-feather="printer"></i></button>
                        @else
                            <button id="btnSave" class="btn btn-outline-success"><i class="btn-icon-prepend"
                                    data-feather="save"></i> Simpan Data</button>
                        @endif

                    </div>
                </div>
                <h6 class="mt-3 card-title">Input Form Pricelist</h6>
                @if (isset($pricelist))
                    <form id="form-pricelist" class="forms-sample" action="{{ route('pricelists.update', $pricelist->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @method('PUT')
                    @else
                        <form id="form-pricelist" class="forms-sample" action="{{ route('pricelists.store') }}"
                            method="POST" enctype="multipart/form-data">
                @endif
                @csrf
                <div class="mb-3 row">
                    <div class="col">
                        <label class="form-label">Header Logo</label>
                        <select class="form-select text-capitalize" name="header_logo_id" id="header_logo_id">
                            @foreach ($logo as $l)
                                <option value="{{ $l->id }}"
                                    {{ old('header_logo_id', $pricelist->header_logo_id ?? '') == $l->id ? 'selected' : '' }}>
                                    {{ $l->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title"
                            value="{{ old('title', $pricelist->title ?? '') }}">
                    </div>
                </div>
                <div class="mb-3 row">

                    <div class="col-md-6">
                        <label class="form-label">Date</label>
                        <div class="mb-2 input-group flatpickr me-2 mb-md-0" id="dashboardDate">
                            <span class="bg-transparent input-group-text input-group-addon" data-toggle><i
                                    data-feather="calendar" class="text-primary"></i></span>
                            <input type="text" class="bg-transparent form-control" placeholder="Select date" data-input
                                id="date" name="date" value="{{ old('date', $pricelist->date ?? '') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Currency</label>
                        <select class="form-select text-capitalize" name="currency_id" id="currency_id">
                            @foreach ($currency as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('currency_id', $pricelist->currency_id ?? '') == $c->id ? 'selected' : '' }}>
                                    {{ $c->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3 row d-none">
                    <div class="col-md-6">
                        <label class="form-label">Footer Text</label>
                        <input type="text" class="form-control" id="footer_text" name="footer_text"
                            value="{{ old('footer_text', $pricelist->footer_text ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Show Payment Method</label>
                        <select class="form-select" name="show_payment_method" id="show_payment_method">
                            <option value="2"
                                {{ old('show_payment_method', $pricelist->show_payment_method ?? '') == '2' ? 'selected' : '' }}>
                                Tidak</option>
                            <option value="1"
                                {{ old('show_payment_method', $pricelist->show_payment_method ?? '') == '1' ? 'selected' : '' }}>
                                Ya</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control">
                            {{ old('notes', $pricelist->notes ?? '') }}
                        </textarea>
                    </div>
                </div>

                <input type="hidden" class="w-100" id="datatable_data" name="datatable_data"
                    value="{{ old('datatable_data', isset($pricelist) ? $pricelist->datatable_data : '') }}">

                </form>
                <div class="mt-3 mb-3 row">
                    <div class="col d-flex justify-content-end">
                        <div class="btn-group" role="group" aria-label="Default button group">
                            <button id="uploadBtn" class="btn btn-outline-success"><i class="btn-icon-prepend"
                                    data-feather="upload"></i>
                                Upload Excel</button>
                            <input type="file" id="excelFile" style="display: none;" />
                            <button id="addColumn" class="btn btn-outline-info">
                                <i class="btn-icon-prepend" data-feather="plus"></i> Tambah Kolom</button>
                            <button id="addRow" class="btn btn-outline-primary"><i class="btn-icon-prepend"
                                    data-feather="plus"></i> Tambah Baris</button>
                            <button id="deleteSelected" class="btn btn-outline-danger"><i class="btn-icon-prepend"
                                    data-feather="trash"></i> Hapus Baris</button>
                            <button id="clearCurrency" class="btn btn-outline-warning"><i class="btn-icon-prepend"
                                    data-feather="trash-2"></i> Clear Currency</button>
                        </div>

                    </div>

                    {{-- PROGRESS BAR --}}
                    <div class="w-100 mt-3">
                        <!-- Progress upload file -->
                        <div class="progress mb-2 d-none" id="uploadProgressWrapper">
                            <div id="uploadProgress" class="progress-bar" role="progressbar" style="width: 0%">
                                0%
                            </div>
                        </div>

                        <!-- Progress proses import (simbolis, buat UX) -->
                        <div class="progress d-none" id="processProgressWrapper">
                            <div id="processProgress" class="progress-bar" role="progressbar" style="width: 0%">
                                0%
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="example" class="table align-middle table-bordered table-hover"></table>
                    </div>
                    <div id="result"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- <script src="https://code.jquery.com/jquery-3.7.1.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        // Define the initial table header structure
        let tableHead = [{
            id: 'id',
            label: 'ID',
            hidden: true,
            checkbox: false
        }];
        let table; // DataTable instance
        let numrow = 0; // Counter for rows
        let dataTbd = []; // Array to store table data
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        ClassicEditor
            .create(document.querySelector('#notes'))
            .catch(error => {
                console.error(error);
            });

        // ===== Utility Functions =====

        // Convert column name to a valid ID format
        function convertNameToID(name) {
            return name.toLowerCase().replace(/\s+/g, '_').replace(/thead\[\]/g, '');
        }

        // Extract ID from column name
        function getIdFromName(name) {
            return name.replace(/thead\[\]/g, '');
        }

        function removeCurrencyFromPrice(array) {
            // Validate input
            if (!Array.isArray(array)) {
                throw new Error("Input must be an array of objects");
            }

            // Common currency symbols (prefix or suffix)
            const currencySymbols = [
                '$', '€', '£', '¥', 'Rp', 'IDR', 'USD', 'EUR', 'GBP', 'JPY',
                '₹', '₽', 'A$', 'C$', 'CHF', 'kr', 'R$', 'S$'
            ];
            // Regex to match currency symbols, spaces, and separators (commas or dots)
            const currencyRegex = new RegExp(
                `^(${currencySymbols.join('|')})\\s*|\\s*(${currencySymbols.join('|')})$|[\\s,.]`,
                'gi'
            );

            // Process each object in the array
            array.forEach(obj => {
                // Check if price property exists and is a string
                if (typeof obj.price === 'string') {
                    try {
                        // Trim leading and trailing whitespace
                        let trimmedPrice = obj.price.trim();
                        // Remove currency symbols, spaces, and separators, keep decimal point
                        let cleanPrice = trimmedPrice.replace(currencyRegex, '');
                        // Convert to number
                        let numericPrice = parseFloat(cleanPrice);
                        // Validate the result
                        if (isNaN(numericPrice)) {
                            console.warn(`Invalid price format for object: ${JSON.stringify(obj)}`);
                        } else {
                            obj.price = numericPrice;
                        }
                    } catch (error) {
                        console.warn(`Error processing price for object: ${JSON.stringify(obj)}`);
                    }
                } else {
                    console.warn(`Price is not a string for object: ${JSON.stringify(obj)}`);
                }
            });

            return array;
        }

        // Escape HTML characters to prevent XSS
        function escapeHTML(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        // Create column title with checkbox and input field
        function createColumnTitle(name, label, hidden = false, withChecked = false) {
            const checked = withChecked ? 'checked' : '';
            const trashHead =
                `<div class="d-flex justify-content-center w-100"><button id="hapusKolom${name}" class="text-center btn btn-outline-danger"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                </svg></button></div>`;
            const checkbox =
                `<div class="d-flex justify-content-center w-100"><input type="checkbox" name="${name}thead[]" ${checked}></div>`;
            const input =
                `<input name="${name}thead[]" type="text" class="form-control theader-change fw-medium border-0 border-bottom text-center ${hidden ? 'd-none' : ''}" value="${label}" />`;
            return hidden ? checkbox + input : `${trashHead}<hr class="p-1 m-1">${checkbox}<hr class="p-1 m-1">` +
                input;
        }

        // Generate table header based on column data
        function generateTableHead(columnData) {
            return columnData.map(col => ({
                title: createColumnTitle(col.id, col.label, col.hidden, col.checkbox),
                orderable: false,
                id: col.id
            }));
        }

        // Handle checkbox changes in the header
        function checkHeader(thishead) {
            updateDataHeader(thishead.name, null, false, thishead.checked);
        }

        // Update table header data
        function updateDataHeader(inputName, newLabel, isHidden = false, newCheckbox = false) {
            const idhead = newLabel !== null ? convertNameToID(inputName) : getIdFromName(inputName);
            const itemIndex = tableHead.findIndex(item => item.id === idhead);
            if (itemIndex === -1) return;

            const currentItem = tableHead[itemIndex];
            let oldId = currentItem.id; // Store the old ID before updating

            if (newLabel === null) {
                tableHead[itemIndex] = {
                    ...currentItem,
                    checkbox: newCheckbox
                };
            } else {
                const newId = convertNameToID(newLabel);
                tableHead[itemIndex] = {
                    id: convertNameToID(newLabel) ?? currentItem.id,
                    label: newLabel ?? currentItem.label,
                    hidden: currentItem.hidden,
                    checkbox: currentItem.checkbox
                };

                // Update keys in dataTbd if the ID has changed
                if (newId !== oldId) {
                    dataTbd = dataTbd.map(item => {
                        const newItem = {
                            ...item
                        };
                        if (newItem[oldId] !== undefined) {
                            newItem[newId] = newItem[oldId]; // Copy data to new key
                            delete newItem[oldId]; // Remove old key
                        }
                        return newItem;
                    });
                }
            }

            // Reload DataTable with updated headers & rerender rows
            LoadDataTable(tableHead);
            renderAllRows();
            toastr.success('Header updated successfully');
        }

        // Function to delete a column
        function deleteColumn(columnId) {
            if (columnId === 'id') {
                toastr.error('Cannot delete the ID column');
                return;
            }

            const colIndex = tableHead.findIndex(col => col.id === columnId);
            if (colIndex === -1) {
                toastr.error(`Column "${columnId}" not found`);
                return;
            }

            const removedColumn = tableHead.splice(colIndex, 1)[0];

            dataTbd = dataTbd.map(item => {
                const newItem = {
                    ...item
                };
                delete newItem[columnId];
                return newItem;
            });

            LoadDataTable(tableHead);
            renderAllRows();
            toastr.success(`Column "${removedColumn.label}" deleted successfully`);
        }

        // Get the maximum ID from dataTbd
        function getMaxIdFromDataTbd() {
            if (dataTbd.length === 0) return 0; // Return 0 if dataTbd is empty
            const maxId = Math.max(...dataTbd.map(item => parseInt(item.id) || 0));
            return maxId;
        }

        // ===== New helper: build row + renderAllRows =====

        // Build row array for DataTables from data object
        function buildRowData(data = {}) {
            numrow++;

            if (!data.id) {
                data.id = getMaxIdFromDataTbd() + 1;
            }

            return tableHead.map((col, i) => {
                const val = escapeHTML(data[col.id] ?? '');
                if (i === 0) {
                    return `
                        <input type="checkbox" name="checkid[]" id="${col.id}${numrow}" />
                        <input type="hidden" name="id[]" id="hidden${col.id}${numrow}" value="${data.id}" />
                    `;
                }
                return `<input name="${col.id}[]" id="${col.id}${numrow}" type="text" class="form-control isi-datatable" value="${val}" />`;
            });
        }

        // Render all rows from dataTbd into DataTables (batch)
        function renderAllRows() {
            if (!table) return;

            table.clear();

            if (!Array.isArray(dataTbd) || dataTbd.length === 0) {
                table.draw(false);
                return;
            }

            numrow = 0;
            const rows = dataTbd.map(item => buildRowData(item));
            table.rows.add(rows).draw(false);
        }

        // ===== DataTable Operations =====

        // Load DataTable with specified columns
        function LoadDataTable(columns) {
            if ($.fn.DataTable.isDataTable('#example')) {
                table.destroy();
                $('#example').empty();
                numrow = 0;
            }

            if (!columns || !Array.isArray(columns) || columns.length === 0) {
                toastr.error('Cannot initialize DataTable: No valid columns defined');
                return;
            }

            table = $('#example').DataTable({
                processing: true,
                responsive: true,
                searching: false,
                deferRender: true,
                pageLength: 50,
                columns: generateTableHead(columns),
                columnDefs: [{
                    targets: 0,
                    orderable: false,
                    searchable: false,
                }]
            });

            LoadChangeTrigger(columns);
            console.log('DataTable loaded successfully');
        }

        // Bind change events to header inputs
        function LoadChangeTrigger(dtb) {
            dtb.forEach(item => {
                if (item.id !== 'id') {
                    $(`input[name="${item.id}thead[]"][type="checkbox"]`).off('change').on('change', function() {
                        checkHeader(this);
                    });
                    // Bind delete column event
                    $(`button[id="hapusKolom${item.id}"]`).off('click').on('click', function() {
                        if (confirm('Are you sure you want to delete this column? (' + item.label + ')')) {
                            deleteColumn(item.id);
                        }
                    });
                }
            });
        }

        // Add a new row to the table (used by "Tambah Baris" button)
        function addRow(data = {}) {
            if (!table) return;
            const rowData = buildRowData(data);
            table.row.add(rowData).draw(false);
        }

        function updateRowDataTable(id, keys, inputValue) {
            const rowId = Number(id);

            // Validasi id
            if (!dataTbd.some(row => row.id === rowId)) {
                console.warn(`Row with id ${rowId} not found`);
                return dataTbd;
            }

            // Perbarui data
            const updatedData = dataTbd.map(row => {
                if (row.id === rowId) {
                    return {
                        ...row,
                        [keys]: inputValue
                    };
                }
                return row;
            });

            dataTbd = updatedData;
            console.log('Updated row:', dataTbd.find(row => row.id === rowId));
            return dataTbd;
        }

        function filterTableData(header, data, columnsToShow) {
            const filteredHeader = header.filter(col => columnsToShow.includes(col.id));

            return {
                // header: filteredHeader,
                header: header,
                data: data
            };
        }

        function firstLoader() {
            const raw = {!! json_encode(isset($pricelist) ? $pricelist->datatable_data : null) !!};

            let dummyJson = null;
            if (typeof raw === 'string') {
                try {
                    dummyJson = JSON.parse(raw);
                } catch (e) {
                    console.error('Failed to parse datatable_data JSON', e);
                    dummyJson = null;
                }
            } else {
                dummyJson = raw;
            }

            if (dummyJson !== null && Array.isArray(dummyJson.header)) {
                const filteredHeader = dummyJson.header.filter(item => item.id !== 'id');
                tableHead.push(...filteredHeader);
            }

            LoadDataTable(tableHead); // Initialize DataTable

            if (dummyJson !== null && Array.isArray(dummyJson.data)) {
                dataTbd = dummyJson.data;
                renderAllRows();
            }

            @if (isset($pricelist))
                toastr.info('DataTable loaded successfully');
            @endif
        }


        // ===== Event Bindings =====

        $(document).ready(function() {
            firstLoader();

            // Trigger file input when upload button is clicked
            $('#uploadBtn').on('click', () => $('#excelFile').click());

            // Handle file upload and process Excel data + progress bar
            $('#excelFile').on('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('file', file);

                // reset progress bar
                $('#uploadProgressWrapper').removeClass('d-none');
                $('#uploadProgress').css('width', '0%').text('0%');

                $('#processProgressWrapper').removeClass('d-none');
                $('#processProgress').css('width', '0%').text('0%');

                $.ajax({
                    url: '/api/import-excel',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();

                        // progress upload file
                        xhr.upload.addEventListener('progress', function(evt) {
                            if (evt.lengthComputable) {
                                const percent = Math.round((evt.loaded / evt.total) *
                                    100);
                                $('#uploadProgress').css('width', percent + '%').text(
                                    percent + '%');
                            }
                        }, false);

                        return xhr;
                    },
                    beforeSend: function() {
                        $('#processProgress').css('width', '10%').text('Processing...');
                    },
                    success: function(response) {
                        $('#uploadProgress').css('width', '100%').text('100%');

                        if (response.header && response.data) {
                            const newHeaderLabels = response.header;
                            const newDataTBD = response.data;

                            // merge header lama + baru
                            if (Array.isArray(newHeaderLabels) && newHeaderLabels.length > 0) {
                                newHeaderLabels.forEach((newHead) => {
                                    const id = newHead.id;
                                    const exists = tableHead.some(col => col.id === id);

                                    if (!exists) {
                                        tableHead.push({
                                            id: newHead.id,
                                            label: newHead.label,
                                            hidden: newHead.hidden ?? false,
                                            checkbox: newHead.checkbox ?? false
                                        });

                                        if (dataTbd.length > 0) {
                                            dataTbd.forEach(row => {
                                                if (typeof row[id] ===
                                                    'undefined') {
                                                    row[id] = '';
                                                }
                                            });
                                        }
                                    }
                                });
                            }

                            // gabung data lama + baru
                            if (dataTbd.length > 0) {
                                dataTbd.push(...newDataTBD);
                            } else {
                                dataTbd = newDataTBD;
                            }

                            $('#processProgress').css('width', '70%').text(
                            'Rendering table...');

                            LoadDataTable(tableHead);
                            renderAllRows();

                            $('#processProgress').css('width', '100%').text('Done');

                            console.log('File imported, dataTbd updated:', dataTbd);
                            toastr.success('Excel file imported successfully');
                        } else {
                            toastr.error('Invalid data format');
                        }

                        setTimeout(() => {
                            $('#uploadProgressWrapper').addClass('d-none');
                            $('#processProgressWrapper').addClass('d-none');
                        }, 1000);

                        $('#excelFile').val('');
                    },
                    error: function() {
                        toastr.error('Failed to import Excel file');
                        $('#excelFile').val('');

                        setTimeout(() => {
                            $('#uploadProgressWrapper').addClass('d-none');
                            $('#processProgressWrapper').addClass('d-none');
                        }, 1000);
                    }
                });
            });

            // Add a new column to the table
            $('#addColumn').on('click', function() {
                const colName = prompt('Masukkan nama kolom baru:', 'Kolom Baru');
                if (!colName) {
                    toastr.warning('Column name cannot be empty');
                    return;
                }

                const colNameID = convertNameToID(colName);
                const exists = tableHead.some(col => col.id === colNameID);
                if (exists) {
                    toastr.error(`Column "${colNameID}" already exists`);
                    return;
                }

                tableHead.push({
                    id: colNameID,
                    label: colName,
                    hidden: false,
                    checkbox: true
                });

                // Tambahkan field kosong ke setiap data existing
                if (dataTbd.length > 0) {
                    dataTbd.forEach(row => {
                        row[colNameID] = row[colNameID] ?? '';
                    });
                }

                LoadDataTable(tableHead);
                renderAllRows();
                toastr.success(`Column "${colName}" added successfully`);
            });

            // Add a new row to the table
            $('#addRow').on('click', () => {
                const emptyData = {};
                tableHead.forEach(col => {
                    if (col.id === 'id') return;
                    emptyData[col.id] = '';
                });

                dataTbd.push(emptyData); // Add to data array
                addRow(emptyData); // Add to table
                toastr.success(`New row added successfully`);
            });

            $('#btnSave').on('click', function() {
                const visibleCols = tableHead.filter(col => col.checkbox === true);
                const columnsToShow = ['id', ...visibleCols.map(col => col.id)];

                const result = filterTableData(tableHead, dataTbd, columnsToShow);

                $('#datatable_data').val(JSON.stringify(result));
                $('#form-pricelist').submit();
                toastr.success('Data saved successfully');
            });

            $('#deleteSelected').on('click', function() {
                if (confirm('Are you sure you want to delete selected rows?')) {
                    const rowsToRemove = [];

                    console.log('checked all :', $('input[type="checkbox"][name="idthead[]"]').is(
                        ':checked'));

                    if ($('input[type="checkbox"][name="idthead[]"]').is(':checked')) {
                        table.clear().draw(); // Clear table
                        dataTbd = []; // Clear dataTbd
                        toastr.success('All rows deleted successfully');
                    } else {
                        table.rows().every(function(index) {
                            if (index >= dataTbd.length) return;
                            const rowNode = $(this.node());
                            const checkbox = rowNode.find(
                                'input[type="checkbox"][name="checkid[]"]');
                            const idInput = rowNode.find('input[type="hidden"][name="id[]"]');
                            const rowId = idInput.val();

                            if (checkbox.is(':checked')) {
                                rowsToRemove.push(rowId);
                                table.row(rowNode).remove();
                            }
                        });

                        if (rowsToRemove.length === 0) {
                            toastr.warning('No rows selected for deletion');
                            return;
                        }

                        dataTbd = dataTbd.filter(item => !rowsToRemove.includes(String(item.id)));
                        numrow = dataTbd.length;
                        renderAllRows();
                        toastr.success('Selected rows deleted successfully');
                    }

                    table.draw();
                    console.log('Rows deleted', rowsToRemove);
                    console.log('dataTbd updated:', dataTbd);
                }

            });

            $('#clearCurrency').on('click', function() {
                dataTbd = removeCurrencyFromPrice(dataTbd);

                LoadDataTable(tableHead);
                renderAllRows();

                toastr.success('Currency formatting cleared successfully');
            });

            // Update header data on input change
            $('#example').on('change', 'input.form-control.theader-change', function() {
                const name = $(this).attr('name');
                const value = $(this).val();
                updateDataHeader(name, value);
            });

            // Handle header checkbox changes
            $(document).on('change', 'input[type="checkbox"][name="idthead[]"]', function() {
                const checked = this.checked;

                table.rows().every(function() {
                    const rowNode = $(this.node());
                    const checkbox = rowNode.find('input[type="checkbox"][name="checkid[]"]');
                    checkbox.prop('checked', checked);
                });

                table.$('input[type="checkbox"][name="checkid[]"]').prop('checked', checked);
                $.fn.dataTable.ext.checkAllStatus = checked;
            });

            // Update data on row table & dataTbd
            $('#example').on('change', 'input.form-control.isi-datatable', function() {
                const inputId = $(this).attr('id');
                const inputName = $(this).attr('name');
                const inputValue = $(this).val();

                const cleanInputName = inputName.replace('[]', '');

                const id = inputId.replace(cleanInputName, '');

                const keys = cleanInputName;

                console.log(`Input ID: ${inputId} | Value: ${inputValue} | id: ${id} | keys: ${keys}`);
                updateRowDataTable(id, keys, inputValue);
            });

            // Update checkbox status on table redraw
            $('#example').on('draw.dt', function() {
                table.$('input[type="checkbox"][name="checkid[]"]').prop('checked', $(
                    'input[type="checkbox"][name="idthead[]"]').is(':checked'));
            });

        });
    </script>
@endpush
