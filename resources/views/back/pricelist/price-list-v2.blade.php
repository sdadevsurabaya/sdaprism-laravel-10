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
            </div>
        </section>
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
    <script>
        $(document).ready(function() {
            // Definisikan tableHead dengan benar
            var tableHead = [{
                    title: '<input type="checkbox" name="check[]" id="check-all">',
                    orderable: false
                },
                {
                    title: '<input type="checkbox" name="check[]" id="check-all"><hr class="m-0 p-0"><input type="text" class="form-control border-0 text-center" value="Name" />',
                    orderable: false
                },
                {
                    title: '<input type="checkbox" name="check[]" id="check-all"><hr class="m-0 p-0"><input type="text" class="form-control border-0 text-center" value="Position" />',
                    orderable: false
                },
                {
                    title: '<input type="checkbox" name="check[]" id="check-all"><hr class="m-0 p-0"><input type="text" class="form-control border-0 text-center" value="Office" />',
                    orderable: false
                },
                {
                    title: '<input type="checkbox" name="check[]" id="check-all"><hr class="m-0 p-0"><input type="text" class="form-control border-0 text-center" value="Age" />',
                    orderable: false
                },
                {
                    title: '<input type="checkbox" name="check[]" id="check-all"><hr class="m-0 p-0"><input type="text" class="form-control border-0 text-center" value="Start date" />',
                    orderable: false
                },
                {
                    title: '<input type="checkbox" name="check[]" id="check-all"><hr class="m-0 p-0"><input type="text" class="form-control border-0 text-center" value="Salary" />',
                    orderable: false
                },
            ];

            // Inisialisasi DataTable
            var table = $('#example').DataTable({
                processing: true,
                // responsive: true,
                columns: tableHead // Langsung gunakan array, bukan dalam array lain
            });

            $('#example').on('click', 'tbody td:not(:first-child)', function(e) {
                editor.inline(this);
            });

            // Event handler untuk checkbox "check all"
            $('#check-all').on('change', function() {
                $('input[name="check[]"]').prop('checked', this.checked);
            });

            // Tombol tambah kolom
            $('#addColumn').on('click', function() {
                var colName = prompt('Masukkan nama kolom baru:', 'Kolom Baru');
                if (!colName) return;

                // Tambahkan kolom baru ke tableHead
                tableHead.push({
                    title: '<input type="checkbox" name="check[]" id="check-all"><hr class="m-0 p-0"><input type="text" class="form-control border-0" value="' +
                        colName + '" />',
                    orderable: false
                });

                // Tambahkan sel kosong ke setiap baris
                // table.rows().every(function() {
                //     this.data().push('<td><input type="text" class="form-control" /></td>'); // Tambahkan data kosong
                //     this.invalidate(); // Invalidasi cache
                //     console.log('====================================');
                //     console.log('Baris baru ditambahkan ke tbody');
                //     console.log('====================================');
                // });

                $('#example tbody tr').each(function() {
                    $(this).append('<td><input type="text" class="form-control" /></td>');
                    console.log('kolom baru ditambahkan ke thead');
                });

                // Destroy dan reinisialisasi DataTable
                table.destroy();
                table = $('#example').DataTable({
                    processing: true,
                    responsive: true,
                    columns: tableHead
                });
            });

            $('#addRow').on('click', function() {
                var colCount = tableHead.length;
                var newRow = [];
                for (let i = 0; i < colCount; i++) {
                    if (i === 0) {
                        newRow.push('<input type="checkbox">');
                    } else {
                        newRow.push('<input type="text" class="form-control" />');
                    }
                }
                table.row.add(newRow).draw();
                console.log('sel baru ditambahkan ke tbody');
            });

            $('#btnSave').on('click', function() {
                var tableDataJson = table.rows().data().toArray();
                console.log('====================================');
                console.log(tableDataJson);
                console.log('====================================');
            });
        });
    </script>
</body>

</html>
