@extends('layouts.layout')

@push('css')
    <style>
        @media (max-width: 991.98px) {

            html,
            body {
                height: 100% !important;
                height: 100dvh !important;
                overflow: hidden !important;
                position: fixed !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                padding-bottom: 0 !important;
                touch-action: none !important;
            }

            body {
                padding-bottom: 0 !important;
            }

            .main-wrapper {
                height: 100dvh !important;
                min-height: 100dvh !important;
                overflow: hidden !important;
            }

            .page-wrapper {
                height: calc(100dvh - 60px) !important;
                overflow: hidden !important;
            }

            .page-content-wrapper {
                padding: 0 !important;
                margin: 0 !important;
                padding-bottom: 0 !important;
            }

            footer.footer {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')
    {{-- ==========================================================================
       DEDICATED FULLSCREEN MOBILE CAMERA SCANNER (Zero Flicker, Instant Start)
       ========================================================================== --}}
    <div id="camera-scanner-view" class="d-block">
        <div class="qris-fullscreen-scanner" id="qrisFullscreenScanner" style="display: block;">
            {{-- Floating Transparent Top Header --}}
            <div class="qris-fullscreen-header">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('dashboard') }}"
                        class="btn btn-icon btn-dark bg-opacity-50 text-white rounded-circle p-2 d-flex align-items-center justify-content-center"
                        style="width: 36px; height: 36px;">
                        <i data-feather="arrow-left" class="icon-md"></i>
                    </a>
                    <div>
                        <h6 class="mb-0 fw-bold text-white fs-15px">Scan QR Code</h6>
                        <small class="text-white-50 fs-11px">Arahkan ke QR Code</small>
                    </div>
                </div>

                {{-- Camera Select Dropdown Header --}}
                <div id="qris-camera-select-wrapper" class="d-none">
                    <select id="qris-camera-select"
                        class="form-select form-select-sm bg-dark text-white border-secondary shadow-sm"
                        style="max-width: 130px; font-size: 11px;">
                    </select>
                </div>
            </div>

            {{-- Fullscreen Camera Viewfinder --}}
            <div class="qris-fullscreen-viewfinder">
                {{-- Permission Alert --}}
                <div id="permission-alert-mobile"
                    class="alert alert-warning d-none position-absolute top-50 start-50 translate-middle text-center shadow-lg p-4 rounded-4"
                    style="z-index: 1060; width: 88%; max-width: 340px;" role="alert">
                    <i data-feather="camera-off" class="icon-lg text-warning mb-2" style="width: 44px; height: 44px;"></i>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Akses Kamera Diperlukan</h6>
                        <small class="text-secondary d-block mb-3">Mohon izinkan akses kamera di browser Anda, lalu ketuk
                            tombol di bawah.</small>
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2"
                            onclick="startScannerMobile()">
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

                {{-- Floating Action Bar (Positioned 20px below the Target Box) --}}
                <div class="qris-fullscreen-bottom-bar">
                    <a href="{{ route('scan.qr') }}"
                        class="btn btn-dark bg-opacity-75 text-white border border-secondary rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-lg text-decoration-none">
                        <i data-feather="edit-3" class="icon-sm"></i>
                        <span class="fs-13px fw-semibold">Input Manual</span>
                    </a>

                    <div class="dropup">
                        <button
                            class="btn btn-dark bg-opacity-75 text-white border border-secondary rounded-pill px-3 py-2 d-flex align-items-center gap-2 shadow-lg dropdown-toggle"
                            type="button" id="cameraDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i data-feather="camera" class="icon-sm"></i>
                            <span class="fs-13px fw-semibold" id="cameraDropdownLabel">Kamera</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg rounded-3 fs-12px p-2 mb-2"
                            id="cameraDropdownList" aria-labelledby="cameraDropdownBtn"
                            style="min-width: 220px; max-height: 240px; overflow-y: auto;">
                            <li><a class="dropdown-item disabled text-muted fs-11px" href="javascript:void(0);">Mencari
                                    kamera...</a></li>
                        </ul>
                    </div>
                </div>
            </div>


        </div>
    </div>

    {{-- FULL PAGE RESULT VIEW (Shown after successful scan, no modal backdrop) --}}
    <div class="container py-3 py-lg-4 d-none" id="qris-fullpage-result-container" style="padding-bottom: 90px !important;">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden" id="qris-fullpage-result">
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
                            <small class="text-secondary d-block fw-semibold mb-1 fs-11px text-uppercase">Raw QR
                                Data:</small>
                            <code
                                class="d-block text-dark fw-bold text-break fs-13px font-monospace bg-white p-2 rounded border"
                                id="full-res-raw">-</code>
                        </div>

                        {{-- Details Table --}}
                        <ul class="list-group list-group-flush fs-14px">
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <span class="text-secondary fw-medium">ID Rakitan</span>
                                <span class="fw-bold text-dark fs-15px" id="full-res-id">-</span>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 bg-light bg-opacity-50">
                                <span class="text-secondary fw-medium">Customer</span>
                                <span class="fw-bold text-primary fs-15px" id="full-res-cust">-</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                <span class="text-secondary fw-medium">No Faktur</span>
                                <span class="fw-bold text-dark fs-15px" id="full-res-faktur">-</span>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 bg-light bg-opacity-50">
                                <span class="text-secondary fw-medium">Tanggal</span>
                                <span class="fw-bold text-dark fs-15px" id="full-res-tanggal">-</span>
                            </li>
                        </ul>

                        {{-- Assembly Specs --}}
                        <div class="p-4 border-top">
                            <small class="text-secondary d-block fw-semibold mb-2 fs-12px text-uppercase">Spesifikasi
                                Rakitan:</small>
                            <div class="p-3 bg-light rounded-3 fw-semibold text-dark text-break fs-14px border"
                                id="full-res-specs">-</div>
                        </div>
                    </div>

                    {{-- Full Page Action Buttons --}}
                    <div class="card-footer bg-white border-top p-3 p-sm-4">
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <button type="button"
                                class="btn btn-primary btn-lg flex-grow-1 py-2 fs-14px fw-semibold d-flex align-items-center justify-content-center gap-2"
                                onclick="restartCameraScan()">
                                <i class="bx bx-refresh fs-5"></i>
                                <span>Pindai Kode Lain</span>
                            </button>
                            <a href="{{ route('dashboard') }}"
                                class="btn btn-outline-secondary btn-lg py-2 fs-14px fw-semibold d-flex align-items-center justify-content-center gap-2">
                                <i class="bx bx-home fs-5"></i>
                                <span>Kembali ke Dashboard</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Fullscreen Loading Overlay during Data Fetch --}}
    <div id="qris-loading-overlay"
        class="d-none position-fixed top-0 start-0 w-100 h-100 flex-column align-items-center justify-content-center bg-dark bg-opacity-75 text-white"
        style="z-index: 1099; backdrop-filter: blur(4px);">
        <div class="spinner-border text-primary mb-3" style="width: 3.2rem; height: 3.2rem;" role="status"></div>
        <h6 class="fw-bold mb-1 text-white fs-16px">Memproses Data Rakitan...</h6>
        <small class="text-white-50 fs-12px">Mohon tunggu sebentar</small>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        let html5QrCode = null;
        let cameraRunning = false;
        let availableCameras = [];
        let selectedCameraId = null;
        let currentCamIndex = 0;

        const qrisCameraSelect = document.getElementById('qris-camera-select');
        const qrisCameraWrapper = document.getElementById('qris-camera-select-wrapper');

        function showLoadingOverlay() {
            const overlay = document.getElementById('qris-loading-overlay');
            if (overlay) {
                overlay.classList.remove('d-none');
                overlay.classList.add('d-flex');
            }
        }

        function hideLoadingOverlay() {
            const overlay = document.getElementById('qris-loading-overlay');
            if (overlay) {
                overlay.classList.add('d-none');
                overlay.classList.remove('d-flex');
            }
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

        async function handleDecodedText(decodedText) {
            // Show fullscreen loading overlay immediately!
            showLoadingOverlay();

            await stopCamera();

            const parts = decodedText.split('|');
            const idValue = parts[0]?.trim();

            if (!idValue) {
                hideLoadingOverlay();
                alert('Format QR tidak valid.');
                restartCameraScan();
                return;
            }

            try {
                const response = await fetch(`{{ route('rakitan.data') }}?id=${idValue}`);
                const result = await response.json();

                hideLoadingOverlay();

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

                    // Switch to Full Page Result ONLY AFTER data is ready
                    const scannerView = document.getElementById('camera-scanner-view');
                    const resultContainer = document.getElementById('qris-fullpage-result-container');

                    if (scannerView) scannerView.classList.add('d-none');
                    if (resultContainer) resultContainer.classList.remove('d-none');
                    document.body.style.overflow = 'auto';
                } else {
                    alert('Data tidak ditemukan.');
                    restartCameraScan();
                }
            } catch (err) {
                hideLoadingOverlay();
                alert('Terjadi kesalahan koneksi.');
                restartCameraScan();
            }
        }

        function restartCameraScan() {
            const scannerView = document.getElementById('camera-scanner-view');
            const resultContainer = document.getElementById('qris-fullpage-result-container');

            if (resultContainer) resultContainer.classList.add('d-none');
            if (scannerView) scannerView.classList.remove('d-none');
            document.body.style.overflow = 'hidden';

            startScannerMobile();
        }

        function loadCameraList() {
            return Html5Qrcode.getCameras().then(cameras => {
                availableCameras = cameras;
                const dropdownList = document.getElementById('cameraDropdownList');

                if (cameras && cameras.length > 0) {
                    if (qrisCameraSelect) qrisCameraSelect.innerHTML = '';
                    if (dropdownList) dropdownList.innerHTML = '';

                    cameras.forEach((cam, idx) => {
                        const opt = document.createElement('option');
                        opt.value = cam.id;
                        opt.textContent = cam.label || `Kamera ${idx + 1}`;

                        const labelLower = (cam.label || '').toLowerCase();
                        if (labelLower.includes('back') || labelLower.includes('rear') || idx === cameras
                            .length - 1) {
                            opt.selected = true;
                            currentCamIndex = idx;
                        }

                        if (qrisCameraSelect) qrisCameraSelect.appendChild(opt);

                        if (dropdownList) {
                            const li = document.createElement('li');
                            const a = document.createElement('a');
                            a.className =
                                `dropdown-item d-flex align-items-center justify-content-between py-2 ${idx === currentCamIndex ? 'active fw-bold' : ''}`;
                            a.href = 'javascript:void(0);';
                            a.innerHTML =
                                `<span>${cam.label || 'Kamera ' + (idx + 1)}</span> ${idx === currentCamIndex ? '<i data-feather="check" class="icon-xs ms-2"></i>' : ''}`;
                            a.onclick = () => switchMobileCamera(cam.id, idx);
                            li.appendChild(a);
                            dropdownList.appendChild(li);
                        }
                    });

                    if (feather) feather.replace();
                    if (cameras.length > 1 && qrisCameraWrapper) {
                        qrisCameraWrapper.classList.remove('d-none');
                    }
                    selectedCameraId = cameras[currentCamIndex].id;
                }
            }).catch(err => {
                console.warn("Could not list cameras:", err);
            });
        }

        function startScannerMobile() {
            if (html5QrCode && cameraRunning) return;

            html5QrCode = new Html5Qrcode("qris-reader");
            const config = {
                fps: 15,
                aspectRatio: 1.0
            };

            loadCameraList().then(() => {
                const cameraConfig = selectedCameraId ? {
                    deviceId: {
                        exact: selectedCameraId
                    }
                } : {
                    facingMode: "environment"
                };

                html5QrCode.start(
                    cameraConfig,
                    config,
                    (decodedText) => handleDecodedText(decodedText),
                    (errorMessage) => {}
                ).then(() => {
                    cameraRunning = true;
                }).catch(err => {
                    console.error("Mobile camera start error:", err);
                    document.getElementById('permission-alert-mobile')?.classList.remove('d-none');
                });
            });
        }

        function switchMobileCamera(deviceId, idx) {
            selectedCameraId = deviceId;
            currentCamIndex = idx;
            if (cameraRunning && html5QrCode) {
                html5QrCode.stop().then(() => {
                    cameraRunning = false;
                    startScannerMobile();
                });
            }
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

        // Direct camera execution on page DOM load
        document.addEventListener('DOMContentLoaded', () => {
            startScannerMobile();
        });
    </script>
@endsection
