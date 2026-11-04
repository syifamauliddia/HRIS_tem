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
    .filter-btn-active, .filter-btn-inactive {
        border-radius: 4px;
        padding: 3px 14px;
        font-size: 13.5px;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
        cursor: pointer;
        background: #ffffff;
    }
    .filter-btn-active { border: 1px solid #198754; color: #198754; }
    .filter-btn-active.selected, .filter-btn-active:hover { background: #198754; color: #ffffff; }
    
    .filter-btn-inactive { border: 1px solid #dc3545; color: #dc3545; }
    .filter-btn-inactive.selected, .filter-btn-inactive:hover { background: #dc3545; color: #ffffff; }

    .btn-create-new {
        border: 1px solid #0d6efd; color: #0d6efd; background: #ffffff;
        border-radius: 4px; padding: 4px 16px; font-size: 13.5px;
        font-weight: 500; transition: all 0.2s ease; cursor: pointer;
    }
    .btn-create-new:hover { background: #0d6efd; color: #ffffff; }

    /* Toolbar Actions */
    .tool-action-btn, .tool-icon-btn {
        width: 32px; height: 32px; border-radius: 4px;
        display: inline-flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s;
    }
    .tool-action-btn { border: none; color: #ffffff; }
    .tool-action-btn:hover { opacity: 0.88; color: #ffffff; }
    
    .tool-icon-btn { border: 1px solid #dee2e6; background: #ffffff; color: #6c757d; }
    .tool-icon-btn:hover { background: #f8f9fa; color: #212529; }

    /* Table Styling */
    .master-table { border-color: #dee2e6; font-size: 13.5px; margin-bottom: 0; }
    .master-table thead th {
        background-color: #f8f9fa; color: #212529; font-weight: 600;
        border-color: #dee2e6; vertical-align: middle; padding: 8px 10px;
        position: sticky; top: 0; z-index: 2;
    }
    .master-table thead tr.sub-header-filter th {
        background-color: #ffffff; padding: 4px 8px; height: 32px;
        position: sticky; top: 38px; z-index: 2;
    }
    .master-table tbody td { border-color: #dee2e6; padding: 8px 10px; vertical-align: middle; }
    .master-table tbody tr { cursor: pointer; transition: background-color 0.1s ease; }
    .master-table tbody tr:hover { background-color: #f1f5f9; }
    .master-table tbody tr.row-selected, .master-table tbody tr.row-selected td {
        background-color: #cce5ff !important;
    }

    .status-active { color: #198754; font-weight: 500; }
    .status-inactive { color: #6c757d; font-weight: 500; }
    .filter-funnel-icon { color: #94a3b8; font-size: 13px; cursor: pointer; }
    .filter-funnel-icon:hover { color: #475569; }

    /* Form Input Custom Clean */
    .form-input-clean {
        height: 40px; border-radius: 4px; border: 1px solid #dee2e6;
        font-size: 13.5px; color: #333; padding: 8px 14px;
        transition: border-color 0.2s ease;
    }
    .form-input-clean:focus { border-color: #86b7fe; box-shadow: none; }
    .form-input-clean::placeholder { color: #adb5bd; font-size: 13px; }

    /* Toggle Switch Custom */
    .custom-switch-container {
        display: flex; align-items: center; gap: 12px; margin-top: 20px;
        padding: 10px 0;
    }
    .form-switch .form-check-input {
        width: 3em; height: 1.5em; cursor: pointer;
    }

    /* Container Scrollable */
    .scrollable-container { max-height: 65vh; overflow-y: auto; overflow-x: hidden; padding-right: 2px; }
    .scrollable-container::-webkit-scrollbar { width: 6px; }
    .scrollable-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .scrollable-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .scrollable-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<!-- TOP BREADCRUMB & HEADER BAR -->
<div class="d-flex justify-content-between align-items-center top-header-bar">
    <div class="d-flex align-items-center">
        <i class="fas fa-ellipsis-h text-muted me-2" style="font-size: 13px;"></i>
        <span class="fw-medium text-dark" id="breadcrumbTitle" style="font-size: 14.5px;">Menu</span>
    </div>
    <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 13px;">
        <span id="liveClock" class="fw-normal">Sen, 2 Nov 2026 0:45:33</span>
        <i class="far fa-copy cursor-pointer text-muted" title="Copy / Split View"></i>
        <i class="fas fa-expand cursor-pointer text-muted" title="Fullscreen" onclick="toggleFullscreen()"></i>
        <div class="d-flex align-items-center gap-1 cursor-pointer" title="User Profile">
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
                <button type="button" id="btnFilterActive" class="filter-btn-active" onclick="filterStatus('active')">Active</button>
                <button type="button" id="btnFilterInactive" class="filter-btn-inactive" onclick="filterStatus('inactive')">Inactive</button>
            </div>
        </div>
        <div>
            <button type="button" class="btn-create-new" onclick="showForm('create')">Create New</button>
        </div>
    </div>

    <hr class="m-0" style="border-top: 1px solid #e9ecef;">

    <!-- TOOLBAR (SEARCH + ACTIONS) -->
    <div class="d-flex justify-content-between align-items-center px-3 py-2" style="background: #ffffff; min-height: 48px;">
        <div class="d-flex align-items-center gap-2">
            <!-- Search Box -->
            <div class="input-group input-group-sm" style="width: 140px; border-radius: 4px; overflow: hidden;">
                <span class="input-group-text bg-white border-end-0 text-muted pe-1" style="border-color: #dee2e6;">
                    <i class="fas fa-search" style="font-size: 11px;"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-1 text-muted" 
                       placeholder="(Ctrl+F)" style="font-size: 12.5px; height: 32px; border-color: #dee2e6;" 
                       oninput="onSearchChange(this.value)">
            </div>

            <!-- CONTAINER ICON AKSI -->
            <div id="rowActionsGroup" class="d-none align-items-center gap-2">
                <button class="tool-action-btn" style="background-color: #dc3545;" title="Hapus" onclick="actionDelete()"><i class="fas fa-trash-alt" style="font-size: 12.5px;"></i></button>
                <button class="tool-action-btn" style="background-color: #0d9488;" title="Read / Lihat" onclick="actionRead()"><i class="fas fa-eye" style="font-size: 12.5px;"></i></button>
                <button class="tool-action-btn" style="background-color: #0d6efd;" title="Edit" onclick="actionEdit()"><i class="fas fa-pencil-alt" style="font-size: 12.5px;"></i></button>
                <button class="tool-action-btn" style="background-color: #64748b;" title="Copy" onclick="actionCopy()"><i class="fas fa-copy" style="font-size: 12.5px;"></i></button>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button class="tool-icon-btn" title="Export CSV" onclick="exportCSV()"><i class="fas fa-file-csv" style="font-size: 15px;"></i></button>
            <button class="tool-icon-btn" title="Refresh Data" onclick="loadTableData()"><i class="fas fa-sync-alt" style="font-size: 12px;"></i></button>
            <select id="pageSizeSelect" class="form-select form-select-sm" style="width: 65px; height: 32px; font-size: 12.5px; border-color: #dee2e6;">
                <option value="20" selected>20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
    </div>

    <!-- TABLE AREA -->
    <div class="table-responsive scrollable-container" style="overflow-x: auto; min-height: 400px;">
        <table class="table master-table table-bordered mb-0 align-middle" id="mainTable">
            <thead>
                <tr style="height: 38px;">
                    <th class="text-center" style="width: 60px;">No</th>
                    <th class="text-start" style="width: 200px;">KODE</th>
                    <th class="text-start" style="width: 250px;">Nama</th>
                    <th class="text-start" style="width: 250px;">ID Machine</th>
                    <th class="text-center" style="width: 150px;">Status</th>
                </tr>
                <tr class="sub-header-filter">
                    <th class="p-1"></th>
                    <th class="p-1">
                        <div class="d-flex justify-content-start px-1"><i class="fas fa-filter filter-funnel-icon" onclick="promptFilter('kode')"></i></div>
                    </th>
                    <th class="p-1">
                        <div class="d-flex justify-content-start px-1"><i class="fas fa-filter filter-funnel-icon" onclick="promptFilter('nama')"></i></div>
                    </th>
                    <th class="p-1">
                        <div class="d-flex justify-content-start px-1"><i class="fas fa-filter filter-funnel-icon" onclick="promptFilter('id_machine')"></i></div>
                    </th>
                    <th class="p-1">
                        <div class="d-flex justify-content-start px-1"><i class="fas fa-filter filter-funnel-icon" onclick="promptFilter('status')"></i></div>
                    </th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Data render here -->
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================================= -->
<!-- 2. FORM VIEW -->
<!-- ========================================================= -->
<div id="formView" class="main-card d-none">
    <!-- Header Form -->
    <div class="d-flex align-items-center px-4 py-3" style="background-color: #6c757d; color: #ffffff;">
        <i class="fas fa-arrow-left fs-5 me-3 cursor-pointer" title="Kembali" onclick="hideForm()"></i>
        <div>
            <h5 class="mb-0 fw-bold text-white" id="formHeaderTitle" style="font-size: 20px;">Form Fingerprint Machine</h5>
            <div class="text-white small" style="opacity: 0.95; font-size: 13.5px;">Master Fingerprint Machine</div>
        </div>
    </div>

    <!-- Body Form -->
    <div class="p-4 bg-white scrollable-container">
        <form id="machineForm" onsubmit="event.preventDefault(); submitForm();">
            <input type="hidden" id="formId">
            <input type="hidden" id="formMode" value="create">

            <div class="row g-3">
                <!-- Sesuai gambar: Kode (agak abu-abu), ID Machine, Nama -->
                <div class="col-md-4">
                    <input type="text" class="form-control form-input-clean bg-light" id="inputKode" placeholder="Tuliskan Kode" required>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-input-clean" id="inputIdMachine" placeholder="Tuliskan ID MACHINE" required>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-input-clean" id="inputNama" placeholder="Tuliskan Nama" required>
                </div>
            </div>

            <!-- Custom Switch InActive / Active -->
            <div class="custom-switch-container">
                <span class="fst-italic" style="color: #dc3545; font-weight: 500;">InActive</span>
                <div class="form-check form-switch m-0 p-0 d-flex align-items-center">
                    <input class="form-check-input ms-0 mt-0" type="checkbox" id="inputStatus" checked>
                </div>
                <span class="fst-italic" style="color: #198754; font-weight: 500;">Active</span>
            </div>

            <!-- Footer Action Buttons -->
            <div class="d-flex justify-content-end align-items-center pt-5 mt-3 border-top">
                <span class="text-secondary small fst-italic me-3" style="color: #6c757d; font-size: 13px;">Tekan CTRL + S untuk shortcut Save Data</span>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-danger btn-sm px-4 py-2 d-flex align-items-center gap-1 fw-bold text-white" id="btnReset" onclick="resetForm()" style="background-color: #d32f2f; border-color: #d32f2f;">
                        <i class="fas fa-times"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-success btn-sm px-4 py-2 d-flex align-items-center gap-1 fw-bold text-white" id="btnSave" style="background-color: #009688; border-color: #009688;">
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
    // Initial Dataset Dummy
    // -------------------------------------------------------------
    const defaultData = [
        { id: 1, kode: 'FNG-001', nama: 'Fingerprint Pintu Utama', id_machine: 'MCH-1001', is_active: 1 },
        { id: 2, kode: 'FNG-002', nama: 'Fingerprint Ruang IT', id_machine: 'MCH-1002', is_active: 1 },
        { id: 3, kode: 'FNG-003', nama: 'Fingerprint Gudang', id_machine: 'MCH-1003', is_active: 0 },
    ];

    let allData = [...defaultData];
    let selectedRowId = null;
    let currentFilterStatus = null;
    let searchQuery = '';
    const API_URL = 'http://localhost:3000/api/fingerprint';

    // -------------------------------------------------------------
    // Live Clock
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
    // Load Data
    // -------------------------------------------------------------
    async function loadTableData() {
        selectedRowId = null;
        updateActionButtonsVisibility();

        try {
            let filtered = [...allData];
            
            if (currentFilterStatus !== null) {
                filtered = filtered.filter(item => Boolean(item.is_active) === currentFilterStatus);
            }
            if (searchQuery) {
                const q = searchQuery.toLowerCase();
                filtered = filtered.filter(item => 
                    item.kode.toLowerCase().includes(q) ||
                    item.nama.toLowerCase().includes(q) ||
                    item.id_machine.toLowerCase().includes(q)
                );
            }
            renderTable(filtered);
        } catch (err) {
            console.error('Error loading data', err);
        }
    }

    // -------------------------------------------------------------
    // Render Table
    // -------------------------------------------------------------
    function renderTable(data) {
        const tbody = document.getElementById('tableBody');
        if (!data || data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted">No Rows To Show</td></tr>`;
            return;
        }

        let html = '';
        data.forEach((row, idx) => {
            const isSelected = (selectedRowId !== null && row.id == selectedRowId);
            const selectedClass = isSelected ? 'row-selected' : '';
            const statusHtml = (row.is_active == 1 || row.is_active === true)
                ? `<span class="status-active">Active</span>`
                : `<span class="status-inactive">Inactive</span>`;

            html += `
                <tr class="${selectedClass}" onclick="selectRow(${row.id})" id="row-${row.id}">
                    <td class="text-center text-muted">${idx + 1}</td>
                    <td class="text-start fw-medium">${row.kode || ''}</td>
                    <td class="text-start">${row.nama || ''}</td>
                    <td class="text-start">${row.id_machine || ''}</td>
                    <td class="text-center">${statusHtml}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    // -------------------------------------------------------------
    // Row Selection
    // -------------------------------------------------------------
    function selectRow(id) {
        selectedRowId = (selectedRowId === id) ? null : id;
        
        document.querySelectorAll('#tableBody tr').forEach(tr => tr.classList.remove('row-selected'));
        if (selectedRowId !== null) {
            const currentTr = document.getElementById(`row-${selectedRowId}`);
            if (currentTr) currentTr.classList.add('row-selected');
        }
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
    // Action Buttons
    // -------------------------------------------------------------
    function actionDelete() {
        const row = getSelectedRow();
        if (!row) return;

        Swal.fire({
            icon: 'warning', text: 'Hapus Mesin Fingerprint ini?',
            showCancelButton: true, confirmButtonText: 'Yes', confirmButtonColor: '#dc3545',
        }).then((result) => {
            if (result.isConfirmed) {
                allData = allData.filter(d => d.id != row.id);
                selectedRowId = null;
                updateActionButtonsVisibility();
                loadTableData();
                Swal.fire({ icon: 'success', text: 'Data dihapus', timer: 1500, showConfirmButton: false });
            }
        });
    }

    function actionRead() {
        const row = getSelectedRow();
        if (row) showForm('read', row);
    }

    function actionEdit() {
        const row = getSelectedRow();
        if (row) showForm('edit', row);
    }

    function actionCopy() {
        const row = getSelectedRow();
        if (row) showForm('copy', row);
    }

    // -------------------------------------------------------------
    // Form Management & History Sync
    // -------------------------------------------------------------
    function showForm(mode, data = {}, pushToHistory = true) {
        document.getElementById('landingView').classList.add('d-none');
        document.getElementById('formView').classList.remove('d-none');

        document.getElementById('formMode').value = mode;
        const isReadonly = (mode === 'read');

        const breadcrumbEl = document.getElementById('breadcrumbTitle');
        if (mode === 'create') {
            breadcrumbEl.innerText = 'Menu > Create';
            if (pushToHistory) {
                window.history.pushState({ view: 'form', mode: 'create' }, '', '/master_fingerprint/create');
            }
        } else if (mode === 'edit') {
            breadcrumbEl.innerText = 'Menu > Edit';
            if (pushToHistory) {
                window.history.pushState({ view: 'form', mode: 'edit', data: data }, '', '/master_fingerprint/' + (data.id || '') + '?action=Edit');
            }
        } else if (mode === 'copy') {
            breadcrumbEl.innerText = 'Menu > Copy';
            if (pushToHistory) {
                window.history.pushState({ view: 'form', mode: 'copy', data: data }, '', '/master_fingerprint/create?action=Copy');
            }
        } else if (mode === 'read') {
            breadcrumbEl.innerText = 'Menu > Detail';
            if (pushToHistory) {
                window.history.pushState({ view: 'form', mode: 'read', data: data }, '', '/master_fingerprint/' + (data.id || ''));
            }
        }

        document.getElementById('formId').value = (mode === 'copy' || mode === 'create') ? '' : (data.id || '');
        document.getElementById('inputKode').value = data.kode || '';
        document.getElementById('inputIdMachine').value = data.id_machine || '';
        document.getElementById('inputNama').value = data.nama || '';
        document.getElementById('inputStatus').checked = (data.is_active !== undefined) ? (data.is_active == 1) : true;

        const fields = ['inputKode', 'inputIdMachine', 'inputNama', 'inputStatus'];
        fields.forEach(f => {
            const el = document.getElementById(f);
            if (el) el.disabled = isReadonly;
        });

        document.getElementById('btnSave').style.display = isReadonly ? 'none' : 'inline-flex';
        document.getElementById('btnReset').style.display = isReadonly ? 'none' : 'inline-flex';
    }

    function hideForm(updateHistory = true) {
        document.getElementById('formView').classList.add('d-none');
        document.getElementById('landingView').classList.remove('d-none');
        document.getElementById('breadcrumbTitle').innerText = 'Menu';
        
        if (updateHistory) {
            window.history.pushState({ view: 'table' }, '', '/master_fingerprint');
        }
        
        selectedRowId = null;
        updateActionButtonsVisibility();
        loadTableData();
    }

    // -------------------------------------------------------------
    // Browser Popstate (Tombol Back / Swipe History)
    // -------------------------------------------------------------
    window.addEventListener('popstate', function(event) {
        const path = window.location.pathname;
        if (path.includes('/create') || (event.state && event.state.view === 'form')) {
            showForm(event.state?.mode || 'create', event.state?.data || {}, false);
        } else {
            hideForm(false);
        }
    });

    // -------------------------------------------------------------
    // Touch Swipe Gesture Listener (Aman & Terkunci di Fingerprint)
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

            if (Math.abs(diffX) > 50 && Math.abs(diffX) > Math.abs(diffY) * 1.3) {
                const formView = document.getElementById('formView');
                if (formView && !formView.classList.contains('d-none')) {
                    const currentPath = window.location.pathname;
                    if (currentPath.includes('fingerprint')) {
                        hideForm(true);
                    }
                }
            }
        }
    }, { passive: true });

    function resetForm() {
        Swal.fire({
            icon: 'warning', text: 'Reset isi form ini?',
            showCancelButton: true, confirmButtonText: 'Yes'
        }).then(res => {
            if (res.isConfirmed) {
                document.getElementById('inputKode').value = '';
                document.getElementById('inputIdMachine').value = '';
                document.getElementById('inputNama').value = '';
                document.getElementById('inputStatus').checked = true;
            }
        });
    }

    function submitForm() {
        const mode = document.getElementById('formMode').value;
        const id = document.getElementById('formId').value;
        const payload = {
            kode: document.getElementById('inputKode').value,
            id_machine: document.getElementById('inputIdMachine').value,
            nama: document.getElementById('inputNama').value,
            is_active: document.getElementById('inputStatus').checked ? 1 : 0
        };

        if (mode === 'edit' && id) {
            const idx = allData.findIndex(d => d.id == id);
            if (idx !== -1) allData[idx] = { ...allData[idx], ...payload };
        } else {
            const newId = Date.now();
            allData.unshift({ id: newId, ...payload });
        }

        Swal.fire({
            icon: 'success', text: 'Data berhasil disimpan', timer: 1500, showConfirmButton: false
        }).then(() => {
            hideForm();
        });
    }

    // -------------------------------------------------------------
    // Keyboard Shortcuts
    // -------------------------------------------------------------
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'f' || e.key === 'F')) {
            e.preventDefault();
            const searchEl = document.getElementById('searchInput');
            if (searchEl && !document.getElementById('landingView').classList.contains('d-none')) {
                searchEl.focus();
            }
        }
        if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
            const formView = document.getElementById('formView');
            if (formView && !formView.classList.contains('d-none')) {
                e.preventDefault();
                submitForm();
            }
        }
    });

    // -------------------------------------------------------------
    // Export & Filter Prompt
    // -------------------------------------------------------------
    function exportCSV() {
        let csv = 'No,KODE,Nama,ID Machine,Status\n';
        allData.forEach((row, i) => {
            const status = (row.is_active == 1) ? 'Active' : 'Inactive';
            csv += `"${i+1}","${row.kode || ''}","${row.nama || ''}","${row.id_machine || ''}","${status}"\n`;
        });
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url; a.download = `master_fingerprint_${Date.now()}.csv`; a.click();
    }

    function promptFilter(col) {
        Swal.fire({
            title: `Filter ${col}`, input: 'text', inputPlaceholder: `Ketik filter untuk ${col}...`,
            showCancelButton: true, confirmButtonText: 'Terapkan', cancelButtonText: 'Reset'
        }).then((result) => {
            if (result.isConfirmed) {
                const val = (result.value || '').toLowerCase();
                const filtered = defaultData.filter(d => String(d[col] || '').toLowerCase().includes(val));
                allData = filtered;
                loadTableData();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                allData = [...defaultData];
                loadTableData();
            }
        });
    }

    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(() => {});
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const path = window.location.pathname;
        if (path.includes('/create')) {
            window.history.replaceState({ view: 'table' }, '', '/master_fingerprint');
            window.history.pushState({ view: 'form', mode: 'create' }, '', path);
            showForm('create', {}, false);
            loadTableData();
        } else {
            window.history.replaceState({ view: 'table' }, '', '/master_fingerprint');
            loadTableData();
        }
    });
</script>
@endpush