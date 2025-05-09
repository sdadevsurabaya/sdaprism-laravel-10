<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
    <style>
        th,
        td {
            /* width: 10%; */
            text-align: center !important;
        }
    </style>
</head>

<body>
    <main>
        <section>
            <div class="container mt-5">
                <div class="row mb-2">
                    <div class="col d-flex justify-content-between">
                        <button id="save" class="btn btn-outline-secondary">Kembali</button>
                        <div class="btn-group" role="group" aria-label="Default button group">
                            {{-- <div> --}}
                            <button id="uploadBtn" class="btn btn-outline-success">Upload Excel</button>
                            <input type="file" id="excelFile" style="display: none;" />
                            <button id="addColumn" class="btn btn-outline-info">Tambah Kolom</button>
                            <button id="addRow" class="btn btn-outline-primary">Tambah Baris</button>
                            <button id="deleteSelected" class="btn btn-outline-danger">Hapus</button>
                            <button id="btnSave" class="btn btn-outline-success">Simpan Data</button>
                            <button id="btnPrint" class="btn btn-outline-warning" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop">Print</button>
                            {{-- </div> --}}
                        </div>

                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table align-middle table-bordered table-hover"></table>
                    </div>

                    <div id="result"></div>
                </div>
            </div>

        </section>

    </main>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Print Preview</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="element-to-print">
                        <div class="box">
                            <style type="text/css">
                                /* .box {
                                    max-width: 800px;
                                    height: 100vh;
                                    margin: auto;
                                    font-size: 16px;
                                    line-height: 24px;
                                    font-family: Arial, sans-serif;
                                    color: #222;
                                    padding: 10px 0 10px 0;
                                } */

                                #modal-table-header th {
                                    padding: 0 0.2em;
                                    vertical-align: middle;
                                    border-bottom: 0px;
                                    border-top: 0px;
                                    background-color: #2e2828ad;
                                    color: white;
                                    border: 1px solid white;
                                    text-align: center;
                                    white-space: nowrap;
                                    font-size: 12px;
                                }

                                #modal-table-body th {
                                    border-top: 0px;
                                    border-bottom: 1px solid #dee2e6;
                                    vertical-align: middle;
                                    padding: 0;
                                    /* font-size: 10px; */

                                }

                                #modal-table-body td {
                                    border-top: 0px;
                                    border-bottom: 1px solid #dee2e6;
                                    background-color: #ebebeb;
                                    border: 1px solid #fcfdff;
                                    vertical-align: middle;
                                    font-size: 12px;
                                }

                                .item {
                                    font-size: 10px;
                                    color: #8a2432;
                                }

                                .text-red {
                                    color: #8a2432;
                                }

                                .gradient {
                                    background: linear-gradient(90deg, rgb(227 227 227) 20%, rgb(255 255 255) 85%);
                                }

                                .bt {
                                    border-bottom: solid 1px lightgray;
                                }

                                #modal-table-body td {
                                    vertical-align: top;
                                    padding: 0;
                                    white-space: nowrap;
                                }

                                .col-notes {
                                    width: 30%;
                                    font-size: 10px;
                                    color: #444;
                                    vertical-align: middle;
                                }

                                .col-contact {
                                    width: 28%;
                                    vertical-align: middle;
                                }

                                .col-logo {
                                    width: 12%;
                                    font-size: 16px;
                                    font-weight: bold;
                                    vertical-align: middle;
                                }

                                .col-branding {
                                    width: 5%;
                                    text-align: right;
                                    vertical-align: middle;
                                }

                                .contact {
                                    font-size: 12px;
                                    color: white;
                                    background-color: #8a2432;
                                    padding: 6px 12px;
                                    margin-bottom: 5px;
                                    display: inline-block;
                                    border-radius: 4px;
                                }

                                .contact-wrapper {
                                    display: flex;
                                    flex-direction: column;
                                    gap: 5px;
                                }

                                .branding .sda {
                                    font-size: 24px;
                                    font-weight: bold;
                                }

                                .branding .year {
                                    color: #8a2432;
                                    font-size: 14px;
                                }

                                .vl {
                                    border-left: 2px solid #8a2432;
                                    height: 50px;
                                    align-content: center;
                                }

                                @media print {
                                    @page {
                                        margin: 0 1cm;
                                    }

                                    .page-break {
                                        page-break-before: always;
                                        /* margin-top: 1.8cm !important; */

                                    }

                                    .tr-margin-top {
                                        border: 1px red solid;
                                        margin-top: 1.8cm !important;
                                        margin-bottom: 10cm !important;
                                    }

                                    main {
                                        /* margin: 1.8cm 0; */
                                        /* page-break-inside: auto; */
                                    }

                                    table {
                                        width: 100%;
                                        border-collapse: collapse;
                                        page-break-inside: auto;
                                    }

                                    header {
                                        position: fixed;
                                        top: 0;
                                        left: 0;
                                        right: 0;
                                        height: auto;
                                        background: transparent;
                                        text-align: center;
                                        padding: 10px 0;
                                        /* display: table-header-group; */
                                    }

                                    footer {
                                        position: fixed;
                                        bottom: 0;
                                        left: 0;
                                        right: 0;
                                        height: auto;
                                        background: transparent;
                                        text-align: center;
                                        /* padding: 10px 0;
                                        display: table-footer-group; */
                                    }

                                    table {
                                        border: 1px solid red !important;
                                    }

                                }
                            </style>
                            <div id="print-header" style="left: 0; right: 0;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" align="left">
                                    <tbody>
                                        <tr align="left">
                                            <td width="33%" style="text-align: left;">
                                                <img src="{{ asset('assets/logo/logo-sda-global-24.svg') }}"
                                                    class="img-fluid mb-2" style="width: calc(100px + 7vw);">
                                            </td>
                                            <td width="61%" style="padding-left: 0; text-align: left;">
                                                <div class="vl">
                                                    <p class="m-0 ms-3" style="font-weight: bold;"> PRICE LIST </p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="print-content">
                                <table class="table tr-margin-top" border="0">
                                    <thead id="modal-table-header">
                                        <tr>
                                            <th class=" tr-margin-top">Brand</th>
                                            <th>Description</th>
                                            <th>Part NO.</th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody id="modal-table-body">
                                        <tr>
                                            <td>Sachio</td>
                                            <td>Sachio</td>
                                            <td>Sachio</td>
                                            <td>Sachio</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div id="print-footer">
                                    <table id="print-footer" class="table" border="0">
                                    <tr>
                                        <td class="col-notes gradient">
                                            <strong>Note:</strong><br>
                                            1. Prices are subject to change without prior notice<br>
                                            2. FOB Surabaya or Jakarta<br>
                                            3. Valid until 30 September 2024
                                        </td>
                                        <td class="col-contact p-0 pe-3">
                                            <div class="contact-wrapper">
                                                <div class="contact">☎ Hotline +62 21 9900 8800</div>
                                                <div class="contact">☎ WhatsApp +62 822 0000 8800</div>
                                            </div>
                                        </td>
                                        <td class="col-logo p-0 pe-3">
                                            <p class="m-0" style="font-size: 10px;">Online Store</p>
                                            <img src="https://beta.sda.co.id/assets/img/toko-logo.png"
                                                alt="Toko SDA Logo" width="auto" height="20"><br>
                                        </td>
                                        <td class="col-branding p-0">
                                            <div class="branding">
                                                <div class="d-flex" style="font-size: 8px;line-height: 1.5;">
                                                    <h1 class="m-0 me-2">SDA</h1>
                                                    <span class="vl"></span>
                                                    <h5 class="m-0 text-red ms-2">YEAR<br><b
                                                            style="font-size: 22px;">2025</b></h5>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <form id="form-htmlcontent" action="{{ route('pricelist.pdf') }}" method="POST" class="d-none">
                    @csrf
                    <input type="hidden" id="htmlcontent" name="htmlcontent">
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="print-PDF">Print</button>
                    <button type="button" class="btn btn-primary" id="print-dompPDF">Print dompdf</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
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

        // ===== Utility Functions =====

        // Convert column name to a valid ID format
        function convertNameToID(name) {
            return name.toLowerCase().replace(/\s+/g, '_').replace(/thead\[\]/g, '');
        }

        // Extract ID from column name
        function getIdFromName(name) {
            return name.replace(/thead\[\]/g, '');
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
            const checkbox = `<input type="checkbox" name="${name}thead[]" ${checked}>`;
            const input =
                `<input name="${name}thead[]" type="text" class="form-control theader-change fw-medium border-0 text-center ${hidden ? 'd-none' : ''}" value="${label}" />`;
            return hidden ? checkbox + input : `${checkbox}<hr class="m-0 p-0">` + input;
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
            LoadDataTable(tableHead);

            // Restore data by re-adding rows
            table.clear().draw(); // Clear any residual rows
            currentData.forEach(item => addRow(item)); // Re-add rows
            console.log('Data restored after header update');
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
            if ($.fn.DataTable.isDataTable('#example')) {
                table.destroy(); // Destroy existing DataTable instance
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
                }],
            });

            LoadChangeTrigger(columns); // Bind change events to header inputs
            console.log('DataTable loaded');
        }

        // Bind change events to header inputs
        function LoadChangeTrigger(dtb) {
            dtb.forEach(item => {
                if (item.id !== 'id') {
                    $(`input[name="${item.id}thead[]"][type="checkbox"]`).off('change').on('change', function() {
                        checkHeader(this);
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
                return `<input name="${col.id}[]" id="${col.id}${numrow}" type="text" class="form-control" value="${val}" />`;
            });

            table.row.add(newRow).draw(true);
        }


        // ===== Event Bindings =====

        $(document).ready(function() {
            LoadDataTable(tableHead); // Initialize DataTable

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

                            } else {

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
                        } else {
                            alert("Format data tidak valid.");
                        }
                        $('#excelFile').val(''); // Reset file input
                    },
                    error: function() {
                        alert('Gagal mengimpor file Excel.');
                        $('#excelFile').val(''); // Reset file input
                    }
                });
            });

            // Add a new column to the table
            $('#addColumn').on('click', function() {
                const colName = prompt('Masukkan nama kolom baru:', 'Kolom Baru');
                if (!colName) return;

                const colNameID = convertNameToID(colName);
                const exists = tableHead.some(col => col.id === colNameID);
                if (exists) return alert(`Kolom "${colNameID}" sudah ada!`);

                tableHead.push({
                    id: colNameID,
                    label: colName,
                    hidden: false,
                    checkbox: true
                });

                table.clear().draw(); // Clear table

                LoadDataTable(tableHead); // Reload DataTable

                dataTbd.forEach((item, index) => {
                    item[colNameID] = '';
                    addRow(item);
                });

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
            });

            $('#btnSave').on('click', function() {
                const tbodyInputData = [];
                const visibleCols = tableHead.filter(col => col.checkbox === true);
                const columnsToShow = ['id', ...visibleCols.map(col => col.id)];
                const filteredHeader = tableHead.filter(col => columnsToShow.includes(col.id));

                // table.rows().every(function(index) {
                //     console.log(index);

                //     if (index >= dataTbd.length) return;

                //     const rowNode = $(this.node());
                //     const rowData = {};

                //     const idInput = rowNode.find('input[type="hidden"][name="id[]"]');
                //     rowData['id'] = idInput.length ? idInput.val() : (dataTbd[index]?.id || index +
                //         1);

                //     tableHead.forEach(col => {
                //         if (col.id === 'id') return;

                //         const input = rowNode.find(`[name="${col.id}[]"]`);
                //         if (input.length && input.is(':visible')) {
                //             rowData[col.id] = input.attr('type') === 'checkbox' ? (input.is(
                //                 ':checked') ? "1" : "0") : input.val();
                //             dataTbd[index][col.id] = rowData[col.id];
                //         } else {
                //             rowData[col.id] = dataTbd[index][col.id] || '';
                //         }
                //     });

                //     tbodyInputData.push(rowData);
                // });

                const result = {
                    header: filteredHeader,
                    data: dataTbd.map(row => {
                        const filtered = {};
                        columnsToShow.forEach(colId => {
                            filtered[colId] = row[colId] ?? '';
                        });
                        return filtered;
                    }),
                    // data: tbodyInputData.map(row => {
                    //     const filtered = {};
                    //     columnsToShow.forEach(colId => {
                    //         filtered[colId] = row[colId] ?? '';
                    //     });
                    //     return filtered;
                    // })
                };

                $('#result').html('<pre>' + JSON.stringify(result, null, 2) + '</pre>');
                // console.log('Saved data:', result);
            });

            // Update header data on input change
            $('#example').on('change', 'input.form-control.theader-change', function() {
                const name = $(this).attr('name');
                const value = $(this).val();
                updateDataHeader(name, value);
            });

            $('#deleteSelected').on('click', function() {
                const rowsToRemove = [];

                console.log('checked all :', $('input[type="checkbox"][name="idthead[]"]').is(':checked'));

                if ($('input[type="checkbox"][name="idthead[]"]').is(':checked')) {
                    table.clear().draw(); // Clear table
                    dataTbd = []; // Clear dataTbd
                    console.log('====================================');
                    console.log('All rows deleted');
                    console.log('====================================');
                } else {
                    table.rows().every(function(index) {
                        if (index >= dataTbd.length) return;
                        const rowNode = $(this.node());
                        const checkbox = rowNode.find('input[type="checkbox"][name="checkid[]"]');
                        const idInput = rowNode.find('input[type="hidden"][name="id[]"]');
                        const rowId = idInput.val();

                        if (checkbox.is(':checked')) {
                            rowsToRemove.push(rowId);
                            table.row(rowNode).remove(); // Hapus dari tampilan
                        }
                    });

                    if (rowsToRemove.length === 0) {
                        alert('No rows selected for deletion.');
                        return;
                    }

                    // Hapus dari sumber data utama
                    dataTbd = dataTbd.filter(item => !rowsToRemove.includes(String(item.id)));
                    numrow = dataTbd.length;
                }



                table.draw(); // Refresh DataTable
                console.log('Rows deleted', rowsToRemove);
                console.log('dataTbd updated:', dataTbd);
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

            $('#btnPrint').on('click', function() {
                const modalTableHeader = $('#modal-table-header');
                const modalTableBody = $('#modal-table-body');

                // Clear previous content
                modalTableHeader.empty();
                modalTableBody.empty();

                // Ensure tableHead and dataTbd are defined
                if (typeof tableHead === 'undefined' || typeof dataTbd === 'undefined') {
                    console.error('tableHead or dataTbd is not defined');
                    return;
                }

                // Generate new header and body for the modal
                const headerRow = tableHead.map(col => `<th>${col.label}</th>`).join('');
                modalTableHeader.append(`<tr>${headerRow}</tr>`);

                // dataTbd.forEach(item => {
                //     const row = tableHead.map(col => `<td>${item[col.id] ?? ''}</td>`).join('');
                //     modalTableBody.append(`<tr>${row}</tr>`);
                // });
                dataTbd.forEach((item, index) => {
                    const row = tableHead.map(col => `<td>${item[col.id] ?? ''}</td>`).join('');
                    const extraClass = index % 55 === 0 && index !== 0 ?
                        'page-break tr-margin-top' : '';
                    modalTableBody.append(`<tr class="${extraClass}">${row}</tr>`);
                });



            });

            $('#print-PDF').on('click', function() {
                const elementToPrint = document.getElementById('element-to-print');
                let htmlHeader = document.getElementById('print-header').innerHTML;
                let htmlFooter = document.getElementById('print-footer').innerHTML;
                let htmlContent = document.getElementById('print-content').innerHTML;
                // $('#htmlcontent').val(htmlContent);
                // document.getElementById('form-htmlcontent').submit();
                const newWin = window.open('', '_blank');

                // Get all styles from the modal
                const styles = `
                <style>
                    ${document.querySelector('#element-to-print style').innerHTML}
                    /* Additional styles for print */
                    body { font-family: Arial, sans-serif; }
                    table { width: 100%; border: 0;}
                    img { max-width: 100%; height: auto; }
                    .img-fluid { max-width: 100%; height: auto; }
                    .m-0 { margin: 0; }
                    .ms-3 { margin-left: 1rem; }
                    .p-0 { padding: 0; }
                    .pe-3 { padding-right: 1rem; }
                    .d-flex { display: flex; }
                </style>
                <!-- Include Bootstrap CSS for consistent styling -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            `;

                // Convert relative image paths to absolute
                // let htmlContent = elementToPrint.innerHTML;
                const baseUrl = window.location.origin;
                htmlContent = htmlContent.replace(
                    /src="([^"]+)"/g,
                    (match, src) => {
                        if (src.startsWith('http')) return match; // Already absolute
                        if (src.startsWith('/')) return `src="${baseUrl}${src}"`; // Root-relative
                        return `src="${baseUrl}/${src}"`; // Relative
                    }
                );

                // Write the new document
                newWin.document.write(`
                <html>
                    <head>
                        <title>Print Price List</title>
                        ${styles}
                    </head>
                    <body>
                        <header>
                            ${htmlHeader}
                        </header>
                        <main>
                            ${htmlContent}
                        </main>
                        <footer>
                            ${htmlFooter}
                        </footer>
                    </body>
                </html>
            `);
                newWin.document.close();

                // Wait for images to load before printing
                const images = newWin.document.getElementsByTagName('img');
                let loadedImages = 0;
                const totalImages = images.length;

                if (totalImages === 0) {
                    // No images, print immediately
                    setTimeout(() => {
                        newWin.print();
                        newWin.close();
                    }, 100);
                } else {
                    // Wait for all images to load
                    for (let img of images) {
                        img.onload = () => {
                            loadedImages++;
                            if (loadedImages === totalImages) {
                                setTimeout(() => {
                                    newWin.print();
                                    newWin.close();
                                }, 100);
                            }
                        };
                        img.onerror = () => {
                            console.warn(`Failed to load image: ${img.src}`);
                            loadedImages++;
                            if (loadedImages === totalImages) {
                                setTimeout(() => {
                                    newWin.print();
                                    newWin.close();
                                }, 100);
                            }
                        };
                    }
                }
            });

            // $('#print-PDF').on('click', function() {
            //     const elementToPrint = document.getElementById('element-to-print').innerHTML;
            //     let htmlHeader = document.getElementById('print-header').innerHTML;
            //     let htmlContent = document.getElementById('print-content').innerHTML;
            //     let htmlFooter = document.getElementById('print-footer').innerHTML;

            //     // Buat elemen sementara untuk PDF
            //     const tempDiv = document.createElement('div');
            //     tempDiv.innerHTML = `
        //         <header>
        //             ${htmlHeader}
        //         </header>
        //         <main>
        //             ${htmlContent}
        //         </main>
        //         <footer>
        //             ${htmlFooter}
        //         </footer>
        //     `;
            //     document.body.appendChild(tempDiv);

            //     // Styling untuk header dan footer
            //     const headerStyle = `
        //         <style>
        //             .header {
        //                 position: fixed;
        //                 top: 0;
        //                 width: 100%;
        //                 padding: 10px;
        //                 text-align: center;
        //                 font-family: Arial, sans-serif;
        //                 font-size: 12px;
        //             }
        //             .footer {
        //                 position: fixed;
        //                 bottom: 0;
        //                 width: 100%;
        //                 padding: 10px;
        //                 text-align: center;
        //                 font-family: Arial, sans-serif;
        //                 font-size: 12px;
        //             }
        //             main {
        //                 margin: 30mm 10mm;
        //                 font-family: Arial, sans-serif;
        //             }
        //         </style>
        //     `;

            //     // Konfigurasi html2pdf
            //     const opt = {
            //         margin: [30, 10, 30,
            //             10
            //         ], // Margin: [top, right, bottom, left] untuk header dan footer
            //         filename: 'document.pdf',
            //         image: {
            //             type: 'jpeg',
            //             quality: 0.98
            //         },
            //         html2canvas: {
            //             scale: 2
            //         },
            //         jsPDF: {
            //             unit: 'mm',
            //             format: 'a4',
            //             orientation: 'portrait'
            //         },
            //         pagebreak: {
            //             mode: ['avoid-all', 'css', 'legacy']
            //         }
            //     };

            //     // Generate PDF
            //     html2pdf().set(opt).from(tempDiv).toPdf().get('pdf').then(function(pdf) {
            //         // Dapatkan instance jsPDF
            //         const totalPages = pdf.internal.getNumberOfPages();

            //         // Tambahkan header dan footer ke setiap halaman
            //         for (let i = 1; i <= totalPages; i++) {
            //             pdf.setPage(i);

            //             // Tambahkan header
            //             pdf.setFontSize(12);
            //             pdf.setFont('helvetica', 'normal');
            //             pdf.text(htmlHeader.replace(/<[^>]+>/g, ''), 10,
            //                 15); // Strip HTML tags untuk teks sederhana

            //             // Tambahkan footer
            //             pdf.text(htmlFooter.replace(/<[^>]+>/g, ''), 10, pdf.internal.pageSize
            //                 .height - 15); // Strip HTML tags
            //         }

            //         // Simpan PDF sebagai blob untuk pratinjau
            //         const pdfUrl = pdf.output('bloburl');
            //         const newWin = window.open('', '_blank');

            //         // Tambahkan iframe untuk pratinjau PDF dan tombol cetak
            //         newWin.document.write(`
        //             <html>
        //                 <head><title>PDF Preview</title>${headerStyle}</head>
        //                 <body>
        //                     <div style="margin-bottom: 10px;">
        //                         <button onclick="document.getElementById('pdfFrame').contentWindow.print()">Print PDF</button>
        //                         <button onclick="window.close()">Close</button>
        //                     </div>
        //                     <iframe id="pdfFrame" src="${pdfUrl}" style="width: 100%; height: 90vh;"></iframe>
        //                 </body>
        //             </html>
        //         `);
            //         newWin.document.close();

            //         // Bersihkan elemen sementara
            //         document.body.removeChild(tempDiv);
            //     });
            // });

            $('#print-dompPDF').on('click', function() {
                let htmlContent = document.getElementById('print-content').innerHTML;
                $('#htmlcontent').val(htmlContent);
                document.getElementById('form-htmlcontent').submit();

            });

        });
    </script>
</body>

</html>
