@extends('layouts.app')

@push('styles')
<style>
    .top-header-bar {
        background: transparent;
        padding: 4px 6px 12px 6px;
        font-size: 14px;
    }
    .main-card {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    /* Header Tabel Warna Abu-abu */
    .table-summary th {
        background-color: #6c757d !important;
        color: #ffffff !important;
        font-weight: 600;
        vertical-align: middle;
        border-color: #5c636a !important;
    }
    .table-summary td {
        vertical-align: middle;
    }
    .evaluator-btn {
        border: 1px solid #ced4da;
        background-color: #f8f9fa;
        color: #212529;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .evaluator-btn.active {
        background-color: #0d6efd;
        color: #ffffff;
        border-color: #0d6efd;
    }
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .clickable-row:hover {
        background-color: #f8f9fa !important;
    }
    /* Banner Header ala Form 360 / Master */
    .form-header-banner {
        background-color: #6c757d;
        color: white;
        padding: 16px 20px;
        border-radius: 6px 6px 0 0;
        display: flex;
        align-items: center;
    }
    .read-only-field {
        background-color: #f8f9fa !important;
        border: 1px solid #dee2e6;
        color: #495057;
        font-size: 13.5px;
        padding: 8px 12px;
        border-radius: 4px;
        width: 100%;
    }
</style>
@endpush

@section('content')
<!-- TOP BREADCRUMB & HEADER BAR -->
<div class="d-flex justify-content-between align-items-center top-header-bar">
    <div class="d-flex align-items-center">
        <i class="fas fa-ellipsis-h text-muted me-2" style="font-size: 13px;"></i>
        <span class="fw-medium text-dark" style="font-size: 14.5px;">Penilaian > Summary Per User</span>
    </div>
    <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 13px;">
        <span id="liveClock" class="fw-normal"></span>
        <i class="fas fa-expand cursor-pointer text-muted" title="Fullscreen" onclick="toggleFullscreen()"></i>
    </div>
</div>

<!-- CONTAINER UTAMA (TABLE VIEW) -->
<div id="summaryTableView" class="main-card p-4">
    <!-- Header Banner dengan Abu-abu Agak Tua -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded text-white" style="background-color: #6c757d; border: 1px solid #5c636a;">
        <div>
            <h4 class="fw-bold text-white mb-1">Ringkasan Penilaian Per Karyawan</h4>
            <p class="text-light small mb-0" style="opacity: 0.9;">Klik pada salah satu baris karyawan untuk melihat detail lengkap ala Penilaian 360.</p>
        </div>
        <div class="d-flex gap-2">
            <input type="text" id="searchUser" class="form-control form-control-sm bg-white text-dark" placeholder="Cari Karyawan..." style="width: 200px;" oninput="filterUserTable(this.value)">
            <button class="btn btn-light btn-sm px-3 text-dark fw-medium" onclick="loadSummaryFromApi()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>
    </div>

    <!-- TABEL SUMMARY -->
    <div class="table-responsive">
        <table class="table table-bordered table-summary align-middle" style="font-size: 13.5px;">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th style="width: 200px;">Nama Karyawan</th>
                    <th style="width: 120px;" class="text-center">Total Penilai</th>
                    <th>Daftar Penilai & Skor Singkat</th>
                    <th class="text-center" style="width: 130px;">Rata-Rata Akhir</th>
                </tr>
            </thead>
            <tbody id="userSummaryTableBody">
                <!-- Data dimuat via JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- CONTAINER DETAIL VIEW (MENGGUNAKAN BANNER HEADER SEPERTI GAMBAR) -->
<div id="summaryDetailView" class="main-card d-none">
    <!-- Header Banner Abu-abu Agak Tua -->
    <div class="form-header-banner" style="background-color: #6c757d; border-radius: 6px 6px 0 0; padding: 16px 20px; display: flex; align-items: center; color: white;">
        <i class="fas fa-arrow-left fa-lg me-3 cursor-pointer" onclick="backToTable()" title="Kembali"></i>
        <div>
            <h5 class="mb-0 fw-bold text-white">Summary Penilaian 360</h5>
            <small class="text-light" style="opacity: 0.95;">Ringkasan evaluasi oleh atasan, rekan, bawahan & diri sendiri</small>
        </div>
    </div>

    <div class="p-4 bg-white">
        <!-- Informasi Karyawan (Grid 3 Kolom) -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Karyawan Yang Dinilai</label>
                <div id="detNamaKaryawan" class="read-only-field fw-semibold">-</div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Jabatan Karyawan</label>
                <div id="detJabatan" class="read-only-field">-</div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Divisi</label>
                <div id="detDivisi" class="read-only-field">-</div>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Level Karyawan</label>
                <div id="detLevel" class="read-only-field">-</div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Periode</label>
                <div id="detPeriode" class="read-only-field">2026/Q3</div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Penilai (Klik untuk ubah view penilai)</label>
                <div id="evaluatorButtonsContainer" class="d-flex flex-wrap gap-2 mt-1">
                    <!-- Tombol pilihan penilai akan dirender di sini -->
                </div>
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Hubungan Penilai</label>
                <div id="detHubungan" class="read-only-field">-</div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Tipe Penilaian</label>
                <div id="detTipe" class="read-only-field">Kompetensi SoftSkill</div>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">Penilaian (Pertanyaan / KPI)</label>
                <div id="detKategoriPenilaian" class="read-only-field">Penilaian Direktur</div>
            </div>
        </div>

        <!-- Bagian Feedback 360 Blocks -->
        <div class="card border shadow-sm mt-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="fw-bold text-dark mb-0">Penilaian Feedback 360</h5>
                <div>
                    <span class="text-muted small me-2">Skor Penilai</span>
                    <span id="detTotalScoreBadge" class="badge bg-primary" style="font-size: 14px; padding: 6px 12px;">0.00</span>
                </div>
            </div>
            <div class="card-body p-0" id="feedbackBlocksContainer">
                <!-- Komponen pertanyaan dirender di sini -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Jam Live
    function updateClock() {
        const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const now = new Date();
        const clockEl = document.getElementById('liveClock');
        if (clockEl) {
            clockEl.innerText = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')}`;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Data summary dimuat dari backend (talent_db)
    var dummyUserSummary = [];

    async function loadSummaryFromApi() {
        const tbody = document.getElementById('userSummaryTableBody');
        try {
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">Memuat data...</td></tr>`;
            }
            const res = await fetch('http://localhost:3000/api/penilaian-summary');
            const json = await res.json().catch(() => ({}));
            if (!res.ok || json.status !== 'success') {
                throw new Error(json.message || 'Gagal mengambil data summary');
            }
            dummyUserSummary = Array.isArray(json.data) ? json.data : [];
            loadUserSummaryData();
        } catch (err) {
            console.error('Gagal memuat summary penilaian:', err);
            if (tbody) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Gagal memuat data summary: ${err.message}</td></tr>`;
            }
        }
    }

    var activeSelectedUser = null;

    function loadUserSummaryData(filter = '') {
        const tbody = document.getElementById('userSummaryTableBody');
        let filtered = dummyUserSummary;

        if (filter) {
            filtered = dummyUserSummary.filter(item => item.nama_karyawan.toLowerCase().includes(filter.toLowerCase()));
        }

        if (filtered.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada data ditemukan.</td></tr>`;
            return;
        }

        let html = '';
        filtered.forEach((user, idx) => {
            let totalSkorAll = 0;
            let countEvaluator = user.evaluators.length;
            let evaluatorBadges = '';

            user.evaluators.forEach(ev => {
                totalSkorAll += ev.skor_rata;
                evaluatorBadges += `<span class="evaluator-badge"><strong>${ev.nama}</strong>: <span class="text-dark fw-bold">${ev.skor_rata}</span></span>`;
            });

            let avgScore = countEvaluator > 0 ? (totalSkorAll / countEvaluator).toFixed(2) : '0.00';

            html += `
                <tr class="clickable-row" onclick="showUserDetailView(${user.id})">
                    <td class="text-center text-muted">${idx + 1}</td>
                    <td class="fw-semibold text-dark">${user.nama_karyawan}<br><small class="text-muted fw-normal">${user.jabatan}</small></td>
                    <td class="text-center"><span class="badge bg-secondary">${countEvaluator} Orang</span></td>
                    <td>${evaluatorBadges}</td>
                    <td class="text-center fw-bold text-success" style="font-size: 15px;">${avgScore}</td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    // Fungsi menampilkan detail ala Form 360 dengan opsi tombol ganti penilai
    function showUserDetailView(userId, evaluatorId = null) {
        const user = dummyUserSummary.find(u => u.id === userId);
        if (!user) return;
        activeSelectedUser = user;

        const evaluator = evaluatorId ? user.evaluators.find(e => e.id_ev === evaluatorId) : user.evaluators[0];

        document.getElementById('detNamaKaryawan').innerText = user.nama_karyawan;
        document.getElementById('detJabatan').innerText = user.jabatan;
        document.getElementById('detDivisi').innerText = user.divisi || 'Information Technology';
        document.getElementById('detLevel').innerText = user.level || 'Staff';
        document.getElementById('detHubungan').innerText = evaluator.hubungan || '-';
        document.getElementById('detTotalScoreBadge').innerText = evaluator.skor_rata ? evaluator.skor_rata.toFixed(2) : '0.00';

        // Render Tombol Pilihan Penilai di form atas
        let evalBtnsHtml = '';
        user.evaluators.forEach(ev => {
            let isActive = (ev.id_ev === evaluator.id_ev) ? 'active' : '';
            evalBtnsHtml += `<button type="button" class="evaluator-btn ${isActive}" onclick="switchEvaluator(${user.id}, ${ev.id_ev})">${ev.nama} (${ev.skor_rata})</button>`;
        });
        document.getElementById('evaluatorButtonsContainer').innerHTML = evalBtnsHtml;

        // Render komponen feedback blocks dengan struktur tabel yang rapi
        let blocksHtml = '';
        if (evaluator.komponen && evaluator.komponen.length > 0) {
            evaluator.komponen.forEach((comp) => {
                blocksHtml += `
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered mb-0 align-middle">
                            <thead>
                                <tr class="bg-primary text-white">
                                    <th colspan="5" class="py-3 px-3 bg-primary text-white border-0" style="background-color: #0d6efd !important;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span style="font-size: 14px; font-weight: 600; max-width: 65%;">${comp.pertanyaan}</span>
                                            <div class="d-flex gap-2">
                                                <span class="badge bg-dark px-2 py-1">Bobot: ${comp.bobot}</span>
                                                <span class="badge bg-dark px-2 py-1">Rating: ${comp.rating}</span>
                                                <span class="badge bg-dark px-2 py-1">Nilai: ${comp.nilai}</span>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                `;

                comp.pilihan.forEach((pil, pIdx) => {
                    let isSelected = (pIdx === comp.selected_index);
                    let bgStyle = isSelected ? 'background-color: #e7f1ff; border: 2px solid #0d6efd;' : 'background-color: #ffffff;';
                    blocksHtml += `
                        <td class="p-3 text-center align-top" style="width: 20%; ${bgStyle}">
                            <div class="small text-dark" style="font-size: 12.5px; line-height: 1.4;">${pil}</div>
                            ${isSelected ? '<div class="mt-3"><span class="badge bg-primary">Pilihan Penilai</span></div>' : ''}
                        </td>
                    `;
                });

                blocksHtml += `</tr></tbody></table></div>`;
            });
        } else {
            blocksHtml = `<div class="p-4 text-center text-muted">Tidak ada rincian komponen penilaian.</div>`;
        }

        document.getElementById('feedbackBlocksContainer').innerHTML = blocksHtml;

        // Transisi Tampilan
        document.getElementById('summaryTableView').classList.add('d-none');
        document.getElementById('summaryDetailView').classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function switchEvaluator(userId, evaluatorId) {
        showUserDetailView(userId, evaluatorId);
    }

    function backToTable() {
        document.getElementById('summaryDetailView').classList.add('d-none');
        document.getElementById('summaryTableView').classList.remove('d-none');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function filterUserTable(val) {
        loadUserSummaryData(val);
    }

    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(() => {});
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }

    function initSummaryPage() {
        if (!document.getElementById('userSummaryTableBody')) return;
        loadSummaryFromApi();
    }

    if (window.registerPageInit) {
        window.registerPageInit(initSummaryPage);
    } else {
        document.addEventListener('DOMContentLoaded', initSummaryPage);
    }
</script>
@endpush