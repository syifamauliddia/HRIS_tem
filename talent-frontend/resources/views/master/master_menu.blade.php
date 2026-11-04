@extends('layouts.app')

@section('content')
<style>
    /* Top Header Bar */
    .top-header-bar {
        background: transparent;
        padding: 4px 6px 12px 6px;
        font-size: 14px;
    }

    /* Main Card */
    .main-card {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
    }

    /* Filter Status Buttons */
    .filter-btn-active {
        border: 1px solid #198754;
        color: #198754;
        background: #ffffff;
        border-radius: 4px;
        padding: 3px 14px;
        font-size: 13.5px;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
        cursor: pointer;
    }
    .filter-btn-active.selected, .filter-btn-active:hover {
        background: #198754;
        color: #ffffff;
    }

    .filter-btn-inactive {
        border: 1px solid #dc3545;
        color: #dc3545;
        background: #ffffff;
        border-radius: 4px;
        padding: 3px 14px;
        font-size: 13.5px;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
        cursor: pointer;
    }
    .filter-btn-inactive.selected, .filter-btn-inactive:hover {
        background: #dc3545;
        color: #ffffff;
    }

    .btn-create-new {
        border: 1px solid #0d6efd;
        color: #0d6efd;
        background: #ffffff;
        border-radius: 4px;
        padding: 4px 16px;
        font-size: 13.5px;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
        cursor: pointer;
    }
    .btn-create-new:hover {
        background: #0d6efd;
        color: #ffffff;
    }

    /* Toolbar Actions */
    .tool-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        color: #ffffff;
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .tool-action-btn:hover {
        opacity: 0.88;
        color: #ffffff;
    }

    .tool-icon-btn {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #dee2e6;
        background: #ffffff;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.2s;
    }
    .tool-icon-btn:hover {
        background: #f8f9fa;
        color: #212529;
    }

    /* Table Styling */
    .master-table {
        border-color: #dee2e6;
        font-size: 13.5px;
        margin-bottom: 0;
    }
    .master-table thead th {
        background-color: #f8f9fa;
        color: #212529;
        font-weight: 600;
        border-color: #dee2e6;
        vertical-align: middle;
        padding: 8px 10px;
    }
    .master-table thead tr.sub-header-filter th {
        background-color: #ffffff;
        padding: 4px 8px;
        height: 32px;
    }
    .master-table tbody td {
        border-color: #dee2e6;
        padding: 8px 10px;
        vertical-align: middle;
        color: #212529;
    }
    .master-table tbody tr {
        cursor: pointer;
        transition: background-color 0.1s ease;
    }
    .master-table tbody tr:hover {
        background-color: #f1f5f9;
    }
    /* Highlight baris saat diklik */
    .master-table tbody tr.row-selected,
    .master-table tbody tr.row-selected td {
        background-color: #cce5ff !important;
    }

    .status-active {
        color: #198754;
        font-weight: 500;
    }
    .status-inactive {
        color: #6c757d;
        font-weight: 500;
    }
    .filter-funnel-icon {
        color: #94a3b8;
        font-size: 13px;
        cursor: pointer;
    }
    .filter-funnel-icon:hover {
        color: #475569;
    }

    /* Form Input Custom Clean (Persis Screenshot Create New) */
    .form-input-clean {
        height: 40px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
        font-size: 13.5px;
        color: #333;
        padding: 8px 14px;
        transition: border-color 0.2s ease;
    }
    .form-input-clean:focus {
        border-color: #86b7fe;
        box-shadow: none;
    }
    .form-input-clean::placeholder {
        color: #adb5bd;
        font-size: 13px;
    }
</style>

<!-- TOP BREADCRUMB & HEADER BAR -->
<div class="d-flex justify-content-between align-items-center top-header-bar">
    <div class="d-flex align-items-center">
        <i class="fas fa-ellipsis-h text-muted me-2" style="font-size: 13px;"></i>
        <span class="fw-medium text-dark" id="breadcrumbTitle" style="font-size: 14.5px;">Menu</span>
    </div>
    <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 13px;">
        <span id="liveClock" class="fw-normal">Rab, 28 Okt 2026 17:39:45</span>
        <i class="far fa-copy cursor-pointer text-muted" title="Copy / Split View" style="cursor: pointer;"></i>
        <i class="fas fa-expand cursor-pointer text-muted" title="Fullscreen" style="cursor: pointer;" onclick="toggleFullscreen()"></i>
        <div class="d-flex align-items-center gap-1 cursor-pointer" style="cursor: pointer;" title="User Profile">
            <i class="fas fa-user-circle fs-5 text-secondary"></i>
            <i class="fas fa-chevron-down text-muted" style="font-size: 10px;"></i>
        </div>
    </div>
