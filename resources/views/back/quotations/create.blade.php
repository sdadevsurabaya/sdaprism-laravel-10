@extends('back.layouts.layout')
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
                                            <input type="text" id="no" class="form-control"
                                                value="012/BD-IPL/02/2025">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Address Letter</label>
                                            <select class="form-select" id="address_letter">
                                                <option value="surabaya">Surabaya</option>
                                                <option value="ciakrang">Cikarang</option>
                                                <option value="semarang">Semarang</option>

                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Date</label>
                                            <input type="date" id="date" class="form-control" value="2025-02-27">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Customer</label>
                                            <input type="text" id="customer_id" class="form-control" value="1">
                                            <input type="text" id="customer" class="form-control"
                                                value="Khong Guan Corporation">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Address</label>
                                            <input type="text" id="address" class="form-control"
                                                value="30068 Eigenbrodt Way Union City">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Valid Until</label>
                                            <input type="date" id="valid_until" class="form-control" value="2025-02-27">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Phone</label>
                                            <input type="text" id="phone" class="form-control" value="5104877800">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Contact Person</label>
                                            <input type="text" id="contact_person" class="form-control"
                                                value="Mr Albert, Mr James, Mrs Martina">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Payment Type</label>
                                            <select id="payment_type" class="form-select">
                                                <option value="CASH">Cash</option>
                                                <option value="BANK TRANSFER">Bank Transfer</option>
                                                <option value="PAY NOW">PayNow</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Currency</label>
                                            <select id="currency" class="form-select">
                                                <option value="IDR">IDR</option>
                                                <option value="UGD">UGD</option>
                                                <option value="SGD">SGD</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal"
                                        data-bs-target="#productModal">
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
                                            <tbody id="quotationProductTableBody">
                                                <!-- Baris produk akan ditambahkan di sini oleh JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row g-3 mt-4">
                                        <div class="col-md-6">
                                            <label class="form-label">In Words</label>
                                            <textarea id="in_words" class="form-control" rows="3"></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Descriptions</label>
                                            <textarea id="descriptions" class="form-control" rows="3" placeholder="Input your descriptions here....."></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Account Options</label>
                                            <select id="account_options" class="form-select">
                                                <option value="BCA USD">BCA USD</option>
                                                <option value="BCA IDR">BCA IDR</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Made By</label>
                                            <input id="made_by" type="text" class="form-control"
                                                value="Alfin Fachrizal">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Remarks</label>
                                            <textarea id="remarks" class="form-control" rows="3">This quotation is not a contract or a bill. It is our best total price for the service and goods described above. The customer will be billed after indicating acceptance of this quote. Payment will be prior to the delivery of service and goods.</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <label class="form-label">Sub Total</label>
                                                    <input id="sub_total" type="number" class="form-control"
                                                        value="0">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label">Additional Discount</label>
                                                    <div class="input-group">
                                                        <input id="additional_discount" type="number"
                                                            class="form-control" value="0">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label">Additional Cost</label>
                                                    <input id="additional_cost" type="number" class="form-control"
                                                        value="0">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label">Total</label>
                                                    <input id="total" type="number" class="form-control"
                                                        value="0">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label">Deposit</label>
                                                    <input id="deposit" type="number" class="form-control"
                                                        value="0">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label">Grand Total</label>
                                                    <input id="grand_total" type="number" class="form-control"
                                                        value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="button" class="btn btn-success"
                                            onclick="saveproductquotation()">SAVE</button>
                                    </div>
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
        var productQuotation = [];
        const productTableBody = document.querySelector('#productTable tbody');
        document.addEventListener('DOMContentLoaded', function() {

            const searchInput = document.getElementById('searchInput');
            const pagination = document.getElementById('pagination');
            const loadingSpinner = document.getElementById('loadingSpinner');
            let currentPage = 1; // Halaman saat ini
            let totalPages = 1; // Total halaman

            // Array untuk menyimpan produk yang dipilih


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
                        <td><button class="btn btn-success" onclick="selectProduct('${product.ID_BRGJADI}', '${product.NAMA_BRGJADI.replace(/"/g, '&quot;')}', '${product.Harga}')">Select</button></td>
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
                        fetchProducts(searchInput.value,
                            currentPage); // Ambil produk berdasarkan halaman dan query
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
            console.log("test 1");
            console.log(productQuotation);
            console.log("test 2");

            // Pastikan name tidak menyebabkan error jika ada tanda petik
            name = name.replace(/"/g, '&quot;');

            console.log(name); // Mengganti " dengan &quot; agar aman

            if (!productQuotation.some(product => product.id === id)) {
                console.log("test 3");
                productQuotation.push({
                    quotation_id: 1,
                    product_id: id,
                    unit: 'PC/PCS',
                    qty: 1,
                    // name: name, // Menyimpan name dengan karakter aman
                    price: parseFloat(price),


                    // total: parseFloat(price),
                    price_before_discount: parseFloat(price),
                    disc_percent: 0,
                    disc_price_total: 0,
                    discr_price_per_unit: 0,
                    nett: parseFloat(price),
                    status: 'active',
                });
            } else {
                alert("Product already added!");
            }

            updateProductTable();
            $('#productModal').modal('hide');
        }


        // Fungsi untuk mengupdate tabel produk di form utama
        function updateProductTable() {
            const tableBody = document.querySelector('#quotationProductTableBody');
            if (!tableBody) return console.error("Element #quotationProductTableBody tidak ditemukan!");

            console.log("updateProductTable() dipanggil");
            console.log("Isi productQuotation:", productQuotation);

            let rowsHTML = ''; // Gunakan string untuk menyimpan semua baris

            productQuotation.forEach((product, index) => {
                // Hitung nilai diskon dan harga setelah diskon
                let discPriceTotal = (product.price * product.qty) * (product.disc_percent / 100);
                let discPricePerUnit = discPriceTotal / product.qty;
                let netPrice = (product.price * product.qty) - discPriceTotal;
                // <td><input type="number" class="form-control" value="${product.discount}" min="0" max="100" onchange="updateProductDiscount(${index}, this.value)"></td>

                rowsHTML += `
        <tr>
            <td>${index + 1}</td>
            <td>${product.name}</td>
            <td>
                <select class="form-select" onchange="updateProductUnit(${index}, this.value)">
                    <option value="PC / PCS" ${product.unit === 'PC / PCS' ? 'selected' : ''}>PC / PCS</option>
                </select>
            </td>
            <td><input type="number" class="form-control" value="${product.qty}" min="1" onchange="updateProductQty(${index}, this.value)"></td>
            <td><input type="number" class="form-control" value="${product.price}" readonly></td>
            <td><input type="number" class="form-control" value="${(product.price * product.qty).toFixed(2)}" readonly></td>
            <td><input type="number" class="form-control" value="${product.disc_percent}" min="0" max="100" ></td>

            <td><input type="number" class="form-control" value="${discPriceTotal.toFixed(2)}" ></td>
            <td><input type="number" class="form-control" value="${discPricePerUnit.toFixed(2)}" ></td>
            <td><input type="number" class="form-control" value="${netPrice.toFixed(2)}" ></td>
            <td class="text-center">
                <button class="btn btn-link text-danger" onclick="removeProduct(${index})">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
        `;
            });

            tableBody.innerHTML = rowsHTML; // Masukkan semua row sekaligus

            console.log("Tabel berhasil diperbarui");
        }

        // Fungsi untuk mengupdate quantity produk
        function updateProductQty(index, qty) {
            productQuotation[index].qty = parseInt(qty);
            updateProductTable();
        }

        // Fungsi untuk mengupdate unit produk
        function updateProductUnit(index, unit) {
            productQuotation[index].unit = unit;
            updateProductTable();
        }

        // Fungsi untuk mengupdate discount produk
        function updateProductDiscount(index, discount) {
            // Logika untuk mengupdate discount
            // Misalnya, hitung total setelah discount
            updateProductTable();
        }

        // Fungsi untuk menghapus produk dari array
        function removeProduct(index) {
            productQuotation.splice(index, 1);
            updateProductTable();
        }

        // document.getElementById("saveQuotationBtn").addEventListener("click", function()
        function saveproductquotation() {
            // Ambil data dari form
            let quotationData = {
                no: document.getElementById("no").value || "",
                address_letter: document.getElementById("address_letter")?.value || "",
                date: document.getElementById("date")?.value || new Date().toISOString().split('T')[0],
                customer_id: document.getElementById("customer_id")?.value || "",
                customer: document.getElementById("customer")?.value || "",
                phone: document.getElementById("phone")?.value || "",
                address: document.getElementById("address")?.value || "",
                valid_until: document.getElementById("valid_until")?.value || "",
                contact_person: document.getElementById("contact_person")?.value || "",
                payment_type: document.getElementById("payment_type")?.value || "",
                currency: document.getElementById("currency")?.value || "",
                in_words: document.getElementById("in_words")?.value || "",
                descriptions: document.getElementById("descriptions")?.value || "",
                account_options: document.getElementById("account_options")?.value || "",
                made_by: document.getElementById("made_by")?.value || "",
                remarks: document.getElementById("remarks")?.value || "",
                sub_total: document.getElementById("sub_total")?.value || "",
                additional_discount: document.getElementById("additional_discount")?.value || "",
                additional_cost: document.getElementById("additional_cost")?.value || "",
                total: document.getElementById("total")?.value || "",
                deposit: document.getElementById("deposit")?.value || "",
                grand_total: document.getElementById("grand_total")?.value || "",
            };

            fetch('/api/postQuotation', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(quotationData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.errors) {
                        alert("Error saat menyimpan Quotation!");
                        console.error(data.errors);
                        return;
                    }

                    // Setelah berhasil menyimpan quotation, simpan products dengan quotation_id dari response
                    let quotationId = data.id;
                    console.log("id nya");
                    console.log(quotationId);

                    productQuotation.forEach(product => {product.quotation_id = quotationId});


                    return fetch('/api/quotation/detail/batch-store', {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                        products: productQuotation
                    })
                    });
                })
                .then(response => response.json())
                .then(result => {
                    if (result.errors) {
                        alert("Error saat menyimpan detail produk!");
                        console.error(result.errors);
                    } else {
                        alert("Quotation dan produk berhasil disimpan!");
                        location.reload(); // Refresh halaman setelah berhasil simpan
                    }
                })
                .catch(error => console.error("Error:", error));





            // fetch('/api/quotation/detail/batch-store', {
            //         method: 'POST',
            //         headers: {
            //             'Content-Type': 'application/json'
            //         },
            //         body: JSON.stringify({
            //             products: productQuotation
            //         })
            //     })
            //     .then(response => response.json())
            //     .then(data => {
            //         if (data.errors) {
            //             console.error("Error saat menyimpan data:", data.errors);
            //             alert("Gagal menyimpan data!");
            //         } else {
            //             alert("Data berhasil disimpan!");
            //             console.log("Response API:", data);
            //         }
            //     })
            //     .catch(error => {
            //         console.error("Terjadi kesalahan:", error);
            //         alert("Terjadi kesalahan saat menyimpan data!");
            //     });




        }
        // );
    </script>
@endsection
