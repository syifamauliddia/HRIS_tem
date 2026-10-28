@extends('layouts.app')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded">
    <div>
        <i class="fas fa-ellipsis-v text-muted me-2"></i> 
        <span class="fw-bold text-secondary">Dashboard</span>
    </div>
    <div class="text-muted small d-flex align-items-center gap-3">
        <span id="currentDate">Sen, 26 Okt 2026 23:48:33</span>
        <i class="far fa-copy cursor-pointer"></i>
        <i class="fas fa-expand cursor-pointer"></i>
        <i class="fas fa-user cursor-pointer"></i>
    </div>
</div>

<!-- Loading State -->
<div id="loadingState" class="text-center py-5 text-muted fst-italic">
    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
    Harap tunggu sistem sedang menyusun konten..
</div>

<!-- Dashboard Content (Disembunyikan saat loading) -->
<div id="dashboardContent" class="d-none">
    
    <!-- 4 Kartu Ringkasan -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 py-2">
                <div class="card-body">
                    <p class="text-dark mb-1 small fw-semibold">Total SUB Aktif</p>
                    <h3 class="text-success fw-bold mb-0" id="totalDivisi">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 py-2">
                <div class="card-body">
                    <p class="text-dark mb-1 small fw-semibold">Total Cabang Aktif</p>
                    <h3 class="text-success fw-bold mb-0" id="totalDepartemen">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 py-2">
                <div class="card-body">
                    <p class="text-dark mb-1 small fw-semibold">Pegawai Absen Hari Ini</p>
                    <h3 class="text-danger fw-bold mb-0" id="pegawaiAbsen">0</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 py-2">
                <div class="card-body">
                    <p class="text-dark mb-1 small fw-semibold">Pegawai Masuk Hari Ini</p>
                    <h3 class="text-success fw-bold mb-0" id="pegawaiMasuk">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Cabang -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-4">Pengeluaran Gaji Karyawan Bulan Ini Per Cabang</h6>
            <div id="chartCabang" style="height: 350px;"></div>
        </div>
    </div>

    <!-- Chart Direktorat -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-4">Pengeluaran Gaji Karyawan Bulan Ini Per Direktorat</h6>
            <div id="chartDirektorat" style="height: 350px;"></div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Library Highcharts -->
<script src="https://code.highcharts.com/highcharts.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        // Konfigurasi waktu real-time
        setInterval(() => {
            const now = new Date();
            document.getElementById('currentDate').innerText = now.toLocaleString('id-ID', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second:'2-digit' }).replace(/\./g, ':');
        }, 1000);

        try {
            // Ambil data dari Backend
            const response = await fetch('http://localhost:3000/api/dashboard');
            const data = await response.json();

            // Isi nilai pada 4 Kartu Ringkasan
            document.getElementById('totalDivisi').innerText = data.sub_count;
            document.getElementById('totalDepartemen').innerText = data.branch_count;
            document.getElementById('pegawaiAbsen').innerText = data.total_absen;
            document.getElementById('pegawaiMasuk').innerText = data.total_hadir;

            // Persiapan Data Chart Cabang
            const cabangCategories = data.branch_salary.map(item => item.branch_name);
            const cabangData = data.branch_salary.map(item => item.total_gaji);

            // Persiapan Data Chart Direktorat
            const direktoratCategories = data.subcomp_salary.map(item => item.subcomp_name);
            const direktoratData = data.subcomp_salary.map(item => item.total_gaji);

            // Opsi Global Highcharts (agar background transparan dan font kecil seperti di Vue)
            const commonChartOptions = {
                chart: { type: 'column', backgroundColor: 'transparent' },
                title: { text: null },
                credits: { enabled: false },
                legend: { enabled: false },
                yAxis: { min: 0, title: { text: null }, gridLineWidth: 0 },
                plotOptions: { column: { color: '#3b82f6', borderRadius: 2 } } // Biru default
            };

            // Render Chart Cabang
            Highcharts.chart('chartCabang', Highcharts.merge(commonChartOptions, {
                xAxis: { categories: cabangCategories, labels: { style: { fontSize: '10px' } } },
                series: [{ data: cabangData }]
            }));

            // Render Chart Direktorat
            Highcharts.chart('chartDirektorat', Highcharts.merge(commonChartOptions, {
                xAxis: { categories: direktoratCategories, labels: { style: { fontSize: '8px' } } },
                series: [{ data: direktoratData }]
            }));

            // Sembunyikan loading, tampilkan konten
            document.getElementById('loadingState').classList.add('d-none');
            document.getElementById('dashboardContent').classList.remove('d-none');

        } catch (error) {
            console.error('Gagal memuat dashboard:', error);
            document.getElementById('loadingState').innerHTML = '<span class="text-danger">Gagal memuat data dari server.</span>';
        }
    });
</script>
@endpush