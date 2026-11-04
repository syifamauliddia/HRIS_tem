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

    <!-- Turbo Drive: navigasi sidebar Penilaian tanpa reload penuh -->
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.23/dist/turbo.es2017-umd.js"></script>
    <script>
        if (window.Turbo && Turbo.session) {
            Turbo.session.drive = false;
        }
        document.addEventListener('DOMContentLoaded', function () {
            if (window.Turbo && Turbo.session) Turbo.session.drive = false;
        });

        window.registerPageInit = function (init) {
            if (window.__appPageInitHandler) {
                document.removeEventListener('turbo:load', window.__appPageInitHandler);
            }
            window.__appPageInitHandler = init;
            if (window.Turbo) {
                document.addEventListener('turbo:load', init);
            } else {
                document.addEventListener('DOMContentLoaded', init);
            }
        };
    </script>
    <meta name="turbo-cache-control" content="no-cache">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        .sidebar { width: 250px; height: 100vh; background: #ffffff; position: fixed; top: 0; left: 0; overflow-y: auto; overflow-x: hidden; border-right: 1px solid #dee2e6; }
        .main-content { margin-left: 250px; padding: 18px 24px; min-height: 100vh; background-color: #f8f9fa; }
        .nav-link { color: #333; margin-bottom: 4px; padding: 6px 12px; font-size: 13.5px; }
        .nav-link:hover, .nav-link.active { background-color: #e9ecef; border-radius: 5px; }
        .sidebar [data-bs-toggle="collapse"] .fa-chevron-down { transition: transform 0.2s ease; }
        .sidebar [data-bs-toggle="collapse"][aria-expanded="false"] .fa-chevron-down { transform: rotate(-90deg); }
        .turbo-progress-bar { visibility: hidden; }
    </style>
</head>
<body>
    @php
        $isMasterPenilaian = request()->is('master-penilaian*');
        $isPenilaian360 = request()->is('penilaian-360*');
        $isPenilaianSummary = request()->is('penilaian-summary*');
        $isPenilaianMenu = $isMasterPenilaian || $isPenilaian360 || $isPenilaianSummary;
    @endphp

    <!-- SIDEBAR -->
    <div class="sidebar p-3 shadow-sm d-flex flex-column">
        <!-- Logo / App Title -->
        <div class="d-flex align-items-center mb-3 px-1">
            <div class="me-2 text-center" style="width: 36px; height: 36px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold;" id="userAvatar">
                <i class="fas fa-leaf text-success" style="font-size: 16px;"></i>
            </div>
            <div>
                <div class="fw-bold text-primary" style="font-size: 16px; line-height: 1.2;">HRIS TRIAL</div>
                <div class="d-flex align-items-center gap-1" style="font-size: 12px;">
                    <i class="fas fa-wifi text-success" style="font-size: 10px;"></i>
                    <span class="text-secondary fw-semibold" id="userName">Syifa</span>
                </div>
                <div class="text-primary fw-bold" style="font-size: 12px; line-height: 1;" id="userRole">Manager</div>
            </div>
        </div>

        <!-- Search Menu Input -->
        <div class="mb-3 px-1">
            <div class="input-group input-group-sm" style="background: #f1f5f9; border-radius: 6px; border: 1px solid #e2e8f0;">
                <span class="input-group-text bg-transparent border-0 text-muted pe-1"><i class="fas fa-search" style="font-size: 11px;"></i></span>
                <input type="text" class="form-control bg-transparent border-0 ps-1 text-muted" placeholder="Cari Menu (Ctrl+K)" style="font-size: 12px; height: 30px;">
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
                <a class="nav-link text-dark d-flex justify-content-between align-items-center fw-bold" href="#masterRootMenu" data-bs-toggle="collapse" aria-expanded="{{ request()->is('master_menu*') || request()->is('m_menu*') ? 'true' : 'false' }}">
                    <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Master</span>
                    <i class="fas fa-chevron-down fa-xs text-muted"></i>
                </a>
                
                <ul class="collapse {{ request()->is('master_menu*') || request()->is('m_menu*') ? 'show' : '' }} list-unstyled ms-3 mt-1 border-start ps-2" id="masterRootMenu">
                    <li>
                        <a class="nav-link {{ request()->is('master_menu*') || request()->is('m_menu*') ? 'text-primary' : 'text-secondary' }}" 
                           style="{{ request()->is('master_menu*') || request()->is('m_menu*') ? 'background-color: #e0edff; color: #1e40af !important; border-radius: 6px; font-weight: 500;' : '' }}" 
                           href="/master_menu">
                            <i class="fas fa-list me-2"></i> Menu
                        </a>
                    </li>
                    <li><a class="nav-link text-secondary" href="/master_fingerprint"><i class="fas fa-fingerprint me-2"></i> Mesin Fingerprint</a></li>
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
                <a class="nav-link text-dark d-flex justify-content-between align-items-center fw-bold" href="#penilaianMenu" data-bs-toggle="collapse" aria-expanded="{{ $isPenilaianMenu ? 'true' : 'false' }}">
                    <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Penilaian</span>
                    <i class="fas fa-chevron-down fa-xs text-muted"></i>
                </a>

                <ul class="collapse {{ $isPenilaianMenu ? 'show' : '' }} list-unstyled ms-3 mt-1 border-start ps-2" id="penilaianMenu">
                    <!-- Submenu Master -->
                    <li class="nav-item mt-1">
                        <a class="nav-link text-dark d-flex justify-content-between align-items-center" href="#masterPenilaianMenu" data-bs-toggle="collapse" aria-expanded="{{ $isPenilaianMenu ? 'true' : 'false' }}">
                            <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Master</span>
                            <i class="fas fa-chevron-down fa-xs text-muted"></i>
                        </a>
                        <ul class="collapse {{ $isPenilaianMenu ? 'show' : '' }} list-unstyled ms-4 mt-1" id="masterPenilaianMenu">
                            <li><a class="nav-link text-secondary" href="/master-penilaian" data-turbo-penilaian><i class="fas fa-user-cog me-2"></i>Komponen Penilaian Karyawan</a></li>
                        </ul>
                    </li>

                    <!-- Submenu Transaksi -->
                    <li class="nav-item mt-1">
                        <a class="nav-link text-dark d-flex justify-content-between align-items-center" href="#transaksiPenilaianMenu" data-bs-toggle="collapse" aria-expanded="{{ $isPenilaianMenu ? 'true' : 'false' }}">
                            <span><i class="fas fa-arrow-right me-2 text-secondary"></i> Transaksi</span>
                            <i class="fas fa-chevron-down fa-xs text-muted"></i>
                        </a>
                        <ul class="collapse {{ $isPenilaianMenu ? 'show' : '' }} list-unstyled ms-4 mt-1" id="transaksiPenilaianMenu">
                            <li><a class="nav-link text-secondary" href="/penilaian-360" data-turbo-penilaian><i class="fas fa-users me-2"></i> Penilaian 360</a></li>
                        </ul>
                    </li>

                    <!-- TAMBAHAN BARU: Menu Summary & 9-Box di bawah Transaksi -->
                    <li class="nav-item mt-1">
                        <a class="nav-link text-secondary" href="/penilaian-summary" data-turbo-penilaian>
                            <i class="fas fa-chart-pie me-2"></i> Summary & 9-Box
                        </a>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" data-turbo-eval="false"></script>

    <script>
        // Fungsi Logout
        function logout() {
            localStorage.removeItem('user');
            window.location.href = '/login';
        }

        // Bersihkan interval & listener keydown halaman lama sebelum Turbo mengganti body.
        if (!window.__turboLifecyclePatch) {
            window.__turboLifecyclePatch = true;
            (function () {
                const trackedIntervals = new Set();
                const nativeSetInterval = window.setInterval.bind(window);
                const nativeClearInterval = window.clearInterval.bind(window);

                window.setInterval = function (fn, delay) {
                    const args = Array.prototype.slice.call(arguments, 2);
                    const id = nativeSetInterval.apply(window, [fn, delay].concat(args));
                    trackedIntervals.add(id);
                    return id;
                };
                window.clearInterval = function (id) {
                    trackedIntervals.delete(id);
                    return nativeClearInterval(id);
                };

                const trackedKeydown = new Set();
                const nativeAddEventListener = document.addEventListener.bind(document);
                const nativeRemoveEventListener = document.removeEventListener.bind(document);

                document.addEventListener = function (type, listener, options) {
                    if (type === 'keydown' && listener) {
                        trackedKeydown.add({ listener: listener, options: options });
                    }
                    return nativeAddEventListener(type, listener, options);
                };

                document.addEventListener('turbo:before-render', function () {
                    trackedIntervals.forEach(function (id) { nativeClearInterval(id); });
                    trackedIntervals.clear();
                    trackedKeydown.forEach(function (item) {
                        nativeRemoveEventListener('keydown', item.listener, item.options);
                    });
                    trackedKeydown.clear();
                });
            })();
        }

        // Navigasi SPA hanya untuk link sidebar Penilaian.
        if (window.__penilaianSpaHandler) {
            document.removeEventListener('click', window.__penilaianSpaHandler);
        }
        window.__penilaianSpaHandler = function (e) {
            const link = e.target.closest('a[data-turbo-penilaian]');
            if (!link) return;
            if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
            if (link.target && link.target !== '_self') return;

            const url = new URL(link.href, window.location.href);
            if (url.origin !== window.location.origin) return;
            e.preventDefault();

            if (url.pathname === window.location.pathname && url.search === window.location.search) return;

            if (window.Turbo) {
                Turbo.visit(url.href);
            } else {
                window.location.href = url.href;
            }
        };
        document.addEventListener('click', window.__penilaianSpaHandler);

        // Load data user ke sidebar (initial + setelah navigasi Turbo).
        function updateSidebarUser() {
            const userStorage = localStorage.getItem('user');
            if (!userStorage) return;
            try {
                const userObj = JSON.parse(userStorage);
                const userData = userObj.data || userObj;

                if (userData.name) {
                    const nameEl = document.getElementById('userName');
                    if (nameEl) nameEl.innerText = userData.name;
                }
                if (userData.role) {
                    const roleEl = document.getElementById('userRole');
                    if (roleEl) roleEl.innerText = userData.role;
                }
            } catch (e) {
                console.error('Gagal membaca data user:', e);
            }
        }

        updateSidebarUser();
        if (window.__sidebarUserHandler) {
            document.removeEventListener('turbo:load', window.__sidebarUserHandler);
        }
        window.__sidebarUserHandler = updateSidebarUser;
        document.addEventListener('turbo:load', window.__sidebarUserHandler);
    </script>
    
    @stack('scripts')
</body>
</html>