</div>

<!-- ========================================================= -->
<!-- 1. LANDING VIEW (TABLE LIST) -->
<!-- ========================================================= -->
<div id="landingView" class="main-card">
    <!-- FILTER STATUS & CREATE NEW ROW -->
    <div class="d-flex justify-content-between align-items-center px-4 py-3">
        <div class="d-flex align-items-center">
            <span class="text-secondary fw-semibold me-3" style="font-size: 13.5px; color: #495057;">Filter Status :</span>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btnFilterActive" class="filter-btn-active" onclick="filterStatus('active')">
                    Active
                </button>
                <button type="button" id="btnFilterInactive" class="filter-btn-inactive" onclick="filterStatus('inactive')">
                    Inactive
                </button>
            </div>
        </div>
        <div>
            <button type="button" class="btn-create-new" onclick="showForm('create')">
                Create New
            </button>
        </div>
    </div>

    <hr class="m-0" style="border-top: 1px solid #e9ecef;">

    <!-- TOOLBAR (SEARCH + DYNAMIC ACTION BUTTONS + RIGHT TOOLS) -->
    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="background: #ffffff; min-height: 48px;">
        <!-- Left: Search Box & 4 Action Buttons (Hanya muncul jika salah satu baris diklik) -->
        <div class="d-flex align-items-center gap-2">
            <!-- Search Box with (Ctrl+F) -->
            <div class="input-group input-group-sm" style="width: 140px; border-radius: 4px; overflow: hidden;">
                <span class="input-group-text bg-white border-end-0 text-muted pe-1" style="border-color: #dee2e6;">
                    <i class="fas fa-search" style="font-size: 11px;"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-1 text-muted" 
                       placeholder="(Ctrl+F)" style="font-size: 12.5px; height: 32px; border-color: #dee2e6;" 
                       oninput="onSearchChange(this.value)">
            </div>

            <!-- CONTAINER ICON AKSI: Awalnya D-NONE, baru muncul jika salah satu baris diklik -->
            <div id="rowActionsGroup" class="d-none align-items-center gap-2">
                <!-- 1. Hapus (Merah) -->
                <button class="tool-action-btn" style="background-color: #dc3545;" title="Hapus" onclick="actionDelete()">
                    <i class="fas fa-trash-alt" style="font-size: 12.5px;"></i>
                </button>
                <!-- 2. Lihat / Read (Teal/Hijau) -->
                <button class="tool-action-btn" style="background-color: #0d9488;" title="Read / Lihat" onclick="actionRead()">
                    <i class="fas fa-eye" style="font-size: 12.5px;"></i>
                </button>
                <!-- 3. Edit (Biru) -->
                <button class="tool-action-btn" style="background-color: #0d6efd;" title="Edit" onclick="actionEdit()">
                    <i class="fas fa-pencil-alt" style="font-size: 12.5px;"></i>
                </button>
                <!-- 4. Copy (Abu-abu Slate) -->
                <button class="tool-action-btn" style="background-color: #64748b;" title="Copy" onclick="actionCopy()">
                    <i class="fas fa-copy" style="font-size: 12.5px;"></i>
                </button>
            </div>
        </div>

        <!-- Right: CSV, Reload, Page Size -->
        <div class="d-flex align-items-center gap-2">
            <button class="tool-icon-btn" title="Export CSV" onclick="exportCSV()">
                <i class="fas fa-file-csv" style="font-size: 15px;"></i>
            </button>
            <button class="tool-icon-btn" title="Refresh Data" onclick="loadTableData()">
                <i class="fas fa-sync-alt" style="font-size: 12px;"></i>
            </button>
            <select id="pageSizeSelect" class="form-select form-select-sm" style="width: 65px; height: 32px; font-size: 12.5px; border-color: #dee2e6;">
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <!-- TABLE AREA -->
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table master-table table-bordered mb-0 align-middle" id="mainTable">
            <thead>
                <tr style="height: 38px;">
                    <th class="text-center" style="width: 50px;">No</th>
                    <th class="text-start" style="width: 140px;">Modul</th>
                    <th class="text-start" style="width: 140px;">Submodul</th>
                    <th class="text-start" style="width: 220px;">Menu</th>
                    <th class="text-start" style="width: 180px;">Path</th>
                    <th class="text-start" style="width: 140px;">Icon</th>
                    <th class="text-end pe-3" style="width: 110px;">Sequence</th>
                    <th class="text-center" style="width: 110px;">Is_active</th>
                </tr>
                <!-- Sub-header filter row with down-caret/funnel icons -->
                <tr class="sub-header-filter">
                    <th class="p-1"></th>
                    <th class="p-1">
                        <div class="d-flex justify-content-end px-1">
                            <i class="fas fa-caret-down filter-funnel-icon" onclick="promptFilter('modul')"></i>
                        </div>
                    </th>
                    <th class="p-1">
                        <div class="d-flex justify-content-end px-1">
                            <i class="fas fa-caret-down filter-funnel-icon" onclick="promptFilter('submodul')"></i>
                        </div>
                    </th>
                    <th class="p-1">
                        <div class="d-flex justify-content-end px-1">
                            <i class="fas fa-caret-down filter-funnel-icon" onclick="promptFilter('menu')"></i>
                        </div>
                    </th>
                    <th class="p-1">
                        <div class="d-flex justify-content-end px-1">
                            <i class="fas fa-caret-down filter-funnel-icon" onclick="promptFilter('path')"></i>
                        </div>
                    </th>
                    <th class="p-1"></th>
                    <th class="p-1">
                        <div class="d-flex justify-content-end px-1">
                            <i class="fas fa-caret-down filter-funnel-icon" onclick="promptFilter('sequence')"></i>
                        </div>
                    </th>
                    <th class="p-1"></th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Memuat data menu...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================= -->
