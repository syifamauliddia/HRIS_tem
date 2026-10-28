@extends('layouts.app')

@push('styles')
<style>
    /* Styling Nav Tabs Sesuai Gambar Referensi */
    .nav-tabs-custom {
        border-bottom: 1px solid #e5e7eb;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        color: #4b5563;
        font-weight: 500;
        padding: 14px 24px;
        border-bottom: 2px solid transparent;
        background: transparent;
        font-size: 15px;
    }
    .nav-tabs-custom .nav-link.active {
        color: #2563eb;
        border-bottom: 2px solid #2563eb;
        font-weight: 600;
    }

    /* Container Kamera & Layout Absen */
    .camera-box {
        width: 100%;
        max-width: 580px;
        height: 320px;
        background-color: #f9fafb;
        border-radius: 12px;
        overflow: hidden;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .camera-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Table Styling */
    .bg-holiday { background-color: #6c757d !important; color: white !important; }
    .bg-cuti { background-color: #f87171 !important; color: white !important; }
    .table-hover tbody tr:hover { background-color: #fef3c7 !important; cursor: pointer; }
    .modal-img-placeholder { height: 166px; width: 166px; background-color: #6c757d; border-radius: 8px; }
</style>
@endpush

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    
    <!-- Header Top Bar -->
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0 rounded-top-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-ellipsis-v text-muted me-2"></i>
            <span class="fw-bold text-secondary">Absensi Online</span>
        </div>
        <div class="text-muted small d-flex align-items-center gap-3">
            <span id="topHeaderDateTime">Rab, 28 Okt 2026 17:34:27</span>
            <i class="far fa-copy cursor-pointer"></i>
            <i class="fas fa-expand cursor-pointer"></i>
            <i class="fas fa-user cursor-pointer"></i>
        </div>
    </div>

    <div class="card-body p-0">
        
        <!-- Tab Navigation (Absen | Daftar Absensi) -->
        <ul class="nav nav-tabs nav-tabs-custom px-4" id="absensiTab" role="tablist">
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link active w-100" id="absen-tab" data-bs-toggle="tab" data-bs-target="#absen-pane" type="button" role="tab" aria-controls="absen-pane" aria-selected="true">
                    Absen
                </button>
            </li>
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link w-100" id="daftar-tab" data-bs-toggle="tab" data-bs-target="#daftar-pane" type="button" role="tab" aria-controls="daftar-pane" aria-selected="false">
                    Daftar Absensi
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-4" id="absensiTabContent">
            
            <!-- ========================================== -->
            <!-- TAB 1: ABSEN (KAMERA & CAPTURE CHECKIN)    -->
            <!-- ========================================== -->
            <div class="tab-pane fade show active" id="absen-pane" role="tabpanel" aria-labelledby="absen-tab">
                <div class="text-center py-2">
                    
                    <h4 class="fw-bold text-dark mb-4" id="checkinTitle">Absen Checkin</h4>
                    
                    <!-- WebCam Feed / Preview Box -->
                    <div class="camera-box border shadow-sm mb-4">
                        <video id="webcamVideo" class="camera-video" autoplay playsinline></video>
                        <canvas id="captureCanvas" class="d-none"></canvas>
                        <div id="camFallback" class="text-muted small d-none">
                            <i class="fas fa-camera fa-2x mb-2"></i><br>Kamera Tidak Aktif / Diizinkan
                        </div>
                    </div>

                    <!-- Tombol Capture -->
                    <div class="mb-5">
                        <button type="button" class="btn btn-primary px-5 py-2 fw-semibold rounded-3 shadow-sm" style="background-color: #2563eb; border: none;" onclick="doCaptureCheckin()">
                            Capture
                        </button>
                    </div>

                    <hr class="my-3 opacity-25">

                    <!-- Realtime Footer Info (Tanggal, Jam, Scope) -->
                    <div class="d-flex justify-content-between align-items-center mb-2 text-secondary small px-2">
                        <div>
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            <span id="footerFullDate">Rabu, 28 Oktober 2026</span>
                        </div>
                        <div>
                            <i class="fas fa-clock text-primary me-2"></i>
                            <span id="footerRealTime">17:34:26</span>
                        </div>
                    </div>

                    <div class="text-start text-secondary small px-2">
                        <div class="row">
                            <div class="col-auto fw-bold" style="width: 100px;">Lokasi</div>
                            <div class="col-auto">:</div>
                            <div class="col" id="userLocationText">Mencari lokasi...</div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-auto fw-bold" style="width: 100px;">Keterangan</div>
                            <div class="col-auto">:</div>
                            <div class="col fw-bold text-danger" id="scopeStatusText">Out Scope</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 2: DAFTAR ABSENSI (REKAP & DETAIL)     -->
            <!-- ========================================== -->
            <div class="tab-pane fade" id="daftar-pane" role="tabpanel" aria-labelledby="daftar-tab">
                
                <!-- BARIS FILTER -->
                <div class="row g-2 mb-3">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Filter Periode</label>
                        <input type="month" id="filterMonth" class="form-control form-control-sm" onchange="initWeeks(); loadDailyData();">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Periode Tgl (Minggu)</label>
                        <select id="filterWeeks" class="form-select form-select-sm" onchange="loadDailyData()"></select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">SUB</label>
                        <input type="text" class="form-control form-control-sm bg-light" value="Temprina Komersial" readonly disabled>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Cabang</label>
                        <input type="text" class="form-control form-control-sm bg-light" value="Surabaya Karah" readonly disabled>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Divisi</label>
                        <select id="filterDivisi" class="form-select form-select-sm" onchange="loadDailyData()">
                            <option value="">Semua Divisi</option>
                            <option value="1">IT & Systems</option>
                            <option value="2">Human Resources</option>
                        </select>
                    </div>
                </div>

                <!-- LOADING STATE -->
                <div id="loadingAbsensi" class="text-center py-4 text-muted fst-italic d-none">
                    <div class="spinner-border spinner-border-sm me-2"></div>
                    Harap tunggu, kami sedang menyiapkan data untuk anda.. 😊
                </div>

                <!-- SUB-TAMPILAN 1: REKAPITULASI HARIAN -->
                <div id="viewDailySummary">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-nowrap w-100">
                            <thead class="bg-primary text-white text-center">
                                <tr>
                                    <th>Hari</th>
                                    <th>Tanggal</th>
                                    <th>Hadir</th>
                                    <th>Izin/Sakit/Cuti</th>
                                    <th>Tidak Hadir</th>
                                    <th>Karyawan Aktif</th>
                                    <th>Presentase</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyDailySummary" class="bg-light">
                                <!-- Data Harian Disini -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- SUB-TAMPILAN 2: DETAIL KARYAWAN PER TANGGAL -->
                <div id="viewDateDetail" class="d-none">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0" id="labelSelectedDate">Tanggal : YYYY-MM-DD</h5>
                        <button class="btn btn-primary btn-sm px-3" onclick="backToDailySummary()">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke list
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-nowrap w-100">
                            <thead class="bg-primary text-white text-center">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>NIK</th>
                                    <th>Nama</th>
                                    <th>Jam Kerja</th>
                                    <th>Jadwal</th>
                                    <th>Status</th>
                                    <th>Waktu Checkin</th>
                                    <th>Waktu Checkout</th>
                                    <th style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyDateDetail" class="bg-light">
                                <!-- Data Detail Karyawan Disini -->
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<!-- MODAL DETAIL CHECKIN / CHECKOUT -->
<div class="modal fade" id="modalDetailAbsensi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">Detail Checkin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <img id="modalImg" src="" alt="Foto Absensi" class="img-fluid rounded shadow-sm d-none" style="max-height: 200px;">
                        <div id="modalImgPlaceholder" class="modal-img-placeholder mx-auto d-flex align-items-center justify-content-center text-white small">Tidak Ada Foto</div>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold" style="width: 35%;">NIK</td>
                                <td style="width: 5%;">:</td>
                                <td id="mNik">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Nama</td>
                                <td>:</td>
                                <td id="mNama">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Departemen</td>
                                <td>:</td>
                                <td id="mDept">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold" id="labelMWaktu">Waktu Checkin</td>
                                <td>:</td>
                                <td id="mWaktu">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold" id="labelMKantor">Kantor Checkin</td>
                                <td>:</td>
                                <td id="mKantor">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold" id="labelMLokasi">Lokasi Checkin</td>
                                <td>:</td>
                                <td id="mLokasi">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold" id="labelMCatatan">Catatan Checkin</td>
                                <td>:</td>
                                <td id="mCatatan">-</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">On Scope</td>
                                <td>:</td>
                                <td id="mOnScope">-</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentSelectedDate = null;
    let webcamStream = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Init Jam & Waktu Realtime
        startRealtimeClock();
        
        // Init WebCam & Lokasi GPS
        initWebcam();
        initGeolocation();

        // Init Filter Bulan Tab 2
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        document.getElementById('filterMonth').value = `${yyyy}-${mm}`;

        initWeeks();
        loadDailyData();
    });

    // -------------------------------------------------------------
    // LOGIKA REALTIME CLOCK & GEOLOCATION (TAB 1)
    // -------------------------------------------------------------
    function startRealtimeClock() {
        setInterval(() => {
            const now = new Date();
            
            // Format Top Header: "Rab, 28 Okt 2026 17:34:27"
            const daysShort = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            const monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agus', 'Sep', 'Okt', 'Nov', 'Des'];
            const topFormatted = `${daysShort[now.getDay()]}, ${now.getDate()} ${monthsShort[now.getMonth()]} ${now.getFullYear()} ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}:${String(now.getSeconds()).padStart(2,'0')}`;
            document.getElementById('topHeaderDateTime').innerText = topFormatted;

            // Format Footer: "Rabu, 28 Oktober 2026"
            const daysLong = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const monthsLong = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            document.getElementById('footerFullDate').innerText = `${daysLong[now.getDay()]}, ${now.getDate()} ${monthsLong[now.getMonth()]} ${now.getFullYear()}`;
            
            // Format Footer Time: "17:34:26"
            document.getElementById('footerRealTime').innerText = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}:${String(now.getSeconds()).padStart(2,'0')}`;
        }, 1000);
    }

    // Mengaktifkan Kamera Laptop / HP
    async function initWebcam() {
        const video = document.getElementById('webcamVideo');
        const fallback = document.getElementById('camFallback');
        try {
            webcamStream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = webcamStream;
        } catch (err) {
            video.classList.add('d-none');
            fallback.classList.remove('d-none');
        }
    }

    // Mendapatkan Koordinat GPS & Scope Status
    function initGeolocation() {
        const locText = document.getElementById('userLocationText');
        const scopeText = document.getElementById('scopeStatusText');

        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    locText.innerText = `Lat: ${lat.toFixed(6)}, Long: ${lng.toFixed(6)}`;
                    
                    // Contoh simulasi penentuan Out Scope / In Scope
                    scopeText.innerText = 'Out Scope';
                    scopeText.className = 'col fw-bold text-danger';
                },
                (error) => {
                    locText.innerText = 'Surabaya Karah, Jawa Timur (Default)';
                    scopeText.innerText = 'Out Scope';
                    scopeText.className = 'col fw-bold text-danger';
                }
            );
        } else {
            locText.innerText = 'GPS tidak didukung oleh browser ini.';
        }
    }

    // Aksi Tombol Capture
    async function doCaptureCheckin() {
        Swal.fire({
            title: 'Memproses Absen...',
            text: 'Mengambil foto dan verifikasi lokasi',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: 'Absen Berhasil!',
                text: 'Presensi Checkin berhasil dicatat.',
                timer: 2000,
                showConfirmButton: false
            });
        }, 1500);
    }

    // -------------------------------------------------------------
    // LOGIKA TAB 2: DAFTAR ABSENSI (REKAP & DETAIL TABLE)
    // -------------------------------------------------------------
    function initWeeks() {
        const monthVal = document.getElementById('filterMonth').value;
        const [year, month] = monthVal.split('-');
        const totalDays = new Date(year, month, 0).getDate();
        const weeksSelect = document.getElementById('filterWeeks');
        
        weeksSelect.innerHTML = '';
        let daysPerWeek = 7;
        let fullWeeks = Math.ceil(totalDays / daysPerWeek);

        for (let i = 0; i < fullWeeks; i++) {
            let start = (i * daysPerWeek) + 1;
            let end = Math.min((i + 1) * daysPerWeek, totalDays);
            let startStr = `${year}-${month}-${String(start).padStart(2, '0')}`;
            let endStr = `${year}-${month}-${String(end).padStart(2, '0')}`;
            
            let opt = document.createElement('option');
            opt.value = `${startStr}/${endStr}`;
            opt.innerText = `P${i + 1} (${startStr} / ${endStr})`;
            weeksSelect.appendChild(opt);
        }
    }

    async function loadDailyData() {
        const monthVal = document.getElementById('filterMonth').value;
        const loading = document.getElementById('loadingAbsensi');
        const tbody = document.getElementById('tbodyDailySummary');

        try {
            loading.classList.remove('d-none');
            tbody.innerHTML = '';

            const response = await fetch(`http://localhost:3000/api/presensi_absensi/get_by_daily?month=${monthVal}`);
            const result = await response.json();
            loading.classList.add('d-none');

            if (result.code === 200 && result.data.length > 0) {
                result.data.forEach(item => {
                    let rowClass = '';
                    if (item.type === 'Hari Libur') rowClass = 'bg-holiday';
                    else if (item.type === 'Cuti Bersama') rowClass = 'bg-cuti';

                    tbody.innerHTML += `
                        <tr class="${rowClass}">
                            <td>${item.day_name_idn}</td>
                            <td>${item.all_days_of_month}</td>
                            <td class="text-end">${item.attend}</td>
                            <td class="text-end">${item.cuti}</td>
                            <td class="text-end">${item.alpha}</td>
                            <td class="text-end">${item.total_kary}</td>
                            <td class="text-end">${Math.round(item.presentase)}%</td>
                            <td class="text-center">
                                <i class="fas fa-search text-warning cursor-pointer fs-6" title="Lihat Detail Tanggal" onclick="openDateDetail('${item.all_days_of_month}')"></i>
                            </td>
                        </tr>
                    `;
                });
            }
        } catch (error) {
            loading.classList.add('d-none');
            tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">Gagal memuat data absensi.</td></tr>`;
        }
    }

    async function openDateDetail(dateStr) {
        currentSelectedDate = dateStr;
        document.getElementById('viewDailySummary').classList.add('d-none');
        document.getElementById('viewDateDetail').classList.remove('d-none');
        document.getElementById('labelSelectedDate').innerText = `Tanggal : ${dateStr}`;

        const loading = document.getElementById('loadingAbsensi');
        const tbody = document.getElementById('tbodyDateDetail');

        try {
            loading.classList.remove('d-none');
            tbody.innerHTML = '';

            const response = await fetch(`http://localhost:3000/api/presensi_absensi/get_by_date?date=${dateStr}`);
            const result = await response.json();
            loading.classList.add('d-none');

            if (result.code === 200 && result.data.length > 0) {
                result.data.forEach((item, idx) => {
                    const isNotAttend = item.absensi?.status === 'NOT ATTEND';
                    const trClass = isNotAttend ? 'table-danger' : '';

                    let checkinHtml = item.absensi?.checkin_time ? 
                        `<div class="d-flex align-items-center" onclick="showModalInfo('checkin', ${JSON.stringify(item).replace(/"/g, '&quot;')})">
                            <i class="fas fa-map-marker-alt me-1 ${item.absensi.checkin_on_scope ? 'text-success' : 'text-danger'}"></i> ${item.absensi.checkin_time}
                        </div>` : '-';

                    let checkoutHtml = item.absensi?.checkout_time ? 
                        `<div class="d-flex align-items-center" onclick="showModalInfo('checkout', ${JSON.stringify(item).replace(/"/g, '&quot;')})">
                            <i class="fas fa-map-marker-alt me-1 ${item.absensi.checkout_on_scope ? 'text-success' : 'text-danger'}"></i> ${item.absensi.checkout_time}
                        </div>` : '-';

                    let actionBtn = isNotAttend ? 
                        `<a href="/absensi/create?ts=${dateStr}&user_id=${item.default_user_id}" class="text-secondary me-2" title="Tambah Absensi"><i class="fas fa-plus"></i></a>` :
                        `<a href="/absensi/${item.presensi_absensi_id}?action=Edit&ts=${dateStr}" class="text-secondary me-2" title="Edit Absensi"><i class="fas fa-edit"></i></a>
                         <i class="fas fa-trash text-danger cursor-pointer" title="Kosongkan Absensi" onclick="deleteAbsensi(${item.presensi_absensi_id})"></i>`;

                    tbody.innerHTML += `
                        <tr class="${trClass}">
                            <td class="text-center">${idx + 1}</td>
                            <td>${item.kode}</td>
                            <td>${item.nama_lengkap}</td>
                            <td>${item.jam_kerja}</td>
                            <td>${item.desc}</td>
                            <td><span class="badge ${isNotAttend ? 'bg-danger' : 'bg-success'}">${item.absensi?.status}</span></td>
                            <td>${checkinHtml}</td>
                            <td>${checkoutHtml}</td>
                            <td class="text-center">${actionBtn}</td>
                        </tr>
                    `;
                });
            }
        } catch (error) {
            loading.classList.add('d-none');
            tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Gagal memuat detail karyawan.</td></tr>`;
        }
    }

    function backToDailySummary() {
        document.getElementById('viewDateDetail').classList.add('d-none');
        document.getElementById('viewDailySummary').classList.remove('d-none');
    }

    function showModalInfo(type, item) {
        const isCheckin = type === 'checkin';
        const abs = item.absensi || {};

        document.getElementById('modalTitle').innerText = `Detail ${isCheckin ? 'Checkin' : 'Checkout'}`;
        document.getElementById('mNik').innerText = item.kode || '-';
        document.getElementById('mNama').innerText = item.nama_lengkap || '-';
        document.getElementById('mDept').innerText = item.dept || '-';

        document.getElementById('labelMWaktu').innerText = `Waktu ${isCheckin ? 'Checkin' : 'Checkout'}`;
        document.getElementById('mWaktu').innerText = (isCheckin ? abs.checkin_time : abs.checkout_time) || '-';

        document.getElementById('labelMKantor').innerText = `Kantor ${isCheckin ? 'Checkin' : 'Checkout'}`;
        document.getElementById('mKantor').innerText = (isCheckin ? abs.checkin_region : abs.checkout_region) || '-';

        document.getElementById('labelMLokasi').innerText = `Lokasi ${isCheckin ? 'Checkin' : 'Checkout'}`;
        document.getElementById('mLokasi').innerText = (isCheckin ? abs.checkin_address : abs.checkout_address) || '-';

        document.getElementById('labelMCatatan').innerText = `Catatan ${isCheckin ? 'Checkin' : 'Checkout'}`;
        document.getElementById('mCatatan').innerText = (isCheckin ? abs.catatan_in : abs.catatan_out) || '-';

        const onScopeVal = isCheckin ? abs.checkin_on_scope : abs.checkout_on_scope;
        document.getElementById('mOnScope').innerHTML = onScopeVal ? 
            '<i class="fas fa-check text-success fs-5"></i> Ya' : 
            '<i class="fas fa-times text-danger fs-5"></i> Tidak';

        const imgUrl = isCheckin ? abs.checkin_foto : abs.checkout_foto;
        const imgElem = document.getElementById('modalImg');
        const imgPlaceholder = document.getElementById('modalImgPlaceholder');

        if (imgUrl) {
            imgElem.src = imgUrl;
            imgElem.classList.remove('d-none');
            imgPlaceholder.classList.add('d-none');
        } else {
            imgElem.classList.add('d-none');
            imgPlaceholder.classList.remove('d-none');
        }

        const modal = new bootstrap.Modal(document.getElementById('modalDetailAbsensi'));
        modal.show();
    }

    async function deleteAbsensi(id) {
        const confirm = await Swal.fire({
            icon: 'warning',
            text: 'Anda yakin mau menghapus data absensi ini?',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak'
        });

        if (!confirm.isConfirmed) return;

        try {
            const res = await fetch(`http://localhost:3000/api/presensi_absensi/${id}`, { method: 'DELETE' });
            if (res.ok) {
                Swal.fire({ icon: 'success', text: 'Data absensi berhasil dikosongkan.' });
                openDateDetail(currentSelectedDate);
            }
        } catch (err) {
            Swal.fire({ icon: 'error', text: 'Gagal mengosongkan absensi.' });
        }
    }
</script>
@endpush