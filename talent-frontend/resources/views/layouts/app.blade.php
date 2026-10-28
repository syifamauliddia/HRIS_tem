<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>TALENT</title>
    
    <!-- Script Proteksi Login -->
    <script>
        if (!localStorage.getItem('user')) {
            window.location.href = '/login';
        }
    </script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        .sidebar { width: 250px; height: 100vh; background: #ffffff; position: fixed; top: 0; left: 0; overflow-y: auto; overflow-x: hidden; border-right: 1px solid #dee2e6; }
        .main-content { margin-left: 250px; padding: 20px; }
        .nav-link { color: #333; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { background-color: #e9ecef; border-radius: 5px; }
        .sidebar [data-bs-toggle="collapse"] .fa-chevron-down { transition: transform 0.2s ease; }
        .sidebar [data-bs-toggle="collapse"][aria-expanded="false"] .fa-chevron-down { transform: rotate(-90deg); }
    </style>
</head>
<body>
    
    <!-- SIDEBAR -->
    <div class="sidebar p-3 shadow-sm d-flex flex-column">
        <h4 class="text-primary fw-bold text-center mb-4">TALENT</h4>
        
        <!-- Profil User (Dinamis) -->
        <div class="d-flex align-items-center mb-4 px-2">
            <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center me-2" style="width: 40px; height: 40px;" id="userAvatar">
                S
            </div>
            <div>
                <div class="fw-bold" id="userName">Loading...</div>
                <small class="text-success" id="userRole"><i class="fas fa-wifi"></i> Manager</small>
            </div>
        </div>
        
        <!-- Daftar Menu -->
        <ul class="nav flex-column mb-auto">
    <!-- 1. Menu Dashboard & Single Items -->
    <li class="nav-item">
        <a class="nav-link text-dark" href="/dashboard"><i class="fas fa-tachometer-alt me-2 text-secondary"></i> Dashboard</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark" href="/change-akses"><i class="fas fa-cog me-2 text-secondary"></i> Change Akses Respo</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark" href="/notification"><i class="fas fa-bell me-2 text-secondary"></i> Notification</a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-dark" href="/absensi"><i class="fas fa-camera me-2 text-secondary"></i> Absensi Online</a>
    </li>

    <!-- 2. Menu Utama: Master -->
    <li class="nav-item mt-2">
        <a class="nav-link text-dark d-flex justify-content-between align-items-center fw-bold" href="#masterRootMenu" data-bs-toggle="collapse" aria-expanded="false">
            <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Master</span>
            <i class="fas fa-chevron-down fa-xs text-muted"></i>
        </a>
        
        <ul class="collapse list-unstyled ms-3 mt-1 border-start ps-2" id="masterRootMenu">
            <li><a class="nav-link text-secondary" href="/master_menu"><i class="fas fa-list me-2"></i> Menu</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-fingerprint me-2"></i> Mesin Fingerprint</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-bookmark me-2"></i> General</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-tasks me-2"></i> Approval</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-font me-2"></i> Prefix Nomor</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-list-ol me-2"></i> Format Nomor</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-building me-2"></i> SBU</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-city me-2"></i> Company</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-bookmark me-2"></i> Cabang</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-building me-2"></i> SUB</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-sitemap me-2"></i> Divisi</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-users me-2"></i> Pengguna</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-user-shield me-2"></i> Responsibility</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-user-lock me-2"></i> Role</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-map-marker-alt me-2"></i> Presensi Lokasi</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-map-marker-alt me-2"></i> Lokasi</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-landmark me-2"></i> Zona</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-shopping-bag me-2"></i> Jenis Biaya</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-briefcase me-2"></i> Cost Center</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-clock me-2"></i> Jam Kerja</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-trophy me-2"></i> Kompetensi</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-user me-2"></i> Karyawan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-folder me-2"></i> Berkas</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-bullhorn me-2"></i> Pengumuman</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-user-tag me-2"></i> Grading</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-calendar-alt me-2"></i> Libur Nasional</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-user-tie me-2"></i> Jabatan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-coins me-2"></i> Tunjangan Kemahalan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-coins me-2"></i> Tunjangan Fungsional</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-sitemap me-2"></i> Struktur Jabatan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-sitemap me-2"></i> Struktur Company</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-user-shield me-2"></i> Level Jabatan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-money-check-alt me-2"></i> Master Gaji</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-id-card me-2"></i> Jobdesc</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-coins me-2"></i> Tunjangan Jabatan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-money-bill me-2"></i> Master Gaji Harian</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-sync-alt me-2"></i> Update Version Mobile</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-box me-2"></i> Media</a></li>
        </ul>
    </li>

    <!-- 3. Menu Utama: Penilaian -->
    <li class="nav-item mt-2">
        <a class="nav-link text-dark d-flex justify-content-between align-items-center fw-bold" href="#penilaianMenu" data-bs-toggle="collapse" aria-expanded="false">
            <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Penilaian</span>
            <i class="fas fa-chevron-down fa-xs text-muted"></i>
        </a>
        
        <ul class="collapse list-unstyled ms-3 mt-1 border-start ps-2" id="penilaianMenu">
            <li class="nav-item mt-1">
                <a class="nav-link text-dark d-flex justify-content-between align-items-center" href="#masterPenilaianMenu" data-bs-toggle="collapse" aria-expanded="false">
                    <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Master</span>
                    <i class="fas fa-chevron-down fa-xs text-muted"></i>
                </a>
                <ul class="collapse list-unstyled ms-4 mt-1" id="masterPenilaianMenu">
                    <li><a class="nav-link text-secondary" href="/master-penilaian"><i class="fas fa-user-cog me-2"></i> Penilaian Karyawan</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-user-tag me-2"></i> Komponen 360</a></li>
                </ul>
            </li>
            <li class="nav-item mt-1">
                <a class="nav-link text-dark d-flex justify-content-between align-items-center" href="#transaksiPenilaianMenu" data-bs-toggle="collapse" aria-expanded="false">
                    <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Transaksi</span>
                    <i class="fas fa-chevron-down fa-xs text-muted"></i>
                </a>
                <ul class="collapse list-unstyled ms-4 mt-1" id="transaksiPenilaianMenu">
                    <li><a class="nav-link text-secondary" href="/penilaian-360"><i class="fas fa-users me-2"></i> Penilaian 360</a></li>
                </ul>
            </li>
        </ul>
    </li>

    <!-- 4. Menu Utama: Perjalanan Dinas -->
    <li class="nav-item mt-2">
        <a class="nav-link text-dark d-flex justify-content-between align-items-center fw-bold" href="#perjalananDinasMenu" data-bs-toggle="collapse" aria-expanded="false">
            <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Perjalanan Dinas</span>
            <i class="fas fa-chevron-down fa-xs text-muted"></i>
        </a>
        
        <ul class="collapse list-unstyled ms-3 mt-1 border-start ps-2" id="perjalananDinasMenu">
            <li class="nav-item mt-1">
                <a class="nav-link text-dark d-flex justify-content-between align-items-center" href="#transaksiPerjalananDinasMenu" data-bs-toggle="collapse" aria-expanded="false">
                    <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Transaksi</span>
                    <i class="fas fa-chevron-down fa-xs text-muted"></i>
                </a>
                <ul class="collapse list-unstyled ms-4 mt-1" id="transaksiPerjalananDinasMenu">
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-user-tag me-2"></i> Penugasan Dinas Karyawan</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-book me-2"></i> Kas Bon Sementara</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-plane me-2"></i> Rencana Perjalanan Dinas</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-alt me-2"></i> Penyelesaian Perjalanan Dinas</a></li>
                </ul>
            </li>
            <li class="nav-item mt-1">
                <a class="nav-link text-dark d-flex justify-content-between align-items-center" href="#masterPerjalananDinasMenu" data-bs-toggle="collapse" aria-expanded="false">
                    <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Master</span>
                    <i class="fas fa-chevron-down fa-xs text-muted"></i>
                </a>
                <ul class="collapse list-unstyled ms-4 mt-1" id="masterPerjalananDinasMenu">
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-money-bill-wave me-2"></i> Tarif Perjalanan Dinas</a></li>
                </ul>
            </li>
        </ul>
    </li>

    <!-- 5. Menu Utama: Pelatihan -->
    <li class="nav-item mt-2">
        <a class="nav-link text-dark d-flex justify-content-between align-items-center fw-bold" href="#pelatihanMenu" data-bs-toggle="collapse" aria-expanded="false">
            <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Pelatihan</span>
            <i class="fas fa-chevron-down fa-xs text-muted"></i>
        </a>
        <ul class="collapse list-unstyled ms-3 mt-1 border-start ps-2" id="pelatihanMenu">
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-arrow-right me-2"></i> Master</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-arrow-right me-2"></i> Transaksi</a></li>
        </ul>
    </li>

    <!-- 6. Menu Utama: Recruitment -->
    <li class="nav-item mt-2">
        <a class="nav-link text-dark d-flex justify-content-between align-items-center fw-bold" href="#recruitmentMenu" data-bs-toggle="collapse" aria-expanded="false">
            <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Recruitment</span>
            <i class="fas fa-chevron-down fa-xs text-muted"></i>
        </a>
        <ul class="collapse list-unstyled ms-3 mt-1 border-start ps-2" id="recruitmentMenu">
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-arrow-right me-2"></i> Transaksi</a></li>
        </ul>
    </li>

    <!-- 7. Menu Utama: Transaksi Root -->
    <li class="nav-item mt-2">
        <a class="nav-link text-dark d-flex justify-content-between align-items-center fw-bold" href="#transaksiRootMenu" data-bs-toggle="collapse" aria-expanded="false">
            <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Transaksi</span>
            <i class="fas fa-chevron-down fa-xs text-muted"></i>
        </a>
        <ul class="collapse list-unstyled ms-3 mt-1 border-start ps-2" id="transaksiRootMenu">
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-calendar-alt me-2"></i> Jadwal Kerja</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-signature me-2"></i> Mutasi & Persuratan Karyawan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-briefcase me-2"></i> Riwayat Posisi</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-stopwatch me-2"></i> Lembur</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-clock me-2"></i> Lembur Harian</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-user-md me-2"></i> Klaim Rawat Inap</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-calendar-minus me-2"></i> Cuti</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-calendar-check me-2"></i> Cuti Mandiri</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-plus-circle me-2"></i> Bonus</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-plus-square me-2"></i> Klaim Askes</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-calendar-day me-2"></i> Presensi Absensi</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-calendar-week me-2"></i> Jadwal Kerja Karyawan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-export me-2"></i> Pengajuan Pekerjaan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-invoice me-2"></i> Laporan Pekerjaan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-clipboard-list me-2"></i> Presensi Absensi Harian</a></li>
        </ul>
    </li>

    <!-- 8. Menu Utama: Outsourcing -->
    <li class="nav-item mt-2">
        <a class="nav-link text-dark d-flex justify-content-between align-items-center fw-bold" href="#outsourcingMenu" data-bs-toggle="collapse" aria-expanded="false">
            <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Outsourcing</span>
            <i class="fas fa-chevron-down fa-xs text-muted"></i>
        </a>
        <ul class="collapse list-unstyled ms-3 mt-1 border-start ps-2" id="outsourcingMenu">
            <li class="nav-item mt-1">
                <a class="nav-link text-dark d-flex justify-content-between align-items-center" href="#transaksiOutsourcingMenu" data-bs-toggle="collapse" aria-expanded="false">
                    <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Transaksi</span>
                    <i class="fas fa-chevron-down fa-xs text-muted"></i>
                </a>
                <ul class="collapse list-unstyled ms-4 mt-1" id="transaksiOutsourcingMenu">
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-clock me-2"></i> Lembur Harian OS</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-chart-bar me-2"></i> Perhitungan Gaji Borongan OS</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-chart-line me-2"></i> Perhitungan Gaji Harian OS</a></li>
                </ul>
            </li>
            <li class="nav-item mt-1">
                <a class="nav-link text-dark d-flex justify-content-between align-items-center" href="#masterOutsourcingMenu" data-bs-toggle="collapse" aria-expanded="false">
                    <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Master</span>
                    <i class="fas fa-chevron-down fa-xs text-muted"></i>
                </a>
                <ul class="collapse list-unstyled ms-4 mt-1" id="masterOutsourcingMenu">
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-money-bill-wave me-2"></i> Master Gaji Harian OS</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-city me-2"></i> Company Outsourcing</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-user-friends me-2"></i> Karyawan Outsourcing</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-calendar-check me-2"></i> Presensi Absensi OS</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-calendar-alt me-2"></i> Jadwal Kerja Karyawan OS</a></li>
                </ul>
            </li>
        </ul>
    </li>

    <!-- 9. Menu Utama: Payroll -->
    <li class="nav-item mt-2">
        <a class="nav-link text-dark d-flex justify-content-between align-items-center fw-bold" href="#payrollMenu" data-bs-toggle="collapse" aria-expanded="false">
            <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Payroll</span>
            <i class="fas fa-chevron-down fa-xs text-muted"></i>
        </a>
        <ul class="collapse list-unstyled ms-3 mt-1 border-start ps-2" id="payrollMenu">
            <li class="nav-item mt-1">
                <a class="nav-link text-dark d-flex justify-content-between align-items-center" href="#transaksiPayrollMenu" data-bs-toggle="collapse" aria-expanded="false">
                    <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Transaksi</span>
                    <i class="fas fa-chevron-down fa-xs text-muted"></i>
                </a>
                <ul class="collapse list-unstyled ms-4 mt-1" id="transaksiPayrollMenu">
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-minus-circle me-2"></i> Potongan</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-calculator me-2"></i> Perhitungan Gaji</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-chart-bar me-2"></i> Perhitungan Gaji Harian</a></li>
                    <li><a class="nav-link text-secondary" href="#"><i class="fas fa-check-circle me-2"></i> Finalisasi Gaji</a></li>
                </ul>
            </li>
        </ul>
    </li>

    <!-- 10. Menu Utama: Laporan -->
    <li class="nav-item mt-2">
        <a class="nav-link text-dark d-flex justify-content-between align-items-center fw-bold" href="#laporanMenu" data-bs-toggle="collapse" aria-expanded="false">
            <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Laporan</span>
            <i class="fas fa-chevron-down fa-xs text-muted"></i>
        </a>
        <ul class="collapse list-unstyled ms-3 mt-1 border-start ps-2" id="laporanMenu">
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-alt me-2"></i> Absensi Karyawan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-invoice-dollar me-2"></i> Slip Gaji</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-csv me-2"></i> Eksport CSV</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-invoice-dollar me-2"></i> Slip Gaji User</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-csv me-2"></i> Eksport Karyawan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-bookmark me-2"></i> Laporan Gaji</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-csv me-2"></i> Eksport BPJS Ketenagakerjaan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-csv me-2"></i> Eksport BPJS Kesehatan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-pdf me-2"></i> Laporan Surat Peringatan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-csv me-2"></i> Laporan BPJS Kesehatan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-csv me-2"></i> Laporan BPJS Ketenagakerjaan</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-file-csv me-2"></i> Laporan Pajak Rekonsiliasi</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-bookmark me-2"></i> Laporan Klaim Akses</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-bookmark me-2"></i> Laporan Klaim Rawat Inap</a></li>
            <li><a class="nav-link text-secondary" href="#"><i class="fas fa-bookmark me-2"></i> Laporan Perjalanan Dinas</a></li>
        </ul>
    </li>

    <hr class="my-3">

    <!-- 11. Tombol Logout -->
    <li class="nav-item">
        <button onclick="logout()" class="nav-link text-danger border-0 bg-transparent w-100 text-start fw-bold">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
    </li>
</ul>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Fungsi Logout
        function logout() {
            localStorage.removeItem('user');
            window.location.href = '/login';
        }

        // Fungsi Load Data User ke Sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const userStorage = localStorage.getItem('user');
            if (userStorage) {
                const userObj = JSON.parse(userStorage);
                const userData = userObj.data || userObj;
                
                if (userData.name) {
                    document.getElementById('userName').innerText = userData.name;
                    document.getElementById('userAvatar').innerText = userData.name.charAt(0).toUpperCase();
                }
                if (userData.role) {
                    document.getElementById('userRole').innerHTML = `<i class="fas fa-wifi"></i> ${userData.role}`;
                }
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>