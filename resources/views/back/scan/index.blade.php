@extends('layouts.layout')

@section('content')
    <div class="container py-4">
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
                            <div id="reader" class="mx-auto mb-2" style="width: 100%; max-width: 420px;"></div>
                            <button id="btn-cancel" class="btn btn-outline-secondary btn-sm mb-2" onclick="cancelScanner()">
                                <i class="bx bx-x me-1"></i>Close Camera
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
                                                <td class="py-2 text-primary">
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
        const scanInput = document.getElementById('scan-input');

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

        function startScanner() {
            document.getElementById('start-box').classList.add('d-none');
            document.getElementById('scanner-box').classList.remove('d-none');
            html5QrCode = new Html5Qrcode("reader");
            Html5Qrcode.getCameras().then(cameras => {
                const cameraId = cameras[cameras.length - 1].id;
                html5QrCode.start(cameraId, { fps: 10, qrbox: 280 }, (decodedText) => {
                    stopCamera().then(() => handleDecodedText(decodedText));
                }).then(() => cameraRunning = true);
            }).catch(err => showError('Camera Init Failed.'));
        }

        function stopCamera() {
            if (!html5QrCode || !cameraRunning) return Promise.resolve();
            return html5QrCode.stop().then(() => { cameraRunning = false; document.getElementById('reader').innerHTML = ''; }).catch(() => {});
        }

        function cancelScanner() { stopCamera().then(() => {
            document.getElementById('scanner-box').classList.add('d-none');
            document.getElementById('start-box').classList.remove('d-none');
        });}

        function showError(msg) {
            stopCamera().then(() => { document.getElementById('error-msg').textContent = msg; showPanel('error-box'); });
        }

        if (!isMobile()) {
            document.addEventListener('click', e => { if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'A') scanInput.focus(); });
            scanInput.focus();
        }
    </script>
@endsection
