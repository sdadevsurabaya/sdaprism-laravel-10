@extends('back/layouts.layout')
@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <div class="d-flex flex-column flex-column-fluid">

                {{-- header-start --}}
                <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                Customers Add</h1>
                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                <li class="breadcrumb-item text-muted">
                                    <a href="../../demo1/dist/index.html" class="text-muted text-hover-primary">Master</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                </li>
                                <li class="breadcrumb-item text-muted">Customers</li>
                            </ul>
                        </div>

                    </div>
                </div>
                {{-- header-end --}}

                {{-- body-start --}}
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <!--begin::Content container-->
                    <div id="kt_app_content_container" class="app-container container-xxl">
                        <!--begin::Products-->
                        <div class="card card-flush">
                            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <!--begin::Search-->

                                    <!--end::Search-->
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <form action="{{ route('quotations.store') }}" method="POST" enctype="multipart/form-data">

                                        @csrf

                                            <h1 class="h4 mb-4">Transaction <span class="text-muted">Quotation</span></h1>
                                            <div class="d-flex justify-content-between mb-4">
                                                <button class="btn btn-primary">Add New Customer</button>
                                                <button class="btn btn-primary">Change Customer</button>
                                            </div>
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-4">
                                                    <label class="form-label">No.</label>
                                                    <input type="text" class="form-control" value="012/BD-IPL/02/2025">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Address Letter</label>
                                                    <select class="form-select">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Date</label>
                                                    <input type="date" class="form-control" value="2025-02-27">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Customer</label>
                                                    <input type="text" class="form-control" value="Khong Guan Corporation">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Address</label>
                                                    <input type="text" class="form-control" value="30068 Eigenbrodt Way Union City">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Valid Until</label>
                                                    <input type="date" class="form-control" value="2025-02-27">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Phone</label>
                                                    <input type="text" class="form-control" value="5104877800">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Contact Person</label>
                                                    <input type="text" class="form-control" value="Mr Albert, Mr James, Mrs Martina">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Payment Type</label>
                                                    <select class="form-select">
                                                        <option></option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Currency</label>
                                                    <select class="form-select">
                                                        <option></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#productModal">
                                                Choose Product
                                            </button>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>No.</th>
                                                            <th>Descriptions</th>
                                                            <th>Unit</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>
                                                            <th>Price before discount</th>
                                                            <th>Disc Percent (%)</th>
                                                            <th>Disc Price Total</th>
                                                            <th>Disc Price / Unit</th>
                                                            <th>Nett</th>
                                                            <th>Remove</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>1</td>
                                                            <td>BaliCafé Arabica Gold RB 500g Bag</td>
                                                            <td>
                                                                <select class="form-select">
                                                                    <option>PC / PCS</option>
                                                                </select>
                                                            </td>
                                                            <td><input type="number" class="form-control" value="1"></td>
                                                            <td><input type="number" class="form-control" value="0"></td>
                                                            <td><input type="number" class="form-control" value="0"></td>
                                                            <td><input type="number" class="form-control" value="0"></td>
                                                            <td><input type="number" class="form-control" value="0"></td>
                                                            <td><input type="number" class="form-control" value="0"></td>
                                                            <td><input type="number" class="form-control" value="0"></td>
                                                            <td class="text-center"><button class="btn btn-link text-danger"><i class="fas fa-times"></i></button></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="row g-3 mt-4">
                                                <div class="col-md-6">
                                                    <label class="form-label">In Words</label>
                                                    <textarea class="form-control" rows="3"></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Descriptions</label>
                                                    <textarea class="form-control" rows="3" placeholder="Input your descriptions here....."></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Account Options</label>
                                                    <select class="form-select">
                                                        <option>-- Choose Bank Account --</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Made By</label>
                                                    <input type="text" class="form-control" value="Alfin Fachrizal">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Remarks</label>
                                                    <textarea class="form-control" rows="3">This quotation is not a contract or a bill. It is our best total price for the service and goods described above. The customer will be billed after indicating acceptance of this quote. Payment will be prior to the delivery of service and goods.</textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row g-3">
                                                        <div class="col-6">
                                                            <label class="form-label">Sub Total</label>
                                                            <input type="number" class="form-control" value="0">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label">Additional Discount</label>
                                                            <div class="input-group">
                                                                <input type="number" class="form-control" value="0">
                                                                <span class="input-group-text">%</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label">Additional Cost</label>
                                                            <input type="number" class="form-control" value="0">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label">Total</label>
                                                            <input type="number" class="form-control" value="0">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label">Deposit</label>
                                                            <input type="number" class="form-control" value="0">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label">Grand Total</label>
                                                            <input type="number" class="form-control" value="0">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end mt-4">
                                                <button type="submit" class="btn btn-success">SAVE</button>
                                            </div>
                                        </div>
                                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- body-end --}}

            </div>
        </div>
    </div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Choose Product</h5>
                <div id="loadingSpinner" class="ms-2 text-center" style="display: none;">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div> Loading...
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Input Pencarian -->
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search products...">
                </div>
                <!-- Loading Spinner -->

                <table class="table table-bordered" id="productTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th>Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data produk akan dimasukkan di sini -->
                    </tbody>
                </table>
                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination" id="pagination">
                        <!-- Pagination items akan dimasukkan di sini -->
                    </ul>
                </nav>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productTableBody = document.querySelector('#productTable tbody');
        const searchInput = document.getElementById('searchInput');
        const pagination = document.getElementById('pagination');
        const loadingSpinner = document.getElementById('loadingSpinner');
        let currentPage = 1; // Halaman saat ini
        let totalPages = 1; // Total halaman

        // Fungsi untuk mengambil data produk
        function fetchProducts(query = '', page = 1) {
            loadingSpinner.style.display = 'block'; // Tampilkan spinner loading
            const url = `http://127.0.0.1:8000/api/getSearchProduct?query=${query}&page=${page}`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Kosongkan tabel sebelum menambahkan data baru
                    productTableBody.innerHTML = '';
                    pagination.innerHTML = ''; // Kosongkan pagination
                    loadingSpinner.style.display = 'none'; // Sembunyikan spinner loading

                    // Tambahkan setiap produk ke dalam tabel
                    data.data.forEach(product => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${product.ID_BRGJADI}</td>
                            <td>${product.NAMA_BRGJADI}</td>
                            <td>${product.id_satuan}</td>
                            <td>${product.Harga}</td>
                            <td><button class="btn btn-success" onclick="selectProduct('${product.ID_BRGJADI}', '${product.NAMA_BRGJADI}', '${product.Harga}')">Select</button></td>
                        `;
                        productTableBody.appendChild(row);
                    });

                    // Update totalPages
                    totalPages = data.total_pages; // Asumsikan API mengembalikan total_pages
                    renderPagination();
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                    loadingSpinner.style.display = 'none'; // Sembunyikan spinner loading jika terjadi error
                });
        }

        // Fungsi untuk merender pagination
        function renderPagination() {
            pagination.innerHTML = ''; // Kosongkan pagination

            // Tombol First
            const firstLi = document.createElement('li');
            firstLi.className = 'page-item';
            firstLi.innerHTML = `<a class="page-link" href="#" aria-label="First">First</a>`;
            firstLi.addEventListener('click', function(event) {
                event.preventDefault();
                currentPage = 1; // Set ke halaman pertama
                fetchProducts(searchInput.value, currentPage);
            });
            pagination.appendChild(firstLi);

            // Tombol Back
            const backLi = document.createElement('li');
            backLi.className = 'page-item';
            backLi.innerHTML = `<a class="page-link" href="#" aria-label="Back">Back</a>`;
            backLi.addEventListener('click', function(event) {
                event.preventDefault();
                if (currentPage > 1) {
                    currentPage--; // Kurangi halaman
                    fetchProducts(searchInput.value, currentPage);
                }
            });
            pagination.appendChild(backLi);

            // Menentukan halaman yang akan ditampilkan
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            // Jika total halaman kurang dari 5, sesuaikan
            if (totalPages <= 5) {
                startPage = 1;
                endPage = totalPages;
            } else {
                // Jika halaman saat ini dekat dengan awal
                if (currentPage < 3) {
                    endPage = 5;
                }
                // Jika halaman saat ini dekat dengan akhir
                if (currentPage > totalPages - 2) {
                    startPage = totalPages - 4;
                }
            }

            // Nomor Halaman
            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                li.addEventListener('click', function(event) {
                    event.preventDefault();
                    currentPage = i; // Update halaman saat ini
                    fetchProducts(searchInput.value, currentPage); // Ambil produk berdasarkan halaman dan query
                });
                pagination.appendChild(li);
            }

            // Tombol Next
            const nextLi = document.createElement('li');
            nextLi.className = 'page-item';
            nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next">Next</a>`;
            nextLi.addEventListener('click', function(event) {
                event.preventDefault();
                if (currentPage < totalPages) {
                    currentPage++; // Tambah halaman
                    fetchProducts(searchInput.value, currentPage);
                }
            });
            pagination.appendChild(nextLi);

            // Tombol Last
            const lastLi = document.createElement('li');
            lastLi.className = 'page-item';
            lastLi.innerHTML = `<a class="page-link" href="#" aria-label="Last">Last</a>`;
            lastLi.addEventListener('click', function(event) {
                event.preventDefault();
                currentPage = totalPages; // Set ke halaman terakhir
                fetchProducts(searchInput.value, currentPage);
            });
            pagination.appendChild(lastLi);
        }

        // Panggil fungsi untuk mengambil produk saat modal dibuka
        $('#productModal').on('show.bs.modal', function() {
            fetchProducts(); // Ambil semua produk saat modal dibuka
        });

        // Event listener untuk input pencarian
        searchInput.addEventListener('input', function() {
            currentPage = 1; // Reset halaman saat melakukan pencarian
            fetchProducts(this.value); // Ambil produk berdasarkan query pencarian
        });
    });

    // Fungsi untuk memilih produk
    function selectProduct(id, name, price) {
        // Logika untuk menambahkan produk ke dalam tabel di form utama
        console.log(`Selected Product: ${id}, ${name}, Price: ${price}`);
        // Tutup modal
        $('#productModal').modal('hide');
    }
</script>
@endsection
