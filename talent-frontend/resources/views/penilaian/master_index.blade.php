@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-0">
    <!-- Header Page -->
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="text-muted"><i class="fas fa-ellipsis-h me-2"></i> Master</span>
        <button class="btn btn-outline-primary btn-sm rounded-circle"><i class="fas fa-user"></i></button>
    </div>

    <div class="card-body p-4 bg-light">
        <!-- Filter Status & Tombol Create New -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <span class="fw-bold text-secondary me-2">Filter Status :</span>
                <button class="btn btn-outline-success btn-sm active px-3">Aktif</button>
                <button class="btn btn-outline-danger btn-sm px-3">Inaktif</button>
            </div>
            <a href="/master-penilaian/create" class="btn btn-outline-primary fw-bold shadow-sm">+ Create New</a>
        </div>

        <!-- Card Tabel Data -->
        <div class="bg-white p-3 border rounded shadow-sm">
            <!-- Search Bar & Controls -->
            <div class="d-flex justify-content-between mb-3">
                <div class="input-group style-search" style="max-width: 250px;">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" placeholder="(Ctrl+F)">
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button class="btn btn-light btn-sm border"><i class="fas fa-file-csv"></i></button>
                    <button class="btn btn-light btn-sm border" onclick="loadMasterData()"><i class="fas fa-sync-alt"></i></button>
                    <select class="form-select form-select-sm" style="width: 70px;">
                        <option>20</option>
                    </select>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-nowrap" id="tableMaster">
                    <thead class="table-light text-secondary">
                        <tr class="text-center">
                            <th>No</th>
                            <th>SBU</th>
                            <th>SUB</th>
                            <th>BRANCH</th>
                            <th>LEVEL</th>
                            <th>DIVISI</th>
                            <th>Tipe Penilaian</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                        </tr>
                        <!-- Filter Inputs Sub-Header -->
                        <tr>
                            <th></th>
                            <th><input type="text" class="form-control form-control-sm"></th>
                            <th><input type="text" class="form-control form-control-sm"></th>
                            <th><input type="text" class="form-control form-control-sm"></th>
                            <th><input type="text" class="form-control form-control-sm"></th>
                            <th><input type="text" class="form-control form-control-sm"></th>
                            <th><input type="text" class="form-control form-control-sm"></th>
                            <th><input type="text" class="form-control form-control-sm"></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="masterTableBody">
                        <tr>
                            <td colspan="9" class="text-center text-muted">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadMasterData();
    });

    // Mengambil data dari Backend Node.js
    async function loadMasterData() {
        const tbody = document.getElementById('masterTableBody');
        try {
            const response = await fetch('http://localhost:3000/api/master-assessments');
            const result = await response.json();
            
            if (result.status === 'success' && result.data.length > 0) {
                tbody.innerHTML = '';
                result.data.forEach((item, index) => {
                    tbody.innerHTML += `
                        <tr>
                            <td class="text-center">${index + 1}</td>
                            <td>${item.sbu || '-'}</td>
                            <td>${item.sub || '-'}</td>
                            <td>${item.branch || '-'}</td>
                            <td>${item.level || '-'}</td>
                            <td>${item.divisi || '-'}</td>
                            <td>${item.tipe_penilaian || '-'}</td>
                            <td>${item.deskripsi || '-'}</td>
                            <td class="text-center fw-bold text-success">${item.status}</td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">Belum ada data tersedia</td></tr>`;
            }
        } catch (error) {
            console.error('Error fetching data:', error);
            tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Gagal terhubung ke server backend</td></tr>`;
        }
    }
</script>
@endpush