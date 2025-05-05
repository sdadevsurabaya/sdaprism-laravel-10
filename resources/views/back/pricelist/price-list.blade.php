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
                            {{-- </div> --}}
                        </div>

                    </div>
                    <div class="table-responsive">
                        <table id="example" class="table align-middle table-bordered table-hover"></table>
                    </div>

                    <div id="result"></div>
                </div>

        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script>
        let tableHead = [{
            id: 'id',
            label: 'ID',
            hidden: true,
            checkbox: false
        }];
        let table;
        let numrow = 0;
        let dataTbd = [];

        // ===== Utility Functions =====
        function convertNameToID(name) {
            return name.toLowerCase().replace(/\s+/g, '_').replace(/thead\[\]/g, '');
        }

        function getIdFromName(name) {
            return name.replace(/thead\[\]/g, '');
        }

        function escapeHTML(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function createColumnTitle(name, label, hidden = false, withChecked = false) {
            const checked = withChecked ? 'checked' : '';
            const checkbox = `<input type="checkbox" name="${name}thead[]" ${checked}>`;
            const input =
                `<input name="${name}thead[]" type="text" class="form-control border-0 text-center ${hidden ? 'd-none' : ''}" value="${label}" />`;
            return hidden ? checkbox + input : `${checkbox}<hr class="m-0 p-0">` + input;
        }

        function generateTableHead(columnData) {
            return columnData.map(col => ({
                title: createColumnTitle(col.id, col.label, col.hidden, col.checkbox),
                orderable: false,
                id: col.id
            }));
        }

        function updateDataHeader(inputName, newLabel, isHidden = false, newCheckbox = false) {
            const idhead = convertNameToID(inputName);
            const itemIndex = tableHead.findIndex(item => item.id === idhead);
            if (itemIndex === -1) return;

            const currentItem = tableHead[itemIndex];
            if (idhead === "id") {
                tableHead[itemIndex] = {
                    ...currentItem,
                    checkbox: newCheckbox
                };
            } else {
                tableHead[itemIndex] = {
                    id: idhead,
                    label: newLabel ?? currentItem.label,
                    hidden: isHidden,
                    checkbox: newCheckbox
                };
            }
        }

        function checkHeader(thishead) {
            updateDataHeader(thishead.name, null, false, thishead.checked);
        }

        // ===== DataTable Operations =====
        function LoadDataTable(columns) {
            if ($.fn.DataTable.isDataTable('#example')) {
                table.destroy();
            }

            table = $('#example').DataTable({
                processing: true,
                responsive: true,
                searching: false,
                // paging: false,
                columns: generateTableHead(columns),

                columnDefs: [{
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    // render: function(data, type, row) {
                    //     return '<input type="checkbox" name="checkid[]" class="row-checkbox">';
                    // }
                }],
            });

            LoadChangeTrigger(columns);
            console.log('DataTable loaded');
        }

        function LoadChangeTrigger(dtb) {
            dtb.forEach(item => {
                if (item.id !== 'id') {
                    $(`input[name="${item.id}thead[]"][type="checkbox"]`).off().on('change', function() {
                        checkHeader(this);
                    });
                }
            });
        }

        function addRow(data = null) {
            const newRow = [];
            numrow++;

            tableHead.forEach((col, i) => {
                const val = data ? escapeHTML(data[col.id] ?? '') : '';
                if (i === 0) {
                    newRow.push(`
                        <input type="checkbox" name="checkid[]" id="${col.id}${numrow}" />
                        <input type="hidden" name="id[]" id="hidden${col.id}${numrow}" value="${numrow}" />
                    `);
                } else {
                    newRow.push(`
                        <input name="${col.id}[]" id="${col.id}${numrow}" type="text" class="form-control" value="${val}" />
                    `);
                }
            });

            table.row.add(newRow).draw(true);
            console.log('Row added');
        }

        // ===== Event Bindings =====
        $(document).ready(function() {
            LoadDataTable(tableHead);

            $('#uploadBtn').on('click', () => $('#excelFile').click());

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
                            tableHead.push(...response.header);
                            LoadDataTable(tableHead);
                            dataTbd = response.data;
                            dataTbd.forEach(item => addRow(item));
                        } else {
                            alert("Format data tidak valid.");
                        }
                    },
                    error: function() {
                        alert('Gagal mengimpor file Excel.');
                    }
                });
            });

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

                table.clear().draw();

                LoadDataTable(tableHead);

                dataTbd.forEach((item, index) => {
                    item[colNameID] = '';
                    addRow(item);
                });

            });

            // $('#addRow').on('click', () => addRow());
            $('#addRow').on('click', () => {
                // Buat objek kosong berdasarkan struktur tableHead
                const emptyData = {};
                tableHead.forEach(col => {
                    emptyData[col.id] = '';
                });

                dataTbd.push(emptyData); // Tambahkan ke array data
                console.log('tableHead: ', tableHead);
                console.log('dataTbd: ', dataTbd);

                addRow(emptyData); // Tambahkan ke tabel
            });

            $('#btnSave').on('click', function() {
                const tbodyInputData = [];
                const visibleCols = tableHead.filter(col => col.checkbox === true);
                const columnsToShow = ['id', ...visibleCols.map(col => col.id)];
                const filteredHeader = tableHead.filter(col => columnsToShow.includes(col.id));

                // Iterasi semua baris di DataTable
                table.rows().every(function(index) {
                    // Hanya proses baris yang memiliki data di dataTbd
                    if (index >= dataTbd.length) return; // Lewati baris kosong

                    const rowNode = $(this.node());
                    const rowData = {};

                    // Ambil ID dari input hidden atau dataTbd
                    const idInput = rowNode.find('input[type="hidden"][name="id[]"]');
                    rowData['id'] = idInput.length ? idInput.val() : (dataTbd[index]?.id || index +
                        1);

                    // Ambil data dari input di DOM (jika tersedia) atau dari dataTbd
                    tableHead.forEach(col => {
                        if (col.id === 'id') return;

                        const input = rowNode.find(`[name="${col.id}[]"]`);
                        if (input.length && input.is(':visible')) {
                            // Jika input ada dan terlihat di DOM, gunakan nilai dari input
                            rowData[col.id] = input.attr('type') === 'checkbox' ? (input.is(
                                ':checked') ? "1" : "0") : input.val();
                            // Perbarui dataTbd untuk sinkronisasi
                            dataTbd[index][col.id] = rowData[col.id];
                        } else {
                            // Jika input tidak ada (baris di halaman lain), gunakan nilai dari dataTbd
                            rowData[col.id] = dataTbd[index][col.id] || '';
                        }
                    });

                    tbodyInputData.push(rowData);
                });

                // Filter data sesuai kolom yang terlihat
                const result = {
                    header: filteredHeader,
                    data: tbodyInputData.map(row => {
                        const filtered = {};
                        columnsToShow.forEach(colId => {
                            filtered[colId] = row[colId] ?? '';
                        });
                        return filtered;
                    })
                };

                // Tampilkan hasil sebagai JSON
                $('#result').html('<pre>' + JSON.stringify(result, null, 2) + '</pre>');
                console.log('Saved data:', result);
            });

            // $('#btnSave').on('click', function() {
            //     const tbodyInputData = [];
            //     const visibleCols = tableHead.filter(col => col.checkbox === true);
            //     const columnsToShow = ['id', ...visibleCols.map(col => col.id)];
            //     const filteredHeader = tableHead.filter(col => columnsToShow.includes(col.id));

            //     table.rows().every(function() {
            //         const row = $(this.node());
            //         const rowData = {};
            //         const idInput = row.find('input[type="hidden"][name="id[]"]');
            //         rowData['id'] = idInput.val();

            //         tableHead.forEach(col => {
            //             if (col.id === 'id') return;
            //             const input = row.find(`[name="${col.id}[]"]`);
            //             rowData[col.id] = input.attr('type') === 'checkbox' ? (input.is(
            //                 ':checked') ? "1" : "0") : input.val();
            //         });

            //         tbodyInputData.push(rowData);
            //     });

            //     const result = {
            //         header: filteredHeader,
            //         data: tbodyInputData.map(row => {
            //             const filtered = {};
            //             columnsToShow.forEach(colId => filtered[colId] = row[colId] ?? '');
            //             return filtered;
            //         })
            //     };

            //     $('#result').html('<pre>' + JSON.stringify(result, null, 2) + '</pre>');
            // });

            $('#example').on('change', 'input.form-control', function() {
                const name = $(this).attr('name');
                const value = $(this).val();
                updateDataHeader(name, value);
            });

            $('#deleteSelected').on('click', function() {
                const rows = table.rows().nodes();
                for (let i = rows.length - 1; i >= 0; i--) {
                    const row = $(rows[i]);
                    const checkbox = row.find('input[type="checkbox"][name="checkid[]"]');
                    if (checkbox.is(':checked')) {
                        table.row(row).remove();
                    }
                }
                table.draw();
            });

            $(document).on('change', 'input[type="checkbox"][name="idthead[]"]', function() {
                const checked = this.checked;

                // Loop melalui semua baris di semua halaman
                table.rows().every(function() {
                    const rowNode = $(this.node());
                    const checkbox = rowNode.find('input[type="checkbox"][name="checkid[]"]');
                    checkbox.prop('checked', checked);
                });

                // Pastikan status checkbox di halaman saat ini juga terupdate
                table.$('input[type="checkbox"][name="checkid[]"]').prop('checked', checked);

                // Simpan status checkbox header untuk digunakan saat ganti halaman
                $.fn.dataTable.ext.checkAllStatus = checked;

                // Log untuk debugging
                console.log('Checkbox header changed:', checked);
            });

            // Perbarui status checkbox saat DataTable dirender ulang (misalnya, saat ganti halaman)
            $('#example').on('draw.dt', function() {
                if ($.fn.dataTable.ext.checkAllStatus === true) {
                    // Jika header checkbox dicentang, pastikan semua checkbox di halaman baru juga dicentang
                    table.$('input[type="checkbox"][name="checkid[]"]').prop('checked', true);
                }
            });

        });
    </script>
</body>

</html>
