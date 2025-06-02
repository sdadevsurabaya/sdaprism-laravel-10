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
                    {{-- @dump(session()->all()) --}}
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
                            {{-- <button id="btnPrint" class="btn btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop"><i class="btn-icon-prepend" data-feather="printer"></i>
                                Print</button> --}}
                        @else
                            <button id="btnSave" class="btn btn-outline-success"><i class="btn-icon-prepend"
                                    data-feather="save"></i> Simpan Data</button>
                        @endif

                    </div>
                </div>
                <h6 class="card-title mt-3">Input Form Pricelist</h6>
                @if (isset($pricelist))
                    <form id="form-pricelist" class="forms-sample" action="{{ route('pricelists.update', $pricelist->id) }}"
                        method="POST" enctype="multipart/form-data">
                        @method('PUT')
                    @else
                        <form id="form-pricelist" class="forms-sample" action="{{ route('pricelists.store') }}"
                            method="POST" enctype="multipart/form-data">
                @endif
                @csrf
                <div class="row mb-3">
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
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Footer Text</label>
                        <input type="text" class="form-control" id="footer_text" name="footer_text"
                            value="{{ old('footer_text', $pricelist->footer_text ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date</label>
                        <div class="input-group flatpickr  me-2 mb-2 mb-md-0" id="dashboardDate">
                            <span class="input-group-text input-group-addon bg-transparent" data-toggle><i
                                    data-feather="calendar" class="text-primary"></i></span>
                            <input type="text" class="form-control bg-transparent" placeholder="Select date" data-input
                                id="date" name="date" value="{{ old('date', $pricelist->date ?? '') }}">
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
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
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <textarea id="notes" name="notes" class="form-control" maxlength="100" rows="8"
                            placeholder="This textarea has a limit of 100 chars.">{{ old('notes', $pricelist->notes ?? '') }}</textarea>

                    </div>
                </div>

                <input type="hidden" class="w-100" id="datatable_data" name="datatable_data"
                    value="{{ old('datatable_data', $pricelist->datatable_data ?? '') }}">

                </form>
                <div class="row mt-3 mb-3">
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
            const trashHead = `<div class="d-flex justify-content-center w-100"><button id="hapusKolom${name}" class="btn btn-outline-danger text-center"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                </svg></button></div>`;
            const checkbox =
                `<div class="d-flex justify-content-center w-100"><input type="checkbox" name="${name}thead[]" ${checked}></div>`;
            const input =
                `<input name="${name}thead[]" type="text" class="form-control theader-change fw-medium border-0 border-bottom text-center ${hidden ? 'd-none' : ''}" value="${label}" />`;
            return hidden ? checkbox + input : `${trashHead}<hr class="m-1 p-1">${checkbox}<hr class="m-1 p-1">` +
                input;
        }

        // Generate table header based on column data
        function generateTableHead(columnData) {
            console.log(columnData.length);
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
            // console.log('updateDataHeader:', inputName, newLabel, isHidden, newCheckbox);
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
                    // console.log('Updated dataTbd keys:', oldId, '->', newId);
                }
            }

            // Store current data before reinitializing
            const currentData = dataTbd.slice(); // Copy existing data
            // Reload DataTable with updated headers
            table.clear().draw(); // Clear any residual rows
            LoadDataTable(tableHead);

            // Restore data by re-adding rows
            currentData.forEach(item => addRow(item)); // Re-add rows
            console.log('Data restored after header update');
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

            const currentData = dataTbd.slice();
            table.clear().draw();
            LoadDataTable(tableHead);

            currentData.forEach(item => addRow(item));
            toastr.success(`Column "${removedColumn.label}" deleted successfully`);
        }

        // Get the maximum ID from dataTbd
        function getMaxIdFromDataTbd() {
            if (dataTbd.length === 0) return 0; // Return 0 if dataTbd is empty
            const maxId = Math.max(...dataTbd.map(item => parseInt(item.id) || 0));
            return maxId;
        }

        // ===== DataTable Operations =====

        // Load DataTable with specified columns
        function LoadDataTable(columns) {
            // console.log('Columns passed to DataTable:', JSON.stringify(columns, null, 2));
            // console.log('Generated columns:', JSON.stringify(generateTableHead(columns), null, 2));

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
                columns: generateTableHead(columns),
                columnDefs: [{
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    // className: 'dt-control'
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
                        // toastr.success(`Column "${item.label}" deleted successfully`);
                    });
                }
            });
        }

        // Add a new row to the table
        function addRow(data = {}) {
            numrow++;

            if (!data.id) {
                data.id = getMaxIdFromDataTbd() + 1;
            }

            const newRow = tableHead.map((col, i) => {
                const val = escapeHTML(data[col.id] ?? '');
                if (i === 0) {
                    return `
                        <input type="checkbox" name="checkid[]" id="${col.id}${numrow}" />
                        <input type="hidden" name="id[]" id="hidden${col.id}${numrow}" value="${data.id}" />
                    `;
                }
                return `<input name="${col.id}[]" id="${col.id}${numrow}" type="text" class="form-control isi-datatable" value="${val}" />`;
            });

            table.row.add(newRow).draw(true);
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
                // data: data.map(row => {
                //     const filtered = {};
                //     columnsToShow.forEach(colId => {
                //         filtered[colId] = row[colId] ?? '';
                //     });
                //     return filtered;
                // })
            };
        }

        function firstLoader() {
            // Safely parse JSON (already encoded as JS object, no need for JSON.parse)
            const dummyJson = JSON.parse({!! json_encode($pricelist->datatable_data ?? null) !!});

            // Push to tableHead if header exists
            if (dummyJson !== null && Array.isArray(dummyJson.header)) {
                const filteredHeader = dummyJson.header.filter(item => item.id !== 'id');
                tableHead.push(...filteredHeader);
            }

            LoadDataTable(tableHead); // Initialize DataTable

            // Push to dataTbd if data exists
            if (dummyJson !== null && Array.isArray(dummyJson.data)) {
                dataTbd.push(...dummyJson.data);

                dataTbd.forEach(item => {
                    addRow(item);
                });
            }
            toastr.info('DataTable loaded successfully');

        }


        // ===== Event Bindings =====

        $(document).ready(function() {
            firstLoader();

            // Trigger file input when upload button is clicked
            $('#uploadBtn').on('click', () => $('#excelFile').click());

            // Handle file upload and process Excel data
            $('#excelFile').on('change', function(e) {
                const file = e.target.files[0];
                const formData = new FormData();
                formData.append('file', file);

                $.ajax({
                    url: '/api/import-excel',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.header && response.data) {
                            const newHeaderLabels = response.header;
                            const newDataTBD = response.data;
                            if (tableHead.length > 0) {
                                if (newHeaderLabels.length > tableHead.length) {
                                    newHeaderLabels.forEach((newHead) => {
                                        const id = newHead.id;
                                        const exists = tableHead.some(col => col.id ===
                                            id);

                                        // Jika header baru belum ada di tableHead, tambahkan ke tableHead
                                        if (!exists) {
                                            tableHead.push({
                                                id: newHead.id,
                                                label: newHead.label,
                                                hidden: newHead.hidden ?? false,
                                                checkbox: newHead.checkbox ??
                                                    false
                                            });

                                            // Tambahkan kolom kosong untuk setiap baris data yang sudah ada agar sesuai dengan header baru
                                            if (dataTbd.length > 0) {
                                                dataTbd.forEach(row => {
                                                    row[newHead.id] = '';
                                                });
                                            }
                                        }
                                    });
                                }
                            }

                            // Tambahkan data baru ke dataTbd
                            if (dataTbd.length > 0) {
                                dataTbd.push(...newDataTBD);
                            } else {
                                dataTbd = newDataTBD;
                            }

                            // Reload DataTable with updated headers
                            table.clear().draw();
                            LoadDataTable(tableHead);

                            // Clear and re-add all rows
                            // let newnumRow = 0;
                            dataTbd.forEach(item => {
                                addRow(item)
                            });

                            console.log('File imported, dataTbd updated:', dataTbd);
                            toastr.success('Excel file imported successfully');
                        } else {
                            // alert("Format data tidak valid.");
                            toastr.error('Invalid data format');
                        }
                        $('#excelFile').val(''); // Reset file input
                    },
                    error: function() {
                        // alert('Gagal mengimpor file Excel.');
                        toastr.error('Failed to import Excel file');
                        $('#excelFile').val(''); // Reset file input
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
                // if (exists) return alert(`Kolom "${colNameID}" sudah ada!`);

                tableHead.push({
                    id: colNameID,
                    label: colName,
                    hidden: false,
                    checkbox: true
                });

                LoadDataTable(tableHead); // Reload DataTable
                table.clear().draw(); // Clear table

                dataTbd.forEach((item, index) => {
                    item[colNameID] = '';
                    addRow(item);
                });
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
                // const tbodyInputData = [];
                // const filteredHeader = tableHead.filter(col => columnsToShow.includes(col.id));

                const visibleCols = tableHead.filter(col => col.checkbox === true);
                const columnsToShow = ['id', ...visibleCols.map(col => col.id)];

                var result = filterTableData(tableHead, dataTbd, columnsToShow);

                $('#datatable_data').val(JSON.stringify(result));
                // $('#result').html('<pre>' + JSON.stringify(result) + '</pre>');
                // $('#result').html('<pre>' + JSON.stringify(result, null, 2) + '</pre>');
                $('#form-pricelist').submit();
                // console.log('Saved data:', result);
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
                                table.row(rowNode).remove(); // Hapus dari tampilan
                            }
                        });

                        if (rowsToRemove.length === 0) {
                            // alert('No rows selected for deletion.');
                            toastr.warning('No rows selected for deletion');
                            return;
                        }

                        // Hapus dari sumber data utama
                        dataTbd = dataTbd.filter(item => !rowsToRemove.includes(String(item.id)));
                        numrow = dataTbd.length;
                        toastr.success('Selected rows deleted successfully');
                    }

                    table.draw(); // Refresh DataTable
                    console.log('Rows deleted', rowsToRemove);
                    console.log('dataTbd updated:', dataTbd);
                }

            });

            $('#clearCurrency').on('click', function() {
                dataTbd = removeCurrencyFromPrice(dataTbd);

                // Reload DataTable with updated headers
                table.clear().draw();
                LoadDataTable(tableHead);

                // Clear and re-add all rows
                // let newnumRow = 0;
                dataTbd.forEach(item => {
                    addRow(item)
                });

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
                // console.log('Checkbox header changed:', checked);
            });

            // Update data on row table & dataTbd
            $('#example').on('change', 'input.form-control.isi-datatable', function() {
                const inputId = $(this).attr('id');
                const inputName = $(this).attr('name');
                const inputValue = $(this).val();

                // Hapus [] dari inputName
                const cleanInputName = inputName.replace('[]', ''); // new_1

                // Ambil angka dari inputId dengan menghapus cleanInputName
                const id = inputId.replace(cleanInputName, ''); // 13 (atau 3 sesuai contoh)

                // Ekstrak keys (bagian sebelum angka di inputName)
                const keys = cleanInputName; // new_1 (dinamis)

                console.log(`Input ID: ${inputId} | Value: ${inputValue} | id: ${id} | keys: ${keys}`);
                updateRowDataTable(id, keys, inputValue);
            });

            // Update checkbox status on table redraw
            $('#example').on('draw.dt', function() {
                table.$('input[type="checkbox"][name="checkid[]"]').prop('checked', $(
                    'input[type="checkbox"][name="idthead[]"]').is(':checked'));
                // if ($.fn.dataTable.ext.checkAllStatus === true) {
                //     table.$('input[type="checkbox"][name="checkid[]"]').prop('checked', true);
                // } else {
                //     table.$('input[type="checkbox"][name="checkid[]"]').prop('checked', false);
                // }
            });

        });
    </script>
@endpush
