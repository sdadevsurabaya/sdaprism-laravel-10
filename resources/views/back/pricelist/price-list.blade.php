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
        th {
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
                    $('input[name="' + item.id + 'thead[]"][type="checkbox"]').on('change', function() {
                        console.log('checkbox all checked');
                        // $('input[name="id[]"]').prop('checked', this.checked);
                        checkHeader(this);
                    });
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
                if (thishead === 'id') {
                    return true;
                } else {
                    return false;
                }
            }

            // Event handler untuk checkbox "check all"
            $('input[name="idthead[]"]').on('change', function() {
                // console.log('checkbox all checked');
                $('input[name="id[]"]').prop('checked', this.checked);
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
                    checkbox: false
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
                var colCount = tableHead.length;
                var newRow = [];
                numrow++;
                for (let i = 0; i < colCount; i++) {
                    console.log(tableHead[i].id);
                    if (i === 0) {
                        newRow.push('<input type="checkbox" name="id[]" id="' + tableHead[i].id +
                            numrow +
                            '" />');
                    } else {
                        newRow.push('<input name="' + tableHead[i].id + '[]" id="' + tableHead[i].id +
                            numrow +
                            '" type="text" class="form-control" value="' + i + '" />');
                    }
                }
                table.row.add(newRow).draw();
                console.log('sel baru ditambahkan ke tbody');
            });

            $('#btnSave').on('click', function() {
                var headerCheck = [];
                var headerValues = [];
                var tbodyInputData = [];

                $('#result').html('');
                // $('#result').append('<pre>' + JSON.stringify(tableHead, null, 2) + '</pre><br><hr><br>');


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

                // print row
                for (let numx = 0; numx < numrow; numx++) {
                    console.log('row ke: ' + numx);
                    let objHead = {};
                    tableHead.forEach((keys, index) => {
                        // let objval = [];
                        console.log('keys: ' + keys.id);
                        $('input[name="' + keys.id + '[]"]').each(function() {
                            // objval.push($(this).val());
                            objHead[keys.id] = $(this).val();
                            if ($(this).attr('type') === 'checkbox') {
                                // Untuk checkbox, simpan status checked (true/false)
                                objHead[keys.id] = $(this).is(':checked');
                                // console.log('get header checkbox status: ' + $(this).is(':checked'));
                            } else {
                                // Untuk input teks, simpan nilai
                                objHead[keys.id] = $(this).val();
                                // console.log('get header value: ' + $(this).val());
                            }
                        });
                    });

                    tbodyInputData.push(objHead);
                }

                console.log('input data: ' + JSON.stringify(tbodyInputData, null, 2));

                var tableDataJson = {
                    header: tableHead,
                    data: tbodyInputData,
                    // data: table.rows().data().toArray(),
                };
                console.log('====================================');
                // console.log(tableDataJson);
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

            function updateDataHeader(inputName, newLabel, isHidden = false, newCheckbox = false) {
                // Convert inputName to ID
                var idhead = convertNameToID(inputName);

                // Validate that idhead exists in tableHead
                const itemExists = tableHead.some(item => item.id === idhead);
                if (!itemExists) {
                    console.warn(`ID ${idhead} tidak ditemukan di tableHead`);
                    return;
                }

                // Update tableHead dynamically
                const updatedTableHead = tableHead.map(item => {
                    if (item.id === idhead) {
                        return {
                            id: convertNameToID(newLabel), // Keep ID consistent
                            label: newLabel, // Update label from input value
                            hidden: isHidden, // Update hidden from parameter
                            checkbox: newCheckbox // Update checkbox from parameter
                        };
                    }
                    return item;
                });

                // Assign back to tableHead
                tableHead = updatedTableHead;

                // Reload table
                LoadDataTable(tableHead);
            }
        });
    </script>
</body>

</html>