<!-- 2. FORM VIEW (PERSIS SESUAI SCREENSHOT CREATE NEW) -->
<!-- ========================================================= -->
<div id="formView" class="main-card d-none">
    <!-- Header Form Grey dengan Icon Back -->
    <div class="d-flex align-items-center px-4 py-3" style="background-color: #6c757d; color: #ffffff;">
        <i class="fas fa-arrow-left fs-5 me-3 cursor-pointer" style="cursor: pointer;" title="Kembali" onclick="hideForm()"></i>
        <div>
            <h5 class="mb-0 fw-bold text-white" id="formHeaderTitle" style="font-size: 20px;">Form Menu</h5>
            <div class="text-white small" style="opacity: 0.95; font-size: 13.5px;">Master Menu</div>
        </div>
    </div>

    <!-- Body Form (3 Kolom Grid, Persis Tanpa Label pada Kolom 1-8, Kolom 9 Berlabel 'Status') -->
    <div class="p-4 bg-white">
        <form id="menuForm" onsubmit="event.preventDefault(); submitForm();">
            <input type="hidden" id="formId">
            <input type="hidden" id="formMode" value="create">

            <div class="row g-3 g-md-4">
                <!-- Baris 1: Modul, Sub Modul, Menu (Tanpa Label, hanya placeholder) -->
                <div class="col-md-4">
                    <input type="text" class="form-control form-input-clean" id="inputModul" placeholder="Tuliskan Nama Modul" required>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-input-clean" id="inputSubmodul" placeholder="Tuliskan Nama Sub Modul">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-input-clean" id="inputMenu" placeholder="Tuliskan Nama Menu" required>
                </div>

                <!-- Baris 2: Path, Endpoint, Icon (Tanpa Label, hanya placeholder) -->
                <div class="col-md-4">
                    <input type="text" class="form-control form-input-clean" id="inputPath" placeholder="Tuliskan Nama Path, Ex: /m_menu" required>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-input-clean" id="inputEndpoint" placeholder="Tuliskan Nama Endpoint, Ex: /m_menu">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-input-clean" id="inputIcon" placeholder="Tuliskan Nama Icon, Ex: bookmark">
                </div>

                <!-- Baris 3: Sequence, Keterangan, Status -->
                <div class="col-md-4" style="margin-top: 28px;">
                    <input type="text" class="form-control form-input-clean" id="inputSequence" placeholder="Tuliskan Sequence, Ex: 2.4">
                </div>
                <div class="col-md-4" style="margin-top: 28px;">
                    <input type="text" class="form-control form-input-clean" id="inputDescription" placeholder="Tuliskan Keterangan">
                </div>
                <div class="col-md-4 position-relative" style="margin-top: 28px;">
                    <label for="inputStatus" class="form-label text-dark fw-medium mb-0" 
                        style="position: absolute; top: -18px; left: 12px; font-size: 13px; line-height: 1; color: #495057;">
                        Status
                    </label>
                    <select class="form-select form-input-clean" id="inputStatus">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Footer Action Buttons (Rata Kanan) -->
            <div class="d-flex justify-content-end align-items-center pt-4 mt-2">
                <span class="text-secondary small fst-italic me-3" style="color: #6c757d; font-size: 13px;">Tekan CTRL + S untuk shortcut Save Data</span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-danger btn-sm px-3 py-2 d-flex align-items-center gap-1 fw-medium" id="btnReset" onclick="resetForm()" style="background-color: #dc3545; border-color: #dc3545;">
                        <i class="fas fa-times"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-success btn-sm px-3 py-2 d-flex align-items-center gap-1 fw-medium" id="btnSave" style="background-color: #00875a; border-color: #00875a;">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // -------------------------------------------------------------
    // Initial Master Menu Dataset (15 Data Persis Screenshot)
    // -------------------------------------------------------------
    const defaultData = [
        { id: 1, modul: 'Penilaian', submodul: 'Transaksi', menu: 'Penilaian 360', path: '/t_penilaian360', endpoint: '/t_penilaian360', icon: 'users', sequence: '2.16', is_active: 1, description: '' },
        { id: 2, modul: 'Master', submodul: '', menu: 'Media', path: '/m_media', endpoint: '/m_media', icon: 'box', sequence: '2.24', is_active: 1, description: '' },
        { id: 3, modul: 'Recruitment', submodul: 'Transaksi', menu: 'Pengajuan Reqruitment', path: '/t_req_recruitment', endpoint: '/t_req_recruitment', icon: 'folder-open', sequence: '3', is_active: 1, description: '' },
        { id: 4, modul: 'Laporan', submodul: '', menu: 'Laporan Perjalanan Dinas', path: '/l_perjalanan_dinas', endpoint: '/l_perjalanan_dinas', icon: 'bookmark', sequence: '9100', is_active: 1, description: '' },
        { id: 5, modul: 'Laporan', submodul: '', menu: 'Laporan Klaim Rawat Inap', path: '/l_klaim_ranap', endpoint: '/l_klaim_ranap', icon: 'bookmark', sequence: '9.99', is_active: 1, description: '' },
        { id: 6, modul: 'Transaksi', submodul: '', menu: 'Klaim Rawat Inap', path: '/t_klaim_ranap', endpoint: '/t_klaim_ranap', icon: 'stopwatch', sequence: '3.08', is_active: 1, description: '' },
        { id: 7, modul: 'Laporan', submodul: '', menu: 'Laporan Klaim Akses', path: '/l_klaim_askes', endpoint: '/l_klaim_askes', icon: 'bookmark', sequence: '9.99', is_active: 1, description: '' },
        { id: 8, modul: 'Transaksi', submodul: '', menu: 'Surat Peringatan Lama', path: '/t_sgp', endpoint: '/t_sgp', icon: 'triangle-exclamation', sequence: '3.15', is_active: 0, description: '' },
        { id: 9, modul: 'Master', submodul: '', menu: 'Cost Center', path: '/m_cost_center', endpoint: '/m_cost_center', icon: 'clipboard', sequence: '2.13', is_active: 1, description: '' },
        { id: 10, modul: 'Laporan', submodul: '', menu: 'Export Karyawan', path: '/l_karyawan_export', endpoint: '/l_karyawan_export', icon: 'file invoice dollar', sequence: '4.98', is_active: 1, description: '' },
        { id: 11, modul: 'Laporan', submodul: '', menu: 'Eksport BPJS Kesehatan', path: '/export_bpjs_kes', endpoint: '/export_bpjs_kes', icon: 'file-csv', sequence: '4.1', is_active: 1, description: '' },
        { id: 12, modul: 'Laporan', submodul: '', menu: 'Eksport BPJS Ketenagakerjaan', path: '/export_bpjs_tk', endpoint: '/export_bpjs_tk', icon: 'file-csv', sequence: '4.1', is_active: 1, description: '' },
        { id: 13, modul: 'Master', submodul: '', menu: 'Master Gaji Harian', path: '/m_standart_gaji_harian', endpoint: '/m_standart_gaji_harian', icon: 'money-bill', sequence: '2.22', is_active: 1, description: '' },
        { id: 14, modul: 'Transaksi', submodul: '', menu: 'Lembur Harian', path: '/t_lembur_harian', endpoint: '/t_lembur_harian', icon: 'stopwatch', sequence: '3.08', is_active: 1, description: '' },
        { id: 15, modul: 'Outsourcing', submodul: 'Master', menu: 'Master Gaji Harian OS', path: '/m_standart_gaji_harian_os', endpoint: '/m_standart_gaji_harian_os', icon: 'money-bill', sequence: '3.14', is_active: 1, description: '' }
    ];

    let allData = [...defaultData];
    let selectedRowId = null; // Awalnya NULL: belum ada baris terpilih, tombol aksi tersembunyi
    let currentFilterStatus = null; // null: all, true: active, false: inactive
    let searchQuery = '';
    const API_URL = 'http://localhost:3000/api/m_menu';

    // -------------------------------------------------------------
    // Live Indonesian Clock
    // -------------------------------------------------------------
    function updateClock() {
        const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const now = new Date();
        const day = days[now.getDay()];
        const date = now.getDate();
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        const h = String(now.getHours());
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');

        const clockEl = document.getElementById('liveClock');
        if (clockEl) {
            clockEl.innerText = `${day}, ${date} ${month} ${year} ${h}:${m}:${s}`;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // -------------------------------------------------------------
    // Load Data from Backend API or Fallback
    // -------------------------------------------------------------
    async function loadTableData() {
        // Reset row selection saat data di-load ulang
        selectedRowId = null;
        updateActionButtonsVisibility();

        const tbody = document.getElementById('tableBody');
        try {
            let url = API_URL;
            const params = [];
            if (currentFilterStatus !== null) {
                params.push(`is_active=${currentFilterStatus ? 1 : 0}`);
            }
            if (searchQuery) {
                params.push(`search=${encodeURIComponent(searchQuery)}`);
            }
            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            const res = await fetch(url);
            if (res.ok) {
                const json = await res.json();
                if (json.status === 'success' && Array.isArray(json.data)) {
                    allData = json.data;
                }
            }
        } catch (err) {
            console.warn('Backend API unavailable, using local data fallback.', err);
            let filtered = [...defaultData];
            if (currentFilterStatus !== null) {
                filtered = filtered.filter(item => Boolean(item.is_active) === currentFilterStatus);
            }
            if (searchQuery) {
                const q = searchQuery.toLowerCase();
                filtered = filtered.filter(item => 
                    item.modul.toLowerCase().includes(q) ||
                    item.menu.toLowerCase().includes(q) ||
                    item.path.toLowerCase().includes(q)
                );
            }
            allData = filtered;
        }

        renderTable();
    }

    // -------------------------------------------------------------
    // Render Table Rows
    // -------------------------------------------------------------
    function renderTable() {
        const tbody = document.getElementById('tableBody');
        if (!allData || allData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data ditemukan</td></tr>`;
            return;
        }

        let html = '';
        allData.forEach((row, idx) => {
            const isSelected = (selectedRowId !== null && row.id == selectedRowId);
            const selectedClass = isSelected ? 'row-selected' : '';
            const statusHtml = (row.is_active == 1 || row.is_active === true)
                ? `<span class="status-active">Active</span>`
                : `<span class="status-inactive">Inactive</span>`;

            html += `
                <tr class="${selectedClass}" onclick="selectRow(${row.id})" id="row-${row.id}">
                    <td class="text-center text-muted">${idx + 1}</td>
                    <td class="text-start">${row.modul || ''}</td>
                    <td class="text-start">${row.submodul || ''}</td>
                    <td class="text-start">${row.menu || ''}</td>
                    <td class="text-start">${row.path || ''}</td>
                    <td class="text-start">${row.icon || ''}</td>
                    <td class="text-end pe-3">${row.sequence !== undefined ? row.sequence : ''}</td>
                    <td class="text-center">${statusHtml}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    // -------------------------------------------------------------
    // Row Selection: Hanya munculkan icon aksi jika baris diklik
    // -------------------------------------------------------------
    function selectRow(id) {
        if (selectedRowId === id) {
            // Jika baris yang sama diklik lagi, unselect baris
            selectedRowId = null;
        } else {
            selectedRowId = id;
        }

        // Update styling baris
        document.querySelectorAll('#tableBody tr').forEach(tr => {
            tr.classList.remove('row-selected');
        });

        if (selectedRowId !== null) {
            const currentTr = document.getElementById(`row-${selectedRowId}`);
            if (currentTr) {
                currentTr.classList.add('row-selected');
            }
        }

        // Tampilkan / sembunyikan 4 tombol aksi
        updateActionButtonsVisibility();
    }

    function updateActionButtonsVisibility() {
        const actionsGroup = document.getElementById('rowActionsGroup');
        if (!actionsGroup) return;

        if (selectedRowId !== null) {
            actionsGroup.classList.remove('d-none');
            actionsGroup.classList.add('d-flex');
        } else {
            actionsGroup.classList.add('d-none');
            actionsGroup.classList.remove('d-flex');
        }
    }

    function getSelectedRow() {
        return allData.find(item => item.id == selectedRowId);
    }

    // -------------------------------------------------------------
    // Filter Active / Inactive
    // -------------------------------------------------------------
    function filterStatus(status) {
        const btnActive = document.getElementById('btnFilterActive');
        const btnInactive = document.getElementById('btnFilterInactive');

        if (status === 'active') {
            if (currentFilterStatus === true) {
                currentFilterStatus = null;
                btnActive.classList.remove('selected');
            } else {
                currentFilterStatus = true;
                btnActive.classList.add('selected');
                btnInactive.classList.remove('selected');
            }
        } else if (status === 'inactive') {
            if (currentFilterStatus === false) {
                currentFilterStatus = null;
                btnInactive.classList.remove('selected');
            } else {
                currentFilterStatus = false;
                btnInactive.classList.add('selected');
                btnActive.classList.remove('selected');
            }
        }
        loadTableData();
    }

    // -------------------------------------------------------------
    // Search Box
    // -------------------------------------------------------------
    let searchTimeout = null;
    function onSearchChange(val) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchQuery = val.trim();
            loadTableData();
        }, 300);
    }

    // -------------------------------------------------------------
    // Action Buttons: Delete, Read, Edit, Copy
    // -------------------------------------------------------------
    async function actionDelete() {
        const row = getSelectedRow();
        if (!row) {
            Swal.fire({ icon: 'warning', text: 'Pilih salah satu baris terlebih dahulu' });
            return;
        }

        Swal.fire({
            icon: 'warning',
            text: 'Hapus Data Terpilih?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545',
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    await fetch(`${API_URL}/${row.id}`, { method: 'DELETE' });
                } catch (e) {
                    console.warn('API Delete fallback');
                }
                allData = allData.filter(d => d.id != row.id);
                selectedRowId = null;
                updateActionButtonsVisibility();
                renderTable();
                Swal.fire({ icon: 'success', text: 'Data berhasil dihapus', timer: 1500, showConfirmButton: false });
            }
        });
    }

    function actionRead() {
        const row = getSelectedRow();
        if (!row) return;
        showForm('read', row);
    }

    function actionEdit() {
        const row = getSelectedRow();
        if (!row) return;
        showForm('edit', row);
    }

    function actionCopy() {
        const row = getSelectedRow();
        if (!row) return;
        showForm('copy', row);
    }

    // -------------------------------------------------------------
    // Form Management (Show / Hide / Pre-fill) & History Sync
    // -------------------------------------------------------------
    function showForm(mode, data = {}, pushToHistory = true) {
        document.getElementById('landingView').classList.add('d-none');
        document.getElementById('formView').classList.remove('d-none');

        document.getElementById('formMode').value = mode;
        const isReadonly = (mode === 'read');

        // Set Breadcrumb Title & History State
        const breadcrumbEl = document.getElementById('breadcrumbTitle');
        if (mode === 'create') {
            breadcrumbEl.innerText = 'Menu > Create';
            if (pushToHistory) {
                window.history.pushState({ view: 'form', mode: 'create' }, '', '/master_menu/create');
            }
        } else if (mode === 'edit') {
            breadcrumbEl.innerText = 'Menu > Edit';
            if (pushToHistory) {
                window.history.pushState({ view: 'form', mode: 'edit', data: data }, '', '/master_menu/' + (data.id || '') + '?action=Edit');
            }
        } else if (mode === 'copy') {
            breadcrumbEl.innerText = 'Menu > Copy';
            if (pushToHistory) {
                window.history.pushState({ view: 'form', mode: 'copy', data: data }, '', '/master_menu/create?action=Copy');
            }
        } else if (mode === 'read') {
            breadcrumbEl.innerText = 'Menu > Detail';
            if (pushToHistory) {
                window.history.pushState({ view: 'form', mode: 'read', data: data }, '', '/master_menu/' + (data.id || ''));
            }
        }

        // Set inputs
        document.getElementById('formId').value = (mode === 'copy' || mode === 'create') ? '' : (data.id || '');
        document.getElementById('inputModul').value = data.modul || '';
        document.getElementById('inputSubmodul').value = data.submodul || '';
        document.getElementById('inputMenu').value = data.menu || '';
        document.getElementById('inputPath').value = data.path || '';
        document.getElementById('inputEndpoint').value = data.endpoint || data.path || '';
        document.getElementById('inputIcon').value = data.icon || '';
        document.getElementById('inputSequence').value = data.sequence !== undefined ? data.sequence : '';
        document.getElementById('inputDescription').value = data.description || '';
        document.getElementById('inputStatus').value = (data.is_active == 0) ? '0' : '1';

        // Toggle Readonly state
        const fields = ['inputModul', 'inputSubmodul', 'inputMenu', 'inputPath', 'inputEndpoint', 'inputIcon', 'inputSequence', 'inputDescription', 'inputStatus'];
        fields.forEach(f => {
            const el = document.getElementById(f);
            if (el) el.disabled = isReadonly;
        });

        // Hide Save & Reset in read mode
        document.getElementById('btnSave').style.display = isReadonly ? 'none' : 'inline-flex';
        document.getElementById('btnReset').style.display = isReadonly ? 'none' : 'inline-flex';
    }

    function hideForm(updateHistory = true) {
        document.getElementById('formView').classList.add('d-none');
        document.getElementById('landingView').classList.remove('d-none');
        document.getElementById('breadcrumbTitle').innerText = 'Menu';
        if (updateHistory) {
            window.history.pushState({ view: 'table' }, '', '/master_menu');
        }
        selectedRowId = null;
        updateActionButtonsVisibility();
        renderTable();
    }

    // -------------------------------------------------------------
    // Browser Popstate (Swipe Left / Back Button Navigation)
    // Memastikan jika diswipe ke kiri / back selalu kembali ke Master Menu
    // -------------------------------------------------------------
    window.addEventListener('popstate', function(event) {
        const path = window.location.pathname;
        if (path.endsWith('/create') || (event.state && event.state.view === 'form')) {
            showForm(event.state?.mode || 'create', event.state?.data || {}, false);
        } else {
            // Kembali ke tampilan tabel Master Menu
            hideForm(false);
        }
    });

    // -------------------------------------------------------------
    // Touch Swipe Gesture Listener (Trackpad / Touch Screen)
    // -------------------------------------------------------------
    let touchStartX = 0;
    let touchStartY = 0;

    document.addEventListener('touchstart', function(e) {
        if (e.touches && e.touches[0]) {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
        }
    }, { passive: true });

    document.addEventListener('touchend', function(e) {
        if (e.changedTouches && e.changedTouches[0]) {
            const touchEndX = e.changedTouches[0].clientX;
            const touchEndY = e.changedTouches[0].clientY;
            const diffX = touchEndX - touchStartX;
            const diffY = touchEndY - touchStartY;

            // Jika swipe horizontal signifikan (> 50px) saat form terbuka, kembali ke Master Menu
            if (Math.abs(diffX) > 50 && Math.abs(diffX) > Math.abs(diffY) * 1.3) {
                const formView = document.getElementById('formView');
                if (formView && !formView.classList.contains('d-none')) {
                    hideForm(true);
                }
            }
        }
    }, { passive: true });

    function resetForm() {
        Swal.fire({
            icon: 'warning',
            text: 'Anda yakin akan mereset form ini?',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then(res => {
            if (res.isConfirmed) {
                document.getElementById('inputModul').value = '';
                document.getElementById('inputSubmodul').value = '';
                document.getElementById('inputMenu').value = '';
                document.getElementById('inputPath').value = '';
                document.getElementById('inputEndpoint').value = '';
                document.getElementById('inputIcon').value = '';
                document.getElementById('inputSequence').value = '';
                document.getElementById('inputDescription').value = '';
                document.getElementById('inputStatus').value = '1';
            }
        });
    }

    async function submitForm() {
        const mode = document.getElementById('formMode').value;
        const id = document.getElementById('formId').value;
        const payload = {
            modul: document.getElementById('inputModul').value,
            submodul: document.getElementById('inputSubmodul').value,
            menu: document.getElementById('inputMenu').value,
            path: document.getElementById('inputPath').value,
            endpoint: document.getElementById('inputEndpoint').value,
            icon: document.getElementById('inputIcon').value,
            sequence: document.getElementById('inputSequence').value,
            description: document.getElementById('inputDescription').value,
            is_active: parseInt(document.getElementById('inputStatus').value)
        };

        try {
            if (mode === 'edit' && id) {
                await fetch(`${API_URL}/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const idx = allData.findIndex(d => d.id == id);
                if (idx !== -1) allData[idx] = { ...allData[idx], ...payload };
            } else {
                const res = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const resJson = await res.json();
                const newId = resJson.data?.id || Date.now();
                
                // PENGUBAHAN: Gunakan unshift agar data baru berada di paling atas (Nomor 1)
                allData.unshift({ id: newId, ...payload });
            }
        } catch (e) {
            console.warn('API save fallback');
            if (mode === 'edit' && id) {
                const idx = allData.findIndex(d => d.id == id);
                if (idx !== -1) allData[idx] = { ...allData[idx], ...payload };
            } else {
                const newId = Date.now();
                
                // PENGUBAHAN: Gunakan unshift agar data baru berada di paling atas (Nomor 1)
                allData.unshift({ id: newId, ...payload });
            }
        }

        Swal.fire({
            icon: 'success',
            text: 'Data berhasil disimpan',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            hideForm();
            selectedRowId = null;
            updateActionButtonsVisibility();
            renderTable();
        });
    }

    // -------------------------------------------------------------
    // Export CSV
    // -------------------------------------------------------------
    function exportCSV() {
        let csv = 'No,Modul,Submodul,Menu,Path,Icon,Sequence,Is_active\n';
        allData.forEach((row, i) => {
            const status = (row.is_active == 1) ? 'Active' : 'Inactive';
            csv += `"${i+1}","${row.modul || ''}","${row.submodul || ''}","${row.menu || ''}","${row.path || ''}","${row.icon || ''}","${row.sequence || ''}","${status}"\n`;
        });
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `master_menu_${Date.now()}.csv`;
        a.click();
    }

    // -------------------------------------------------------------
    // Fullscreen Toggle
    // -------------------------------------------------------------
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(() => {});
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }

    // -------------------------------------------------------------
    // Keyboard Shortcuts (Ctrl+F for Search, Ctrl+S for Save)
    // -------------------------------------------------------------
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'f' || e.key === 'F')) {
            e.preventDefault();
            const searchEl = document.getElementById('searchInput');
            if (searchEl) searchEl.focus();
        }
        if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
            const formView = document.getElementById('formView');
            if (formView && !formView.classList.contains('d-none')) {
                e.preventDefault();
                submitForm();
            }
        }
    });

    // Sub-header filter prompt
    function promptFilter(col) {
        Swal.fire({
            title: `Filter ${col}`,
            input: 'text',
            inputPlaceholder: `Ketik filter untuk ${col}...`,
            showCancelButton: true,
            confirmButtonText: 'Terapkan',
            cancelButtonText: 'Reset'
        }).then((result) => {
            if (result.isConfirmed) {
                const val = (result.value || '').toLowerCase();
                const filtered = defaultData.filter(d => String(d[col] || '').toLowerCase().includes(val));
                allData = filtered;
                selectedRowId = null;
                updateActionButtonsVisibility();
                renderTable();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                allData = [...defaultData];
                selectedRowId = null;
                updateActionButtonsVisibility();
                renderTable();
            }
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const path = window.location.pathname;
        if (path.endsWith('/create')) {
            // Setup base history ke /master_menu, lalu buka form create
            window.history.replaceState({ view: 'table' }, '', '/master_menu');
            window.history.pushState({ view: 'form', mode: 'create' }, '', '/master_menu/create');
            showForm('create', {}, false);
            loadTableData(); // Siapkan data tabel di background
        } else {
            window.history.replaceState({ view: 'table' }, '', '/master_menu');
            loadTableData();
        }
    });
</script>
@endpush
