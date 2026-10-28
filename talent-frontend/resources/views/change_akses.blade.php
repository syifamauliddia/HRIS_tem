@extends('layouts.app')

@section('content')
<!-- Memasukkan CSS langsung di dalam section agar dipastikan selalu ter-load -->
<style>
    .header-respo {
        background-color: #6b7280; /* Abu-abu gelap */
        color: white;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
    }
    .card-respo {
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: #ffffff; /* Kartu tidak aktif warna putih */
        height: 100%;
        color: #374151;
    }
    .card-respo:hover {
        background-color: #f3f4f6;
    }
    
    /* Styling khusus kartu yang Sedang Aktif */
    .card-respo.active {
        background-color: #10b981 !important; /* Hijau terang sesuai referensi */
        border-color: #10b981;
        color: #111827 !important; /* Teks gelap */
    }
    .card-respo.active:hover {
        background-color: #059669 !important;
    }
    .card-respo.active h5 {
        color: #111827 !important; 
    }

    .text-respo {
        font-size: 14px;
        margin-bottom: 2px;
    }
</style>

<div class="card border-0 shadow-sm" style="border-radius: 0.375rem;">
    
    <!-- HEADER ABU-ABU -->
    <div class="header-respo px-4 py-3 d-flex align-items-center">
        <a href="javascript:history.back()" class="text-white text-decoration-none me-3" title="Kembali">
            <i class="fas fa-arrow-left fs-5 fw-bold hover:text-warning"></i>
        </a>
        <div>
            <h5 class="mb-0 fw-bold" style="font-size: 20px;">Akses Responsibility</h5>
            <div class="text-light" style="font-size: 14px; opacity: 0.9;">Pilih salah satu Responsibility untuk mengakses fitur tertentu</div>
        </div>
    </div>

    <!-- CONTENT BODY -->
    <div class="card-body p-4 bg-white" style="min-height: 70vh; border-bottom-left-radius: 0.375rem; border-bottom-right-radius: 0.375rem;">
        
        <div id="loadingRespo" class="text-center py-5 text-muted fst-italic">
            <div class="spinner-border spinner-border-sm me-2" role="status"></div> 
            Memuat data responsibility...
        </div>

        <div class="row g-4" id="respoContainer">
            <!-- Data akan dirender di sini oleh JS -->
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const container = document.getElementById('respoContainer');
        const loading = document.getElementById('loadingRespo');
        
        const respoStorage = localStorage.getItem('respo');
        const currentRespo = respoStorage ? JSON.parse(respoStorage) : {};
        const activeId = currentRespo.id || null;

        try {
            const response = await fetch('http://localhost:3000/api/akses-respo');
            const result = await response.json();
            
            loading.classList.add('d-none'); 

            if(result.status === 'success' && result.data.length > 0) {
                result.data.forEach(item => {
                    const isActive = (item.id === activeId);
                    
                    const html = `
                        <div class="col-md-4">
                            <div class="card card-respo p-4 shadow-sm ${isActive ? 'active' : ''}" onclick="changeRespo(${item.id})">
                                <h5 class="mb-3 fw-bold" style="font-size: 18px;">${item.name}</h5>
                                <div class="text-respo">ROLE : ${item.roles}</div>
                                <div class="text-respo">SBU : ${item.sbu}</div>
                                <div class="text-respo">SUB : ${item.sub}</div>
                                <div class="text-respo">BRANCH : ${item.branch}</div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += html;
                });
            }
        } catch (error) {
            loading.innerHTML = '<span class="text-danger">Gagal memuat data dari server.</span>';
        }
    });

    async function changeRespo(id) {
        try {
            if (localStorage.getItem('respo')) {
                const confirm = await Swal.fire({
                    icon: 'warning',
                    text: 'Ubah Akses Respo?',
                    iconColor: '#1469AE',
                    confirmButtonColor: '#1469AE',
                    showDenyButton: true,
                    confirmButtonText: 'Ya',
                    denyButtonText: 'Batal'
                });

                if (!confirm.isConfirmed) return; 
            }

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const response = await fetch('http://localhost:3000/api/akses-respo/change', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ m_respo_id: id })
            });
            
            const result = await response.json();

            if(result.status === 'success') {
                localStorage.setItem('respo', JSON.stringify(result.data));
                Swal.close();
                window.location.reload(); 
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                text: error.message || 'Terjadi kesalahan'
            });
        }
    }
</script>
@endpush