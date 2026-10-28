@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom pt-3 pb-2">
        <h5 class="mb-0 text-secondary"><i class="fas fa-arrow-left me-2"></i> Form Penilaian</h5>
        <small class="text-muted ms-4">Master Penilaian</small>
    </div>
    <div class="card-body bg-light p-4">
        <form id="formMasterPenilaian" class="bg-white p-4 border rounded shadow-sm">
            <!-- Dropdown Atas -->
            <div class="row mb-3">
                <div class="col-md-4"><select class="form-select text-muted" id="sbu_id"><option value="1">HOLDING</option></select></div>
                <div class="col-md-4"><select class="form-select text-muted" id="sub_id"><option value="1">HOLDING</option></select></div>
                <div class="col-md-4"><select class="form-select text-muted" id="branch_id"><option value="1">HOLDING</option></select></div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><select class="form-select text-muted" id="level_id"><option value="1">Direktur Holding</option></select></div>
                <div class="col-md-4"><select class="form-select text-muted" id="division_id"><option value="1">Information Technology</option></select></div>
                <div class="col-md-4"><select class="form-select text-success border-success" id="tipe_penilaian"><option value="Kompetensi SoftSkill">Kompetensi SoftSkill</option></select></div>
            </div>
            <div class="row mb-4 align-items-center">
                <div class="col-md-8"><input type="text" class="form-control border-success" id="deskripsi" placeholder="Tuliskan deskripsi" value="Penilaian Direktur"></div>
                <div class="col-md-4 d-flex">
                    <span class="text-danger me-2 font-italic">InActive</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status" checked>
                    </div>
                    <span class="text-success ms-2">Active</span>
                </div>
            </div>

            <!-- Tombol Tambah Assessment -->
            <div class="text-end mb-3">
                <button type="button" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-1"></i> Tambah Assessment</button>
            </div>

            <!-- Tabel Form Kosong -->
            <table class="table table-borderless align-middle mb-5">
                <thead>
                    <tr class="text-muted border-bottom">
                        <th>NO.</th>
                        <th>NAMA INDICATOR <span class="text-danger">*</span></th>
                        <th>KATEGORI <span class="text-danger">*</span></th>
                        <th>BOBOT <span class="text-danger">*</span></th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">1.</td>
                        <td><input type="text" class="form-control" placeholder="Masukkan Nama Assessment" id="nama_indicator"></td>
                        <td><select class="form-select text-muted"><option>Pilih Kategori Penilai</option></select></td>
                        <td><input type="number" class="form-control" value="0" id="bobot"></td>
                        <td><button type="button" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></td>
                    </tr>
                </tbody>
            </table>

            <!-- Tombol Simpan -->
            <div class="text-end mt-5 border-top pt-3">
                <small class="text-muted me-3 fst-italic">Tekan CTRL + S untuk shortcut Save Data</small>
                <button type="button" class="btn btn-danger text-white fw-bold px-4 me-2"><i class="fas fa-times me-1"></i> Reset</button>
                <button type="submit" class="btn btn-success text-white fw-bold px-4"><i class="fas fa-save me-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@stack('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('formMasterPenilaian').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Susun data untuk dikirim ke Backend Node.js
            const payload = {
                sbu_id: document.getElementById('sbu_id').value,
                sub_id: document.getElementById('sub_id').value,
                branch_id: document.getElementById('branch_id').value,
                level_id: document.getElementById('level_id').value,
                division_id: document.getElementById('division_id').value,
                tipe_penilaian: document.getElementById('tipe_penilaian').value,
                deskripsi: document.getElementById('deskripsi').value,
                status: document.getElementById('status').checked ? 'Active' : 'InActive',
                indicators: [
                    {
                        nama_indicator: document.getElementById('nama_indicator').value || 'Indicator Default',
                        kategori: 'Default Kategori',
                        bobot: document.getElementById('bobot').value,
                        details: [] // Kosongkan dulu untuk tahap ini
                    }
                ]
            };

            try {
                // Tembak API Backend
                const response = await fetch('http://localhost:3000/api/master-assessments', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                const result = await response.json();
                if(result.status === 'success') {
                    alert('Data Penilaian Berhasil Disimpan ke Database!');
                } else {
                    alert('Gagal menyimpan data.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Tidak bisa terhubung ke backend. Pastikan server.js berjalan.');
            }
        });
    });
</script>
