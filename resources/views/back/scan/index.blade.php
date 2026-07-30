@extends('layouts.layout')

@section('content')
    {{-- ==========================================================================
       HOSE ASSEMBLY / QR SCAN PAGE (Desktop & Mobile Card UI)
       ========================================================================== --}}
    <div class="container py-3 py-lg-4" style="padding-bottom: 90px !important;">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">

                {{-- CARD VIEW: Hose Assembly Manual Input & Camera Trigger --}}
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden" id="qris-card-container">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-2">
                        <i class="bx bx-qr-scan fs-4 text-primary"></i>
                        <h5 class="fw-bold mb-0 text-dark fs-16px">Scan QR Code</h5>
                    </div>
                    <div class="card-body p-4">

                        {{-- Permission Notification --}}
                        <div id="permission-alert" class="alert alert-warning d-none d-flex align-items-center gap-2 mb-3" role="alert">
                            <i class="bx bx-camera-off fs-5 flex-shrink-0"></i>
                            <div>
                                <strong>Camera Access Required</strong><br>
                                <small>Please allow camera permissions in your browser.</small>
                            </div>
                        </div>

                        {{-- Scanner Input / Manual --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary mb-2" for="scan-input">
                                <i class="bx bx-barcode-reader me-1"></i>Scanner Input / Manual
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="text" id="scan-input" class="form-control fs-14px" placeholder="Arahkan scanner atau ketik..." autocomplete="off" autofocus>
                                <button class="btn btn-danger fw-bold px-4" id="btn-ok" type="button" onclick="processScanInput()">
                                    OK
                                </button>
                            </div>
                        </div>

                        <div class="d-flex align-items-center my-4">
                            <hr class="flex-grow-1 border-secondary opacity-25">
                            <span class="px-3 small fw-bold text-muted">OR</span>
                            <hr class="flex-grow-1 border-secondary opacity-25">
                        </div>

                        {{-- Camera Scan Trigger --}}
                        <div id="start-box" class="text-center">
                            {{-- On Mobile, link directly to scan.camera route --}}
                            <a href="{{ route('scan.camera') }}" class="btn btn-outline-danger btn-lg rounded-3 px-4 py-2 fw-semibold w-100 w-sm-auto d-inline-flex d-lg-none align-items-center justify-content-center">
                                <i class="bx bx-camera me-2 fs-5"></i><span>Scan via Camera</span>
                            </a>
                            {{-- On Desktop, start inline desktop camera scanner --}}
                            <button id="btn-start" class="btn btn-outline-danger btn-lg rounded-3 px-4 py-2 fw-semibold w-100 w-sm-auto d-none d-lg-inline-flex align-items-center justify-content-center" onclick="startScanner()">
                                <i class="bx bx-camera me-2 fs-5"></i><span>Scan via Camera</span>
                            </button>
                        </div>

                        {{-- Desktop Inline Camera Box --}}
                        <div id="scanner-box" class="d-none text-center mt-3">
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

                        <div id="loading-box" class="d-none text-center py-4">
                            <div class="spinner-border text-danger mb-3" role="status"></div>
                            <div class="text-muted fw-semibold">Memproses Data Rakitan...</div>
                        </div>

                        <div id="error-box" class="d-none text-center mt-3">
                            <div class="alert alert-danger"><span id="error-msg">Terjadi kesalahan.</span></div>
                            <button class="btn btn-outline-secondary btn-sm" onclick="resetAll()">Coba Lagi</button>
                        </div>

                    </div>
                </div>

                {{-- FULL PAGE RESULT VIEW --}}
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden d-none" id="qris-fullpage-result">
                    <div class="card-header bg-success text-white py-3 px-4 d-flex align-items-center gap-2">
                        <i class="bx bx-check-circle fs-3"></i>
                        <div>
                            <h6 class="fw-bold mb-0 fs-16px">Hasil Scan Terbaca</h6>
                            <small class="text-white-50 fs-12px">Data Rakitan Berhasil Ditemukan</small>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        {{-- Raw QR Code Data Box --}}
                        <div class="p-3 bg-light border-bottom">
                            <small class="text-secondary d-block fw-semibold mb-1 fs-11px text-uppercase">Raw QR Data:</small>
                            <code class="d-block text-dark fw-bold text-break fs-13px font-monospace bg-white p-2 rounded border" id="full-res-raw">-</code>
                        </div>

                        {{-- Details Table --}}
                        <ul class="list-group list-group-flush fs-14px">
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <span class="text-secondary fw-medium">ID Rakitan</span>
                                <span class="fw-bold text-dark fs-15px" id="full-res-id">-</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 bg-light bg-opacity-50">
                                <span class="text-secondary fw-medium">Customer</span>
                                <span class="fw-bold text-primary fs-15px" id="full-res-cust">-</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <span class="text-secondary fw-medium">No Faktur</span>
                                <span class="fw-bold text-dark fs-15px" id="full-res-faktur">-</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 bg-light bg-opacity-50">
                                <span class="text-secondary fw-medium">Tanggal</span>
                                <span class="fw-bold text-dark fs-15px" id="full-res-tanggal">-</span>
                            </li>
                        </ul>

                        {{-- Assembly Specs --}}
                        <div class="p-4 border-top">
                            <small class="text-secondary d-block fw-semibold mb-2 fs-12px text-uppercase">Spesifikasi Rakitan:</small>
                            <div class="p-3 bg-light rounded-3 fw-semibold text-dark text-break fs-14px border" id="full-res-specs">-</div>
                        </div>
                    </div>

                    {{-- Full Page Action Buttons --}}
                    <div class="card-footer bg-white border-top p-3 p-sm-4">
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="button" class="btn btn-primary btn-lg flex-grow-1 py-2 fs-14px fw-semibold d-flex align-items-center justify-content-center gap-2" onclick="resetAll()">
                                <i class="bx bx-refresh fs-5"></i>
                                <span>Pindai Kode Lain</span>
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg py-2 fs-14px fw-semibold d-flex align-items-center justify-content-center gap-2">
                                <i class="bx bx-home fs-5"></i>
                                <span>Kembali ke Dashboard</span>
                            </a>
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

        if (scanInput) {
            scanInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); processScanInput(); } });
        }

        function processScanInput() {
            const raw = scanInput.value.trim();
            if (!raw) return;
            handleDecodedText(raw);
        }

        async function handleDecodedText(decodedText) {
            await stopCamera();

            if (scanInput) scanInput.value = '';

            const parts = decodedText.split('|');
            const idValue = parts[0]?.trim();

            if (!idValue) { showError('Format QR tidak valid.'); return; }
            showPanel('loading-box');

            try {
                const response = await fetch(`{{ route('rakitan.data') }}?id=${idValue}`);
                const result = await response.json();

                if (result.success && result.data && result.data.length > 0) {
                    const data = result.data[0];
                    const idRakitan = data.ID_RAKITAN || idValue;
                    const cust = data.nama_cust || '-';
                    const faktur = data.No_Faktur || '-';
                    const tgl = data.Tanggal ? data.Tanggal.split(' ')[0].split('-').reverse().join('-') : '-';
                    const specs = data.NAMA_RAKITAN || '-';

                    document.getElementById('full-res-raw').textContent = decodedText;
                    document.getElementById('full-res-id').textContent = idRakitan;
                    document.getElementById('full-res-cust').textContent = cust;
                    document.getElementById('full-res-faktur').textContent = faktur;
                    document.getElementById('full-res-tanggal').textContent = tgl;
                    document.getElementById('full-res-specs').textContent = specs;

                    showPanel('fullpage-result');
                } else {
                    showError('Data tidak ditemukan.');
                }
            } catch (err) {
                showError('Terjadi kesalahan koneksi.');
            }
        }

        function showPanel(id) {
            const cardContainer = document.getElementById('qris-card-container');
            const fullpageResult = document.getElementById('qris-fullpage-result');
            const loadingBox = document.getElementById('loading-box');
            const errorBox = document.getElementById('error-box');

            if (id === 'fullpage-result') {
                if (cardContainer) cardContainer.classList.add('d-none');
                if (fullpageResult) fullpageResult.classList.remove('d-none');
            } else {
                if (fullpageResult) fullpageResult.classList.add('d-none');
                if (cardContainer) cardContainer.classList.remove('d-none');

                if (loadingBox) loadingBox.classList.toggle('d-none', id !== 'loading-box');
                if (errorBox) errorBox.classList.toggle('d-none', id !== 'error-box');
            }
        }

        function showError(msg) {
            showPanel('error-box');
            const errEl = document.getElementById('error-msg');
            if (errEl) errEl.textContent = msg;
        }

        function resetAll() {
            stopCamera().then(() => {
                if (scanInput) scanInput.value = '';
                showPanel('');
                document.getElementById('start-box')?.classList.remove('d-none');
                document.getElementById('scanner-box')?.classList.add('d-none');
                if (!isMobile() && scanInput) scanInput.focus();
            });
        }

        function loadCameraList() {
            return Html5Qrcode.getCameras().then(cameras => {
                availableCameras = cameras;
                if (cameras && cameras.length > 0) {
                    if (cameraSelect) cameraSelect.innerHTML = '';
                    cameras.forEach((cam, idx) => {
                        const opt = document.createElement('option');
                        opt.value = cam.id;
                        opt.textContent = cam.label || `Kamera ${idx + 1}`;
                        if (cameraSelect) cameraSelect.appendChild(opt);
                    });
                    if (cameras.length > 1 && cameraWrapper) {
                        cameraWrapper.classList.remove('d-none');
                    }
                    selectedCameraId = cameras[0].id;
                }
            }).catch(err => console.warn("Could not list cameras:", err));
        }

        function startScanner() {
            document.getElementById('start-box')?.classList.add('d-none');
            document.getElementById('scanner-box')?.classList.remove('d-none');
            showPanel('');

            if (html5QrCode && cameraRunning) return;

            html5QrCode = new Html5Qrcode("reader");
            const config = { fps: 10 };

            loadCameraList().then(() => {
                const cameraConfig = selectedCameraId ? { deviceId: { exact: selectedCameraId } } : { facingMode: "environment" };

                html5QrCode.start(
                    cameraConfig,
                    config,
                    (decodedText) => handleDecodedText(decodedText),
                    (errorMessage) => {}
                ).then(() => {
                    cameraRunning = true;
                }).catch(err => {
                    console.error("Camera start error:", err);
                    document.getElementById('permission-alert')?.classList.remove('d-none');
                });
            });
        }

        function stopCamera() {
            if (html5QrCode && cameraRunning) {
                return html5QrCode.stop().then(() => {
                    cameraRunning = false;
                    html5QrCode.clear();
                }).catch(err => {
                    console.warn("Camera stop error:", err);
                    cameraRunning = false;
                });
            }
            return Promise.resolve();
        }

        function cancelScanner() {
            stopCamera().then(() => {
                document.getElementById('scanner-box')?.classList.add('d-none');
                document.getElementById('start-box')?.classList.remove('d-none');
            });
        }

        if (cameraSelect) {
            cameraSelect.addEventListener('change', (e) => {
                selectedCameraId = e.target.value;
                if (cameraRunning && html5QrCode) {
                    html5QrCode.stop().then(() => {
                        cameraRunning = false;
                        startScanner();
                    });
                }
            });
        }
    </script>
@endsection
