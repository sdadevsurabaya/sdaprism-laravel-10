@extends('layouts.layout')

@push('css')
<style>
    @media (max-width: 991.98px) {
        html, body {
            overflow: hidden !important;
            height: 100% !important;
            max-height: 100vh !important;
        }
    }
</style>
@endpush

@section('content')

    {{-- ==========================================================================
       MOBILE & TABLET VIEW (< 992px): Fullscreen Mobile Scanner Concept
       ========================================================================== --}}
    <div class="d-block d-lg-none">
        <div class="qris-fullscreen-scanner">
            {{-- Floating Transparent Top Header --}}
            <div class="qris-fullscreen-header">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-icon btn-dark bg-opacity-50 text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i data-feather="arrow-left" class="icon-md"></i>
                    </a>
                    <div>
                        <h6 class="mb-0 fw-bold text-white fs-15px">Scan QR Code</h6>
                        <small class="text-white-50 fs-11px">Arahkan ke QR Code</small>
                    </div>
                </div>

                {{-- Camera Select Dropdown --}}
                <div id="qris-camera-select-wrapper" class="d-none">
                    <select id="qris-camera-select" class="form-select form-select-sm bg-dark text-white border-secondary shadow-sm" style="max-width: 130px; font-size: 11px;">
                    </select>
                </div>
            </div>

            {{-- Fullscreen Camera Viewfinder --}}
            <div class="qris-fullscreen-viewfinder">
                {{-- Permission Alert --}}
                <div id="permission-alert-mobile" class="alert alert-warning d-none position-absolute top-50 start-50 translate-middle text-center shadow-lg p-4 rounded-4" style="z-index: 1060; width: 88%; max-width: 340px;" role="alert">
                    <i data-feather="camera-off" class="icon-lg text-warning mb-2" style="width: 44px; height: 44px;"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Akses Kamera Diperlukan</h6>
                        <small class="text-secondary d-block mb-3">Mohon izinkan akses kamera di browser Anda, lalu ketuk tombol di bawah.</small>
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2" onclick="startScannerMobile()">
                            <i data-feather="refresh-cw" class="icon-sm me-1"></i> Coba Buka Kamera
                        </button>
                    </div>
                </div>

                {{-- Camera Stream Output --}}
                <div id="qris-reader"></div>

                {{-- Cutout Target Square Mask --}}
                <div class="qris-fullscreen-target">
                    {{-- Laser Scan Line Animation --}}
                    <div class="qris-scan-line"></div>
                    
                    {{-- Animated Corner Brackets --}}
                    <div class="qris-corner qris-corner-tl"></div>
                    <div class="qris-corner qris-corner-tr"></div>
                    <div class="qris-corner qris-corner-bl"></div>
                    <div class="qris-corner qris-corner-br"></div>
                </div>
            </div>

            {{-- Floating Bottom Action Bar (Positioned above Bottom Nav) --}}
            <div class="qris-fullscreen-bottom-bar">
                <button type="button" class="btn btn-dark bg-opacity-75 text-white border border-secondary rounded-pill px-4 py-2 d-flex align-items-center gap-2 shadow-lg" data-bs-toggle="modal" data-bs-target="#manualInputModal">
                    <i data-feather="edit-3" class="icon-sm"></i>
                    <span class="fs-13px fw-semibold">Input Manual</span>
                </button>

                <button type="button" class="btn btn-dark bg-opacity-75 text-white border border-secondary rounded-pill px-4 py-2 d-flex align-items-center gap-2 shadow-lg" onclick="toggleCameraSwitch()">
                    <i data-feather="refresh-cw" class="icon-sm"></i>
                    <span class="fs-13px fw-semibold">Switch</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Manual Input Sheet Modal for Mobile --}}
    <div class="modal fade modal-bottom-sheet d-lg-none" id="manualInputModal" tabindex="-1" aria-labelledby="manualInputModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="manualInputModalLabel">Input Kode Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="text-secondary small mb-3">Ketik ID Rakitan atau gunakan scanner fisik HID di bawah ini:</p>
                    <div class="input-group input-group-lg mb-3">
                        <span class="input-group-text bg-white text-muted"><i data-feather="barcode" class="icon-md"></i></span>
                        <input type="text" id="mobile-manual-input" class="form-control" placeholder="Contoh: RKT-10023..." autocomplete="off">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary btn-lg rounded-3 d-flex align-items-center justify-content-center gap-2" onclick="submitManualInput()">
                            <i data-feather="check" class="icon-md"></i>
                            <span>Proses Kode</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scan Result Bottom Sheet Modal for Mobile --}}
    <div class="modal fade modal-bottom-sheet d-lg-none" id="mobileResultModal" tabindex="-1" aria-labelledby="mobileResultModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white py-3">
                    <h6 class="modal-title fw-bold d-flex align-items-center gap-2 mb-0" id="mobileResultModalLabel">
                        <i data-feather="check-circle" class="icon-md"></i> Hasil Scan Terbaca
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3 bg-light border-bottom">
                        <small class="text-secondary d-block mb-1 fs-11px">Raw QR Data:</small>
                        <code class="d-block text-dark fw-bold text-break fs-12px" id="m-res-raw">-</code>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-secondary">ID Rakitan</span>
                            <span class="fw-bold text-dark" id="m-res-id">-</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-secondary">Customer</span>
                            <span class="fw-bold text-primary" id="m-res-cust">-</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-secondary">No Faktur</span>
                            <span class="fw-bold text-dark" id="m-res-faktur">-</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-2">
                            <span class="text-secondary">Tanggal</span>
                            <span class="fw-bold text-dark" id="m-res-tanggal">-</span>
                        </li>
                    </ul>
                    <div class="p-3 border-top bg-light">
                        <small class="text-secondary d-block mb-1">Spesifikasi Rakitan:</small>
                        <div class="fw-semibold text-dark text-break fs-13px" id="m-res-specs">-</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 p-3">
                    <button type="button" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2" data-bs-dismiss="modal" onclick="resetAllMobile()">
                        <i data-feather="refresh-cw" class="icon-sm"></i>
                        <span>Pindai Kode Lain</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- ==========================================================================
       DESKTOP VIEW (>= 992px): Preserved Original Desktop Layout
       ========================================================================== --}}
    <div class="container py-4 d-none d-lg-block">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center gap-2">
                        <i class="bx bx-qr-scan fs-5"></i>
                        <span class="fw-semibold">Scan QR Code</span>
                    </div>
                    <div class="card-body">

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
        let currentCamIndex = 0;
        let currentFacingMode = 'environment';

        const scanInput = document.getElementById('scan-input');
        const cameraSelect = document.getElementById('camera-select');
        const qrisCameraSelect = document.getElementById('qris-camera-select');
        const cameraWrapper = document.getElementById('camera-select-wrapper');
        const qrisCameraWrapper = document.getElementById('qris-camera-select-wrapper');

        function isMobile() { return /Mobi|Android|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent); }

        if (scanInput) {
            scanInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); processScanInput(); } });
        }

        const manualInput = document.getElementById('mobile-manual-input');
        if (manualInput) {
            manualInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); submitManualInput(); } });
        }

        function submitManualInput() {
            const raw = manualInput.value.trim();
            if (!raw) return;
            const modalEl = document.getElementById('manualInputModal');
            if (modalEl) {
                const bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) bsModal.hide();
            }
            manualInput.value = '';
            handleDecodedText(raw);
        }

        function processScanInput() {
            const raw = scanInput.value.trim();
            if (!raw) return;
            handleDecodedText(raw);
        }

        async function handleDecodedText(decodedText) {
            document.getElementById('res-raw-data').textContent = decodedText;
            document.getElementById('m-res-raw').textContent = decodedText;

            const parts = decodedText.split('|');
            const idValue = parts[0]?.trim();
            if (scanInput) scanInput.value = '';

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

                    // Desktop populate
                    document.getElementById('res-id-rakitan').textContent = idRakitan;
                    document.getElementById('res-nama-customer').textContent = cust;
                    document.getElementById('res-no-faktur').textContent = faktur;
                    document.getElementById('res-tanggal').textContent = tgl;
                    document.getElementById('res-spesifikasi').textContent = specs;

                    // Mobile populate
                    document.getElementById('m-res-id').textContent = idRakitan;
                    document.getElementById('m-res-cust').textContent = cust;
                    document.getElementById('m-res-faktur').textContent = faktur;
                    document.getElementById('m-res-tanggal').textContent = tgl;
                    document.getElementById('m-res-specs').textContent = specs;

                    showPanel('result-box');

                    // Show mobile result modal if mobile
                    const resultModalEl = document.getElementById('mobileResultModal');
                    if (resultModalEl && (window.innerWidth < 992 || isMobile())) {
                        const bsResultModal = new bootstrap.Modal(resultModalEl);
                        bsResultModal.show();
                    }
                } else { showError('Data tidak ditemukan.'); }
            } catch (err) { showError('Terjadi kesalahan koneksi.'); }
        }

        function showPanel(id) {
            ['result-box', 'error-box', 'loading-box'].forEach(p => {
                const el = document.getElementById(p);
                if (el) el.classList.add('d-none');
            });
            if (id === 'loading-box') {
                document.getElementById('loading-box')?.classList.remove('d-none');
            } else if (id === 'result-box') {
                document.getElementById('result-box')?.classList.remove('d-none');
            } else if (id === 'error-box') {
                document.getElementById('error-box')?.classList.remove('d-none');
            }
        }

        function resetAll() {
            stopCamera().then(() => {
                if (scanInput) scanInput.value = '';
                showPanel('');
                document.getElementById('result-box')?.classList.add('d-none');
                document.getElementById('start-box')?.classList.remove('d-none');
                document.getElementById('scanner-box')?.classList.add('d-none');
                if (!isMobile() && scanInput) scanInput.focus();
            });
        }

        function resetAllMobile() {
            if (window.innerWidth < 992 || isMobile()) {
                startScannerMobile();
            }
        }

        function loadCameraList() {
            return Html5Qrcode.getCameras().then(cameras => {
                availableCameras = cameras;
                if (cameras && cameras.length > 0) {
                    if (cameraSelect) cameraSelect.innerHTML = '';
                    if (qrisCameraSelect) qrisCameraSelect.innerHTML = '';

                    cameras.forEach((cam, idx) => {
                        const opt = document.createElement('option');
                        opt.value = cam.id;
                        opt.textContent = cam.label || `Kamera ${idx + 1}`;

                        const labelLower = (cam.label || '').toLowerCase();
                        if (labelLower.includes('back') || labelLower.includes('rear') || idx === cameras.length - 1) {
                            opt.selected = true;
                            currentCamIndex = idx;
                        }

                        if (cameraSelect) cameraSelect.appendChild(opt);
                        if (qrisCameraSelect) qrisCameraSelect.appendChild(opt.cloneNode(true));
                    });

                    selectedCameraId = (cameraSelect ? cameraSelect.value : (qrisCameraSelect ? qrisCameraSelect.value : cameras[0].id));
                    if (cameraWrapper) cameraWrapper.classList.remove('d-none');
                    if (qrisCameraWrapper) qrisCameraWrapper.classList.remove('d-none');
                } else {
                    if (cameraWrapper) cameraWrapper.classList.add('d-none');
                    if (qrisCameraWrapper) qrisCameraWrapper.classList.add('d-none');
                }
                return cameras;
            }).catch(err => {
                throw err;
            });
        }

        if (cameraSelect) {
            cameraSelect.addEventListener('change', async function() {
                selectedCameraId = this.value;
                await stopCamera();
                startCameraStream(selectedCameraId, 'reader');
            });
        }

        if (qrisCameraSelect) {
            qrisCameraSelect.addEventListener('change', async function() {
                selectedCameraId = this.value;
                await stopCamera();
                startCameraStream(selectedCameraId, 'qris-reader');
            });
        }

        async function toggleCameraSwitch() {
            await stopCamera();

            if (availableCameras && availableCameras.length > 1) {
                currentCamIndex = (currentCamIndex + 1) % availableCameras.length;
                selectedCameraId = availableCameras[currentCamIndex].id;
                if (qrisCameraSelect) qrisCameraSelect.value = selectedCameraId;
                if (cameraSelect) cameraSelect.value = selectedCameraId;
                startCameraStream(selectedCameraId, 'qris-reader');
            } else {
                // Mobile camera toggle between rear (environment) & front (user)
                currentFacingMode = (currentFacingMode === 'environment') ? 'user' : 'environment';
                startCameraStream({ facingMode: currentFacingMode }, 'qris-reader');
            }
        }

        function startScanner() {
            document.getElementById('start-box')?.classList.add('d-none');
            document.getElementById('scanner-box')?.classList.remove('d-none');
            document.getElementById('permission-alert')?.classList.add('d-none');

            loadCameraList().then(cameras => {
                if (cameras && cameras.length > 0) {
                    const camId = selectedCameraId || cameras[cameras.length - 1].id;
                    startCameraStream(camId, 'reader');
                } else {
                    showError('Kamera tidak ditemukan.');
                }
            }).catch(() => {
                showError('Gagal mengakses kamera.');
            });
        }

        async function startScannerMobile() {
            document.getElementById('permission-alert-mobile')?.classList.add('d-none');
            const scanner = document.querySelector('.qris-fullscreen-scanner');
            if (scanner) scanner.style.display = 'block';

            if (cameraRunning) return;

            try {
                const cameras = await loadCameraList();
                if (cameras && cameras.length > 0) {
                    const camId = selectedCameraId || cameras[cameras.length - 1].id;
                    await startCameraStream(camId, 'qris-reader');
                } else {
                    await startCameraStream({ facingMode: currentFacingMode }, 'qris-reader');
                }
            } catch (err) {
                console.warn("Camera load with device ID failed, trying facingMode fallback...", err);
                try {
                    await startCameraStream({ facingMode: currentFacingMode }, 'qris-reader');
                } catch (err2) {
                    try {
                        const altFacing = (currentFacingMode === 'environment') ? 'user' : 'environment';
                        await startCameraStream({ facingMode: altFacing }, 'qris-reader');
                    } catch (err3) {
                        console.error("All camera start attempts failed:", err3);
                        document.getElementById('permission-alert-mobile')?.classList.remove('d-none');
                    }
                }
            }
        }

        function startCameraStream(cameraTarget, targetContainerId) {
            if (!html5QrCode || html5QrCode.elementId !== targetContainerId) {
                if (html5QrCode) {
                    try { html5QrCode.clear(); } catch(e) {}
                }
                html5QrCode = new Html5Qrcode(targetContainerId);
                html5QrCode.elementId = targetContainerId;
            }

            const scanConfig = (targetContainerId === 'qris-reader')
                ? { fps: 15 }
                : { fps: 10, qrbox: { width: 240, height: 240 } };

            return html5QrCode.start(
                cameraTarget,
                scanConfig,
                (decodedText) => {
                    stopCamera().then(() => handleDecodedText(decodedText));
                }
            ).then(() => {
                cameraRunning = true;
            });
        }

        function stopCamera() {
            if (!html5QrCode || !cameraRunning) return Promise.resolve();
            return html5QrCode.stop().then(() => {
                cameraRunning = false;
                const containerId = html5QrCode.elementId;
                if (containerId && document.getElementById(containerId)) {
                    document.getElementById(containerId).innerHTML = '';
                }
            }).catch(() => {
                cameraRunning = false;
            });
        }

        function cancelScanner() {
            stopCamera().then(() => {
                document.getElementById('scanner-box')?.classList.add('d-none');
                document.getElementById('start-box')?.classList.remove('d-none');
            });
        }

        function showError(msg) {
            stopCamera().then(() => {
                const desktopErr = document.getElementById('error-msg');
                if (desktopErr) desktopErr.textContent = msg;
            });
        }

        if (!isMobile() && scanInput) {
            document.addEventListener('click', e => {
                if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'A' && e.target.tagName !== 'SELECT') {
                    scanInput.focus();
                }
            });
            scanInput.focus();
        }

        // Stop camera & hide scanner overlay whenever any modal is opened so modal is 100% accessible
        document.addEventListener('show.bs.modal', function () {
            stopCamera();
            const scanner = document.querySelector('.qris-fullscreen-scanner');
            if (scanner) scanner.style.display = 'none';
        });

        document.addEventListener('hidden.bs.modal', function (e) {
            const scanner = document.querySelector('.qris-fullscreen-scanner');
            if (scanner) scanner.style.display = 'block';

            if (e.target && e.target.id === 'manualInputModal' && (window.innerWidth < 992 || isMobile())) {
                setTimeout(() => {
                    startScannerMobile();
                }, 200);
            }
        });

        // Auto-start camera scanner on mobile/tablet page load
        document.addEventListener('DOMContentLoaded', () => {
            if (window.innerWidth < 992 || isMobile()) {
                setTimeout(() => {
                    startScannerMobile();
                }, 300);
            }
        });
    </script>
@endsection
