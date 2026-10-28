@extends('layouts.app')

@push('styles')
<style>
    .accordion-button:not(.collapsed) {
        background-color: #343a40; /* Warna gelap */
        color: white;
        box-shadow: none;
    }
    .accordion-button {
        background-color: #495057;
        color: white;
    }
    .accordion-button::after {
        filter: brightness(0) invert(1);
    }
    .table-hover tbody tr {
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        
        <!-- Toolbar Atas (Search, Refresh, Select) -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Cari" onkeyup="if(event.key === 'Enter') loadTable()">
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" title="Segarkan" onclick="loadTable()">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <select id="paginateSelect" class="form-select" style="width: 100px;" onchange="loadTable()">
                    <option value="50">50</option>
                    <option value="100" selected>100</option>
                    <option value="200">200</option>
                    <option value="500">500</option>
                </select>
            </div>
        </div>

        <!-- Area List Accordion -->
        <div id="loadingState" class="text-center py-4 text-muted fst-italic d-none">
            <div class="spinner-border spinner-border-sm me-2"></div> Memuat data...
        </div>

        <div class="accordion" id="accordionNotification">
            <!-- Data akan di-render di sini oleh Javascript -->
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadTable();
    });

    async function loadTable() {
        const accordionContainer = document.getElementById('accordionNotification');
        const loading = document.getElementById('loadingState');
        const searchVal = document.getElementById('searchInput').value;
        const limitVal = document.getElementById('paginateSelect').value;

        // Ambil data respo dari localStorage
        const respoData = localStorage.getItem('respo');
        let m_branch_id = '';
        let m_subcomp_id = '';
        
        if (respoData) {
            const respoValues = JSON.parse(respoData);
            m_branch_id = respoValues.m_branch_id || '';
            m_subcomp_id = respoValues.m_subcomp_id || '';
        }

        try {
            loading.classList.remove('d-none');
            accordionContainer.innerHTML = '';

            // Setup Query Params
            const params = new URLSearchParams({
                search: searchVal,
                limit: limitVal,
                m_branch_id: m_branch_id,
                m_subcomp_id: m_subcomp_id
            });

            // Hit API
            const response = await fetch(`http://localhost:3000/api/notification/outstanding?${params.toString()}`);
            const result = await response.json();
            const data = result.data;

            loading.classList.add('d-none');

            if (data.length === 0) {
                accordionContainer.innerHTML = `<div class="alert alert-info text-center">Data approval tidak ditemukan</div>`;
                return;
            }

            // Grouping data berdasarkan trx_name (Sama seperti Vue reduce)
            const groupedData = data.reduce((acc, item) => {
                const key = item.trx_name;
                if (!acc[key]) acc[key] = [];
                acc[key].push(item);
                return acc;
            }, {});

            // Render Accordion
            let counter = 0;
            for (const [groupName, items] of Object.entries(groupedData)) {
                const isOpen = counter === 0 ? 'show' : ''; // Buka otomatis akordion pertama
                const isCollapsed = counter === 0 ? '' : 'collapsed';
                const headingId = `heading${counter}`;
                const collapseId = `collapse${counter}`;

                let rowsHtml = '';
                items.forEach(d => {
                    // Logic Redirect URL saat baris diklik
                    const url = `/${d.form_name || d.trx_table}/${d.id}?is_approval=true`;
                    
                    rowsHtml += `
                        <tr onclick="window.location.href='${url}'">
                            <td class="text-primary fw-bold">${d.nomor}</td>
                            <td>${d.trx_nomor}</td>
                            <td>${d.creator}</td>
                            <td>${d.trx_date}</td>
                            <td>${d.created_at}</td>
                            <td>${d.status}</td>
                        </tr>
                    `;
                });

                const accordionHtml = `
                    <div class="accordion-item mb-3 border">
                        <h2 class="accordion-header" id="${headingId}">
                            <button class="accordion-button fw-bold ${isCollapsed}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}">
                                ${groupName}
                            </button>
                        </h2>
                        <div id="${collapseId}" class="accordion-collapse collapse ${isOpen}" data-bs-parent="#accordionNotification">
                            <div class="accordion-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0 text-sm align-middle text-nowrap">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nomor Approval</th>
                                                <th>Nomor Transaksi</th>
                                                <th>Pemohon</th>
                                                <th>Tgl Transaksi</th>
                                                <th>Tgl Pengajuan Approval</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${rowsHtml}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                accordionContainer.innerHTML += accordionHtml;
                counter++;
            }

        } catch (error) {
            loading.classList.add('d-none');
            accordionContainer.innerHTML = `<div class="alert alert-danger text-center">Gagal memuat data dari server</div>`;
        }
    }
</script>
@endpush