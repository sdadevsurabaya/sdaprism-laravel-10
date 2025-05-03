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
                            <button id="addColumn" class="btn btn-outline-info">Tambah Kolom</button>
                            <button id="addRow" class="btn btn-outline-primary">Tambah Baris</button>
                            <button id="deleteSelected" class="btn btn-outline-danger">Hapus</button>
                            <button id="btnSave" class="btn btn-outline-success">Simpan Data</button>
                        </div>
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
        // Fungsi buat header kolom
        function createColumnTitle(name, label, hidden = false, whitChecked = false) {
            const checked = whitChecked ? 'checked' : '';
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

        function convertNameToID(name) {
            return name.toLowerCase().replace(/\s+/g, '_').replace(/thead\[\]/g, '');
        }

        function getIdFromName(name) {
            return name.replace(/thead\[\]/g, '');
        }

        $(document).ready(function() {

            var tableHead = [{
                    id: 'id',
                    label: 'ID',
                    hidden: true,
                    checkbox: false
                },
                {
                    id: 'name',
                    label: 'Name',
                    hidden: false,
                    checkbox: true
                }
            ];

            var table = $('#example').DataTable({
                processing: true,
                responsive: true,
                searching: false,
                columns: generateTableHead(tableHead)
            });

            LoadChangeTrigger(tableHead);

            function LoadChangeTrigger(dtb) {
                dtb.forEach(item => {
                    if (item.id !== 'id') {
                        $('input[name="' + item.id + 'thead[]"][type="checkbox"]').on('change', function() {
                            console.log('checkbox checked : ' + this.name);
                            // $('input[name="id[]"]').prop('checked', this.checked);
                            checkHeader(this);
                        });
                    }

                });
            }

            function LoadDataTable(dtb) {
                // Destroy dan reinisialisasi DataTable
                table.destroy();
                table = $('#example').DataTable({
                    processing: true,
                    responsive: true,
                    searching: false,
                    columns: generateTableHead(dtb)
                });

                LoadChangeTrigger(dtb);
            }

            function checkHeader(thishead) {
                console.log('header checked: ' + thishead.checked);
                updateDataHeader(thishead.name, null, false, thishead.checked);
            }

            // Event handler untuk checkbox "check all"
            $('input[name="idthead[]"]').on('change', function() {
                if (this.checked) {
                    // Jika checkbox "check all" dicentang, centang semua checkbox di tbody
                    $('input[name="id[]"]').prop('checked', true);
                    console.log('checkbox all checked true');
                } else {
                    // Jika tidak, hapus centang semua checkbox di tbody
                    $('input[name="id[]"]').prop('checked', false);
                    console.log('checkbox all checked false');
                }
            });

            // Tombol tambah kolom
            $('#addColumn').on('click', function() {
                var colName = prompt('Masukkan nama kolom baru:', 'Kolom Baru');
                if (!colName) return;

                // Convert to lowercase and replace spaces with underscores
                const colNameID = convertNameToID(colName);
                // const colNameID = colName.toLowerCase().replace(/\s+/g, '_');
                // ✅ Cek apakah ID kolom sudah ada
                const exists = tableHead.some(col => col.id === colNameID);
                if (exists) {
                    alert(`Kolom dengan ID "${colNameID}" sudah ada!`);
                    return;
                }

                tableHead.push({
                    id: colNameID,
                    label: colName,
                    hidden: false,
                    checkbox: true
                });

                let numnew = 0;

                $('#example tbody tr').each(function() {
                    numnew++;
                    $(this).append('<td><input name="' + colNameID + '[]" id="' + colNameID +
                        numnew + '" type="text" class="form-control" /></td>');
                    console.log('kolom baru ditambahkan ke thead');
                });

                LoadDataTable(tableHead);
            });

            let num = 0;
            let numrow = 0;

            $('#addRow').on('click', function() {
                const colCount = tableHead.length;
                const newRow = [];
                numrow++;

                for (let i = 0; i < colCount; i++) {
                    const colId = tableHead[i].id;

                    if (i === 0) {
                        // Kolom pertama: checkbox + hidden ID
                        newRow.push(`
                <input type="checkbox" name="checkid[]" id="${colId}${numrow}" />
                <input type="hidden" name="id[]" id="hidden${colId}${numrow}" value="${numrow}" />
            `);
                    } else {
                        // Kolom lainnya: input text
                        newRow.push(`
                <input name="${colId}[]" id="${colId}${numrow}" type="text" class="form-control" value="${i}" />
            `);
                    }
                }

                table.row.add(newRow).draw();
                console.log('Baris baru ditambahkan ke tbody');
            });

            // let numrow = 0;
            // $('#addRow').on('click', function() {
            //     var colCount = tableHead.length;
            //     var newRow = [];
            //     numrow++;
            //     for (let i = 0; i < colCount; i++) {
            //         console.log(tableHead[i].id);
            //         if (i === 0) {
            //             newRow.push('<input type="checkbox" name="id[]" id="' + tableHead[i].id +
            //                 numrow +
            //                 '" /><input type="hidden" name="id[]" id="hidden' + tableHead[i].id +
            //                 numrow +
            //                 '" value="' + numrow + '"/>');
            //         } else {
            //             newRow.push('<input name="' + tableHead[i].id + '[]" id="' + tableHead[i].id +
            //                 numrow +
            //                 '" type="text" class="form-control" value="' + i + '" />');
            //         }
            //     }
            //     table.row.add(newRow).draw();
            //     console.log('sel baru ditambahkan ke tbody');
            // });

            $('#btnSave').on('click', function() {
                var headerCheck = [];
                var headerValues = [];
                var tbodyInputData = [];

                $('#result').html('');

                console.log('====================================');
                console.log(numrow);
                console.log('====================================');
                // get header checked
                $('input[name="theader[]"]').each(function() {
                    if ($(this).attr('type') === 'checkbox') {
                        // Untuk checkbox, simpan status checked (true/false)
                        headerCheck.push($(this).is(':checked'));
                        // console.log('get header checkbox status: ' + $(this).is(':checked'));
                    } else {
                        // Untuk input teks, simpan nilai
                        headerCheck.push($(this).val());
                        // console.log('get header value: ' + $(this).val());
                    }
                });

                // get header value
                let headobjval = {};
                tableHead.forEach((keys, index) => {
                    console.log('keys: ' + keys.id);
                    let headarr = [];
                    $('input[name="' + keys.id + 'thead[]"]').each(function() {
                        console.log('get header value: ' + $(this).val());
                        headarr.push($(this).val());
                    });
                    headobjval[keys.id] = headarr;
                });
                headerValues.push(headobjval);

                tbodyInputData.length = 0;
                // print row
                table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                    const row = $(this.node());
                    const objHead = {};

                    // Ambil nilai id dari hidden input (asumsi di kolom pertama)
                    const idHiddenInput = row.find('input[type="hidden"][name="id[]"]');
                    objHead["id"] = idHiddenInput.val();

                    // Loop untuk ambil data dari setiap kolom lain
                    tableHead.forEach((col) => {
                        if (col.id === 'id') return; // Sudah ditangani di atas

                        const input = row.find(`[name="${col.id}[]"]`);
                        if (input.length) {
                            if (input.attr('type') === 'checkbox') {
                                objHead[col.id] = input.is(':checked') ? "1" : "0";
                            } else {
                                objHead[col.id] = input.val();
                            }
                        }
                    });

                    tbodyInputData.push(objHead);
                });

                console.log('input data: ' + JSON.stringify(tbodyInputData, null, 2));

                // var tableDataJson = {
                //     header: tableHead,
                //     data: tbodyInputData,
                //     // data: table.rows().data().toArray(),
                // };

                const visibleColumns = tableHead.filter(col => col.checkbox === true);

                // Buat array `columnsToShow` yang mencakup 'id' dan kolom checkbox
                const columnsToShow = ['id', ...visibleColumns.map(col => col.id)];

                // Saring header yang hanya berisi kolom checkbox yang true
                const filteredHeader = tableHead.filter(col => columnsToShow.includes(col.id));

                // Saring data di tbody sesuai dengan kolom yang akan ditampilkan
                const filteredData = tbodyInputData.map(rowData => {
                    const filteredRow = {};
                    columnsToShow.forEach(id => {
                        filteredRow[id] = rowData[id] || '';
                    });
                    return filteredRow;
                });

                // Gabungkan ke dalam tableDataJson
                var tableDataJson = {
                    header: filteredHeader,
                    data: filteredData
                };

                console.log('====================================');
                console.log(JSON.stringify(tableDataJson, null, 2));
                console.log('====================================');
                $('#result').append('<pre>' + JSON.stringify(tableDataJson, null, 2) + '</pre>');
            });

            $('#example').on('change', 'input.form-control', function() {
                const inputName = $(this).attr('name'); // Use name attribute, not value
                const inputValue = $(this).val();

                console.log(`${inputName} | ${inputValue}`);

                // Call update function
                updateDataHeader(inputName, inputValue, false, false);
            });

            $('#deleteSelected').on('click', function() {
                // Ambil semua baris di DataTable
                const rows = table.rows().nodes();

                // Loop dari belakang untuk mencegah index shift saat hapus
                for (let i = rows.length - 1; i >= 0; i--) {
                    const row = $(rows[i]);

                    // Checkbox ada di kolom pertama (asumsi kamu menaruhnya di kolom pertama)
                    const checkbox = row.find('input[type="checkbox"][name="checkid[]"]');

                    if (checkbox.length && checkbox.is(':checked')) {
                        // Hapus baris dari DataTable
                        table.row(row).remove();
                        console.log(`Baris ke-${i + 1} dihapus`);
                    }
                }

                // Redraw table setelah semua baris dihapus
                table.draw();

                // Reset ulang numrow jika perlu
                // numrow = table.rows().count();
            });


            function updateDataHeader(inputName, newLabel, isHidden = false, newCheckbox = false) {
                // Tentukan ID berdasarkan inputName atau newLabel
                var idhead = convertNameToID(inputName);

                // Cari index item di tableHead
                const itemIndex = tableHead.findIndex(item => item.id === idhead);
                if (itemIndex === -1) {
                    console.warn(`ID ${idhead} tidak ditemukan di tableHead`);
                    return;
                }

                const currentItem = tableHead[itemIndex];

                // Jika id adalah "id", hanya update checkbox saja
                if (idhead === "id") {
                    tableHead[itemIndex] = {
                        ...currentItem,
                        checkbox: newCheckbox
                    };
                } else {
                    // Update item secara penuh
                    tableHead[itemIndex] = {
                        id: newLabel !== null ? convertNameToID(newLabel) : currentItem.id,
                        label: newLabel !== null ? newLabel : currentItem.label,
                        hidden: isHidden,
                        checkbox: newCheckbox
                    };
                }

                // Reload table dengan header baru
                LoadDataTable(tableHead);
            }
        });
    </script>
</body>

</html>
