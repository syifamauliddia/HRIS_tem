@extends('layouts.app')

@push('styles')
    <!-- CSS Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        /* Select2 mengikuti tampilan Bootstrap */
        .select2-container { width: 100% !important; }
        .select2-container--default .select2-selection--single {
            height: 38px; border: 1px solid #dee2e6; border-radius: 6px; display: flex; align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 12px; padding-right: 30px; color: #495057; line-height: normal;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px; right: 6px;
        }
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #86b7fe;
        }
        .select2-dropdown {
            border: 1px solid #dee2e6; border-radius: 6px;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0d6efd;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #dee2e6; border-radius: 4px; padding: 6px 10px; outline: none;
        }
    </style>
@endpush

@section('content')
<div class="card shadow-sm border-0 rounded-md">
    <!-- Header Page -->
    <div class="bg-gray-500 text-white rounded-t-md py-3 px-4 d-flex align-items-center" style="background-color: #6c757d;">
        <a href="/master-penilaian" class="text-white me-3 text-decoration-none"><i class="fas fa-arrow-left fa-lg"></i></a>
        <div>
            <h5 class="mb-0 fw-bold">Form Penilaian</h5>
            <small class="text-light">Master Penilaian</small>
        </div>
    </div>

    <div class="card-body bg-white p-4">
        <form id="formMasterPenilaian">
            <!-- Dropdown Atas (Row 1) -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <select class="form-select text-dark bg-white select2" id="sbu_id" style="background-color: #ffffff !important;">
                        <option value="" selected disabled>Pilih SBU</option>
                        <option value="HOLDING">HOLDING</option>
                        <option value="XPDC">XPDC</option>
                        <option value="TRADING">TRADING</option>
                        <option value="PACKAGING">PACKAGING</option>
                        <option value="PUBLISHING">PUBLISHING</option>
                        <option value="PERKOM">PERKOM</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="sub_id" disabled style="pointer-events: none; appearance: none; -webkit-appearance: none; -moz-appearance: none; background: #f8f9fa !important; color: #6c757d !important;">
                        <option value="1">Pilih Sub SBU</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="branch_id" disabled style="pointer-events: none; appearance: none; -webkit-appearance: none; -moz-appearance: none; background: #f8f9fa !important; color: #6c757d !important;">
                        <option value="1">Pilih Cabang</option>
                    </select>
                </div>
            </div>

            <!-- Dropdown Atas (Row 2) -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <select class="form-select text-dark bg-white select2" id="sbu_id" style="background-color: #ffffff !important;">
                        <option value="" selected disabled>Pilih Level</option>
                        <option value="Direktur Holding">Direktur Holding</option>
                        <option value="GM/Wadir Holding">GM/Wadir Holding</option>
                        <option value="Asmen/Manager/OM">Asmen/Manager/OM</option>
                        <option value="Kadiv/Wakadiv">Kadiv/Wakadiv</option>
                        <option value="Karu/Wakaru">Karu/Wakaru</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="division_id" disabled style="pointer-events: none; appearance: none; -webkit-appearance: none; -moz-appearance: none; background: #f8f9fa !important; color: #6c757d !important;">
                        <option value="1">Information Technology</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select class="form-select text-dark bg-white select2" id="sbu_id" style="background-color: #ffffff !important;">
                        <option value="" selected disabled>Pilih Tipe Penilaian</option>
                        <option value="Performance KPI">Performance KPI</option>
                        <option value="Performance ReviewPI">Performance Review</option>
                        <option value="Performance HardSkill">Performance HardSkill</option>
                        <option value="Performance SoftSkill">Performance SoftSkill</option>
                    </select>
                </div>
            </div>

            <!-- Deskripsi & Status Switch (Row 3) -->
            <div class="row mb-4 align-items-center">
                <div class="col-md-8">
                    <input type="text" class="form-control border-success" id="deskripsi" placeholder="Tuliskan deskripsi">
                </div>
                <div class="col-md-4 d-flex align-items-center">
                    <span class="text-danger me-2 fst-italic">InActive</span>
                    <div class="form-check form-switch fs-5">
                        <input class="form-check-input" type="checkbox" id="status" checked>
                    </div>
                    <span class="text-success ms-2 fw-medium">Active</span>
                </div>
            </div>

            <!-- Tombol Tambah Assessment -->
            <div class="text-end mb-4">
                <button type="button" class="btn btn-primary shadow-sm px-3 py-2" id="btnTambahAssessment">
                    <i class="fas fa-plus me-1"></i> Tambah Assessment
                </button>
            </div>

            <!-- CONTAINER DYNAMIC ASSESSMENT & SUB-DETAIL -->
            <div id="assessmentContainer">
                <!-- Assessment Item akan dirender di sini via JS -->
            </div>

            <!-- Tombol Simpan -->
            <div class="d-flex justify-content-end align-items-center mt-4 pt-3 border-top">
                <small class="text-muted me-3 fst-italic">Tekan CTRL + S untuk shortcut Save Data</small>
                <button type="reset" class="btn btn-danger text-white fw-bold px-4 me-2"><i class="fas fa-times me-1"></i> Reset</button>
                <button type="submit" class="btn btn-success text-white fw-bold px-4"><i class="fas fa-save me-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery & Select2 -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Inisialisasi Select2 untuk Dropdown Statis
        $('.searchable-select').each(function () {
            $(this).select2({
                placeholder: $(this).data('placeholder'),
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 0
            });
        });

        // 2. Fungsi Membuat Templat Baris Sub-Detail
        function createSubDetailRow(subNumber = 1) {
            return `
                <tr class="sub-detail-row">
                    <td class="fw-bold ps-3 sub-no">${subNumber}.</td>
                    <td><input type="text" class="form-control sub-keterangan" placeholder="Masukkan Keterangan"></td>
                    <td><input type="number" class="form-control text-end sub-nilai" value="0"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-delete-sub-detail"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
        }

        // 3. Fungsi Membuat Templat Blok Assessment (Indikator + Sub-Detail)
        function createAssessmentBlock(indicatorNumber = 1) {
            return `
                <div class="card border mb-4 shadow-sm assessment-item">
                    <div class="card-body p-0">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="bg-light text-muted border-bottom">
                                <tr>
                                    <th style="width: 60px;" class="ps-3">NO.</th>
                                    <th>NAMA INDICATOR <span class="text-danger">*</span></th>
                                    <th style="width: 280px;">KATEGORI <span class="text-danger">*</span></th>
                                    <th style="width: 180px;">BOBOT <span class="text-danger">*</span></th>
                                    <th style="width: 80px;" class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="fw-bold ps-3 indicator-no">${indicatorNumber}.</td>
                                    <td><input type="text" class="form-control nama-indicator" placeholder="Masukkan Nama Assessment"></td>
                                    <td>
                                        <select class="form-select text-muted">
                                            <option selected disabled>Pilih Kategori Penilai</option>
                                            <option value="SoftSkill">SoftSkill</option>
                                            <option value="HardSkill">HardSkill</option>
                                            <option value="Core Value">Core Value</option>
                                        </select>
                                    </td>
                                    <td><input type="number" class="form-control text-end bobot-input" value="0"></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm btn-delete-assessment"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Bagian Sub-Detail -->
                        <div class="p-3 bg-light border-top">
                            <h6 class="text-secondary fw-bold mb-2">Sub-Detail</h6>
                            <table class="table table-borderless align-middle mb-2 bg-white rounded border">
                                <thead class="table-light text-muted border-bottom">
                                    <tr>
                                        <th style="width: 60px;" class="ps-3">NO.</th>
                                        <th>KETERANGAN <span class="text-danger">*</span></th>
                                        <th style="width: 250px;">NILAI <span class="text-danger">*</span></th>
                                        <th style="width: 80px;" class="text-center">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody class="sub-detail-container">
                                    ${createSubDetailRow(1)}
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-link text-primary text-decoration-none fw-semibold p-0 mt-1 btn-add-sub-detail">
                                <i class="fas fa-plus me-1"></i> Tambah Detail Penilaian
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        // 4. Fungsi Update Penomoran Indikator & Sub-Detail
        function updateNumbers() {
            $('.assessment-item').each(function(index) {
                const indicatorIndex = index + 1;
                $(this).find('.indicator-no').text(indicatorIndex + '.');
                
                $(this).find('.sub-detail-row').each(function(subIndex) {
                    $(this).find('.sub-no').text((subIndex + 1) + '.');
                });
            });
        }

        // 5. Fungsi Menambah Assessment Baru
        function addAssessment() {
            const currentCount = $('.assessment-item').length + 1;
            const newBlockHtml = createAssessmentBlock(currentCount);
            const $newBlock = $(newBlockHtml);
            
            $('#assessmentContainer').append($newBlock);

            // Inisialisasi Select2 pada dropdown Kategori yang baru dibuat
            $newBlock.find('.kategori-select').select2({
                placeholder: "Pilih Kategori Penilai",
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 0
            });

            updateNumbers();
        }

        // Tambah Assessment awal saat pertama dimuat
        addAssessment();

        // Event Klik: Tombol "+ Tambah Assessment"
        $('#btnTambahAssessment').on('click', function() {
            addAssessment();
        });

        // Event Klik: Tombol "+ Tambah Detail Penilaian" (Hanya Tambah Sub-Detail)
        $(document).on('click', '.btn-add-sub-detail', function() {
            const $subContainer = $(this).closest('.assessment-item').find('.sub-detail-container');
            const newSubNo = $subContainer.find('.sub-detail-row').length + 1;
            $subContainer.append(createSubDetailRow(newSubNo));
            updateNumbers();
        });

        // Event Klik: Hapus Assessment Block
        $(document).on('click', '.btn-delete-assessment', function() {
            if ($('.assessment-item').length > 1) {
                $(this).closest('.assessment-item').remove();
                updateNumbers();
            } else {
                alert('Minimal harus ada 1 Assessment!');
            }
        });

        // Event Klik: Hapus Sub-Detail Row
        $(document).on('click', '.btn-delete-sub-detail', function() {
            const $subContainer = $(this).closest('.sub-detail-container');
            if ($subContainer.find('.sub-detail-row').length > 1) {
                $(this).closest('.sub-detail-row').remove();
                updateNumbers();
            } else {
                alert('Minimal harus ada 1 Sub-Detail!');
            }
        });

        // 6. Form Submit Handler (Dynamic Payload Mapping)
        document.getElementById('formMasterPenilaian').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Susun data dinamis dari setiap assessment item dan sub-detailnya
            const indicatorsPayload = [];

            $('.assessment-item').each(function() {
                const $item = $(this);
                const detailsPayload = [];

                $item.find('.sub-detail-row').each(function() {
                    detailsPayload.push({
                        keterangan: $(this).find('.sub-keterangan').val() || '',
                        nilai: $(this).find('.sub-nilai').val() || 0
                    });
                });

                indicatorsPayload.push({
                    nama_indicator: $item.find('.nama-indicator').val() || '',
                    kategori: $item.find('.kategori-select').val() || '',
                    bobot: $item.find('.bobot-input').val() || 0,
                    details: detailsPayload
                });
            });

            const payload = {
                sbu_id: document.getElementById('sbu_id').value,
                sub_id: document.getElementById('sub_id').value,
                branch_id: document.getElementById('branch_id').value,
                level_id: document.getElementById('level_id').value,
                division_id: document.getElementById('division_id').value,
                tipe_penilaian: document.getElementById('tipe_penilaian').value,
                deskripsi: document.getElementById('deskripsi').value,
                status: document.getElementById('status').checked ? 'Active' : 'InActive',
                indicators: indicatorsPayload
            };

            try {
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

        // 7. Shortcut CTRL + S
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
                e.preventDefault();
                document.getElementById('formMasterPenilaian').requestSubmit();
            }
        });

    });
</script>
@endpush