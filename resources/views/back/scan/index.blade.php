@extends('layouts.layout')

@section('content')
    <nav class="page-breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Main</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <div class="container py-2">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow-sm border-0 rounded-3">
                    {{-- <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bx bx-qr-scan fs-4"></i>
                            <span class="fw-bold fs-5">Dashboard - QR Scan & Fast Lookup</span>
                        </div>
                        <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-semibold">Scanner Ready</span>
                    </div> --}}
                    <div class="card-body p-4">

                        {{-- Permission Notification --}}
                        <div id="permission-alert" class="alert alert-warning d-none d-flex align-items-center gap-2 mb-3" role="alert">
                            <i class="bx bx-camera-off fs-5 flex-shrink-0"></i>
                            <div>
                                <strong>Camera Access Required</strong><br>
                                <small>Please allow camera permissions in your browser.</small>
                            </div>
                        </div>

                        {{-- Scanner HID Input --}}
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted mb-2" for="scan-input">
                                <i class="bx bx-barcode-reader me-1"></i>Scanner Input / Manual
                            </label>
                            <div class="input-group">
                                <input type="text" id="scan-input" class="form-control" placeholder="Arahkan scanner atau ketik..." autocomplete="off" autofocus>
                                <button class="btn btn-danger fw-semibold px-3" id="btn-ok" type="button" onclick="processScanInput()">
                                    <i class="bx bx-check me-1"></i>OK
                                </button>
                            </div>
                        </div>

                        <div class="d-flex align-items-center my-3"><hr class="flex-grow-1"><span class="px-3 small fw-bold text-muted">OR</span><hr class="flex-grow-1"></div>

                        {{-- Camera Scan --}}
                        <div id="start-box" class="text-center mb-3">
                            <button id="btn-start" class="btn btn-outline-danger px-4" onclick="startScanner()">
                                <i class="bx bx-camera me-1"></i>Scan via Camera
                            </button>
                        </div>

                        <div id="scanner-box" class="d-none text-center">
                            <div id="camera-select-wrapper" class="mb-3 d-none">
                                <label for="camera-select" class="form-label small fw-semibold text-muted mb-1">
                                    <i class="bx bx-camera me-1"></i>Pilih Kamera:
                                </label>
                                <select id="camera-select" class="form-select form-select-sm mx-auto shadow-sm" style="max-width: 340px;">
                                </select>
                            </div>
                            <div id="reader" class="mx-auto mb-2 overflow-hidden rounded-3 border" style="width: 100%; max-width: 420px;"></div>
                            <button id="btn-cancel" class="btn btn-outline-secondary btn-sm mb-2" onclick="cancelScanner()">
                                <i class="bx bx-x me-1"></i>Tutup Kamera
                            </button>
                        </div>

                        {{-- Results Display --}}
                        <div id="result-box" class="d-none mt-3">
                            <div class="card border-success shadow-none mb-3 overflow-hidden">
                                <div class="card-header bg-success text-white py-2 d-flex align-items-center gap-2">
                                    <i class="bx bx-check-circle fs-5"></i>
                                    <span class="fw-bold small text-uppercase">Scan Result</span>
                                </div>
                                <div class="card-body p-0">
                                    {{-- Raw Data Display --}}
                                    <div class="px-3 py-2 border-bottom bg-light bg-opacity-50">
                                        <div class="small fw-bold text-dark mb-1">Data QR Terbaca:</div>
                                        <div class="p-2 border rounded bg-white small text-muted font-monospace" id="res-raw-data" style="word-break: break-all;">-</div>
                                    </div>

                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>
                                            <tr class="border-bottom">
                                                <th class="ps-3 py-2 text-muted fw-normal" style="width: 100px;">ID Rakitan</th>
                                                <td class="py-2 text-dark">
                                                    <span class="me-1">:</span>
                                                    <span class="fw-bold" id="res-id-rakitan">-</span>
                                                </td>
                                            </tr>
                                            <tr class="border-bottom">
                                                <th class="ps-3 py-2 text-muted fw-normal">Customer</th>
                                                <td class="py-2 text-primary text-wrap">
                                                    <span class="me-1 text-dark">:</span>
                                                    <span class="fw-bold" id="res-nama-customer">-</span>
                                                </td>
                                            </tr>
                                            <tr class="border-bottom">
                                                <th class="ps-3 py-2 text-muted fw-normal">No Faktur</th>
                                                <td class="py-2 text-dark">
                                                    <span class="me-1">:</span>
                                                    <span class="fw-bold" id="res-no-faktur">-</span>
                                                </td>
                                            </tr>
                                            <tr class="border-bottom">
                                                <th class="ps-3 py-2 text-muted fw-normal">Tanggal</th>
                                                <td class="py-2 text-dark">
                                                    <span class="me-1">:</span>
                                                    <span class="fw-bold" id="res-tanggal">-</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="p-3 border-top bg-light bg-opacity-10">
                                        <div class="py-1 text-muted small">Deskripsi Spesifikasi:</div>
                                        <div class="fw-bold text-dark lh-base" id="res-spesifikasi" style="word-break: break-word;">-</div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center"><button class="btn btn-outline-secondary btn-sm" onclick="resetAll()">Reset / Next Scan</button></div>
                        </div>

                        <div id="loading-box" class="d-none text-center py-5">
                            <div class="spinner-border text-danger mb-3" role="status"></div>
                            <div class="text-muted fw-semibold">Loading Data...</div>
                        </div>

                        <div id="error-box" class="d-none text-center mt-3">
                            <div class="alert alert-danger"><span id="error-msg">Error occurred.</span></div>
                            <button class="btn btn-outline-secondary btn-sm" onclick="resetAll()">Try Again</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        let html5QrCode = null;
        let cameraRunning = false;
        let availableCameras = [];
        let selectedCameraId = null;

        const scanInput = document.getElementById('scan-input');
        const cameraSelect = document.getElementById('camera-select');
        const cameraWrapper = document.getElementById('camera-select-wrapper');

        function isMobile() { return /Mobi|Android|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent); }

        scanInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); processScanInput(); } });

        function processScanInput() {
            const raw = scanInput.value.trim();
            if (!raw) return;
            handleDecodedText(raw);
        }

        async function handleDecodedText(decodedText) {
            document.getElementById('res-raw-data').textContent = decodedText;
            const parts = decodedText.split('|');
            const idValue = parts[0]?.trim();
            scanInput.value = '';
            if (!isMobile()) scanInput.focus();

            if (!idValue) { showError('Invalid QR Format.'); return; }
            showPanel('loading-box');

            try {
                const response = await fetch(`{{ route('rakitan.data') }}?id=${idValue}`);
                const result = await response.json();

                if (result.success && result.data && result.data.length > 0) {
                    const data = result.data[0];
                    document.getElementById('res-id-rakitan').textContent = data.ID_RAKITAN || idValue;
                    document.getElementById('res-nama-customer').textContent = data.nama_cust || '-';
                    document.getElementById('res-no-faktur').textContent = data.No_Faktur || '-';
                    document.getElementById('res-tanggal').textContent = data.Tanggal ? data.Tanggal.split(' ')[0].split('-').reverse().join('-') : '-';
                    document.getElementById('res-spesifikasi').textContent = data.NAMA_RAKITAN || '-';
                    showPanel('result-box');
                } else { showError('Data not found.'); }
            } catch (err) { showError('Connection Error.'); }
        }

        function showPanel(id) {
            ['result-box', 'error-box', 'loading-box'].forEach(p => {
                const el = document.getElementById(p);
                if (el) el.classList.toggle('d-none', p !== id);
            });
        }

        function resetAll() {
            stopCamera().then(() => {
                scanInput.value = '';
                showPanel('');
                document.getElementById('result-box').classList.add('d-none');
                document.getElementById('start-box').classList.remove('d-none');
                document.getElementById('scanner-box').classList.add('d-none');
                if (!isMobile()) scanInput.focus();
            });
        }

        function loadCameraList() {
            return Html5Qrcode.getCameras().then(cameras => {
                availableCameras = cameras;
                if (cameras && cameras.length > 0) {
                    cameraSelect.innerHTML = '';
                    cameras.forEach((cam, idx) => {
                        const opt = document.createElement('option');
                        opt.value = cam.id;
                        opt.textContent = cam.label || `Kamera ${idx + 1}`;
                        
                        const labelLower = (cam.label || '').toLowerCase();
                        if (labelLower.includes('back') || labelLower.includes('rear') || idx === cameras.length - 1) {
                            opt.selected = true;
                        }
                        cameraSelect.appendChild(opt);
                    });

                    selectedCameraId = cameraSelect.value;
                    cameraWrapper.classList.remove('d-none');
                } else {
                    cameraWrapper.classList.add('d-none');
                }
                return cameras;
            }).catch(err => {
                document.getElementById('permission-alert').classList.remove('d-none');
                throw err;
            });
        }

        if (cameraSelect) {
            cameraSelect.addEventListener('change', function() {
                selectedCameraId = this.value;
                if (cameraRunning) {
                    stopCamera().then(() => {
                        startCameraStream(selectedCameraId);
                    });
                }
            });
        }

        function startScanner() {
            document.getElementById('start-box').classList.add('d-none');
            document.getElementById('scanner-box').classList.remove('d-none');
            document.getElementById('permission-alert').classList.add('d-none');

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            loadCameraList().then(cameras => {
                if (cameras && cameras.length > 0) {
                    const camId = selectedCameraId || cameras[cameras.length - 1].id;
                    startCameraStream(camId);
                } else {
                    showError('Kamera tidak ditemukan pada perangkat Anda.');
                }
            }).catch(() => {
                showError('Gagal mengakses kamera. Mohon izinkan akses kamera di browser Anda.');
            });
        }

        function startCameraStream(cameraId) {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }

            html5QrCode.start(
                cameraId,
                { fps: 10, qrbox: 280 },
                (decodedText) => {
                    stopCamera().then(() => handleDecodedText(decodedText));
                }
            ).then(() => {
                cameraRunning = true;
            }).catch(err => {
                showError('Gagal membuka kamera yang dipilih.');
            });
        }

        function stopCamera() {
            if (!html5QrCode || !cameraRunning) return Promise.resolve();
            return html5QrCode.stop().then(() => {
                cameraRunning = false;
                document.getElementById('reader').innerHTML = '';
            }).catch(() => {
                cameraRunning = false;
            });
        }

        function cancelScanner() {
            stopCamera().then(() => {
                document.getElementById('scanner-box').classList.add('d-none');
                document.getElementById('start-box').classList.remove('d-none');
            });
        }

        function showError(msg) {
            stopCamera().then(() => {
                document.getElementById('error-msg').textContent = msg;
                showPanel('error-box');
            });
        }

        if (!isMobile()) {
            document.addEventListener('click', e => {
                if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'A' && e.target.tagName !== 'SELECT') {
                    scanInput.focus();
                }
            });
            scanInput.focus();
        }
    </script>
@endsection

