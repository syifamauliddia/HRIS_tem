const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const mysql = require('mysql2/promise');

const app = express();
app.use(cors());
app.use(bodyParser.json());

// Setup koneksi ke database talent_db
const pool = mysql.createPool({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'talent_db'
});

// Test route untuk memastikan API jalan
app.get('/', (req, res) => {
    res.send('Backend Talent API Running!');
});

// -------------------------------------------------------------
// 1. API Authentication / Login
// -------------------------------------------------------------
app.post('/api/login', async (req, res) => {
    const { username, password, token_fcm } = req.body;
    
    try {
        const [rows] = await pool.query(
            'SELECT id, username, name, role FROM users WHERE username = ? AND password = ?',
            [username, password]
        );

        if (rows.length === 0) {
            return res.status(401).json({ message: 'Username atau Password Salah!' });
        }

        const user = rows[0];

        res.json({
            token: 'bearer-token-talent-' + Date.now(),
            token_type: 'Bearer',
            data: {
                id: user.id,
                username: user.username,
                name: user.name,
                role: user.role
            }
        });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// -------------------------------------------------------------
// 2. API Master Assessment (Penilaian Karyawan)
// -------------------------------------------------------------
// GET: Ambil daftar list master assessment
app.get('/api/master-assessments', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM master_assessments ORDER BY id DESC');
        res.json({ status: 'success', data: rows });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// POST: Simpan data form master assessment baru
app.post('/api/master-assessments', async (req, res) => {
    const { sbu, sub, branch, level, divisi, tipe_penilaian, deskripsi, status } = req.body;
    try {
        const [result] = await pool.query(
            `INSERT INTO master_assessments (sbu, sub, branch, level, divisi, tipe_penilaian, deskripsi, status) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
            [sbu, sub, branch, level, divisi, tipe_penilaian, deskripsi, status || 'Active']
        );
        res.json({ status: 'success', message: 'Data master assessment berhasil disimpan', id: result.insertId });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 3. API Transaksi Penilaian 360
// -------------------------------------------------------------
// GET: Ambil daftar list transaksi 360
app.get('/api/transaction-360', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM transaction_360 ORDER BY id DESC');
        res.json({ status: 'success', data: rows });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 4. API Dashboard
// -------------------------------------------------------------
app.get('/api/dashboard', (req, res) => {
    // Data tiruan (dummy) menyesuaikan hasil pada gambar
    res.json({
        sub_count: 0,
        branch_count: 17,
        total_absen: 505,
        total_hadir: 0,
        branch_salary: [
            { branch_name: 'Temprina Perkom Sumengko', total_gaji: 0 },
            { branch_name: 'Temprina Perkom Nganjuk', total_gaji: 0 },
            { branch_name: 'Temprina Perkom Bawen', total_gaji: 0 },
            { branch_name: 'TWL Malang', total_gaji: 0 },
            { branch_name: 'Shopping Bag Bekasi', total_gaji: 0 },
            { branch_name: 'Packaging Komersial Bekasi', total_gaji: 0 },
            { branch_name: 'Perkom DCI Nganjuk', total_gaji: 0 },
            { branch_name: 'Perkom Aliansi Cengkareng', total_gaji: 0 },
            { branch_name: 'JPBOOK Surabaya', total_gaji: 0 },
            { branch_name: 'DIM Surabaya', total_gaji: 0 },
            { branch_name: 'HOLDING', total_gaji: 0 }
        ],
        subcomp_salary: [
            { subcomp_name: 'Temprina Perkom SBN', total_gaji: 0 },
            { subcomp_name: 'Temprina Perkom Bali', total_gaji: 0 },
            { subcomp_name: 'Temprina Packaging Komersial', total_gaji: 0 },
            { subcomp_name: 'Temprina Packaging Shopping Bag', total_gaji: 0 },
            { subcomp_name: 'DIM', total_gaji: 0 },
            { subcomp_name: 'Wrapping Craft', total_gaji: 0 },
            { subcomp_name: 'Perkom Aliansi', total_gaji: 0 },
            { subcomp_name: 'Perkom DCI', total_gaji: 0 },
            { subcomp_name: 'Publishing DCP', total_gaji: 0 },
            { subcomp_name: 'HOLDING', total_gaji: 0 }
        ]
    });
});

// -------------------------------------------------------------
// 5. API Akses Responsibility
// -------------------------------------------------------------
// GET: Ambil daftar akses responsibility
app.get('/api/akses-respo', (req, res) => {
    res.json({
        status: 'success',
        data: [
            {
                id: 1,
                name: 'Manager',
                roles: 'Assistant Manager, Wakadiv, Kadiv, Wakaru, Karu, Staff, Manager',
                sbu: 'PACKAGING',
                sub: 'Temprina Packaging Komersial',
                branch: 'Packaging Komersial Karah'
            },
            {
                id: 2,
                name: 'Staff IT',
                roles: 'Software Engineer, IT Support',
                sbu: 'HEAD OFFICE',
                sub: 'Corporate IT',
                branch: 'Surabaya Pusat'
            }
        ]
    });
});

// POST: Ubah akses responsibility
app.post('/api/akses-respo/change', (req, res) => {
    const { m_respo_id } = req.body;
    
    // Simulasi respons sukses mengubah role
    res.json({
        status: 'success',
        message: 'Akses berhasil diubah',
        data: {
            id: m_respo_id,
            role_aktif: m_respo_id === 1 ? 'Manager' : 'Staff IT'
        }
    });
});

// -------------------------------------------------------------
// 6. API Notification (Outstanding Approval)
// -------------------------------------------------------------
app.get('/api/notification/outstanding', (req, res) => {
    // Simulasi data dummy dari database
    const data = [
        {
            id: 1,
            trx_name: 'Pengajuan Cuti',
            nomor: 'APP/CUTI/2026/10/001',
            trx_nomor: 'TRX-CT-001',
            creator: 'Budi Santoso',
            trx_date: '2026-10-25',
            created_at: '2026-10-25 10:00:00',
            status: 'Menunggu Approval',
            form_name: 'transaksi/cuti'
        },
        {
            id: 2,
            trx_name: 'Pengajuan Cuti',
            nomor: 'APP/CUTI/2026/10/002',
            trx_nomor: 'TRX-CT-002',
            creator: 'Siti Aminah',
            trx_date: '2026-10-26',
            created_at: '2026-10-26 14:30:00',
            status: 'Menunggu Approval',
            form_name: 'transaksi/cuti'
        },
        {
            id: 3,
            trx_name: 'Lembur Harian',
            nomor: 'APP/LMBR/2026/10/001',
            trx_nomor: 'TRX-LM-001',
            creator: 'Andi Wijaya',
            trx_date: '2026-10-27',
            created_at: '2026-10-27 08:15:00',
            status: 'Menunggu Approval',
            form_name: 'transaksi/lembur'
        }
    ];

    res.json({ status: 'success', data: data });
});

// -------------------------------------------------------------
// 7. API Transaksi Absensi Karyawan (Presensi Absensi)
// -------------------------------------------------------------

// GET: Rekapitulasi Absensi Harian Per Bulan
app.get('/api/presensi_absensi/get_by_daily', (req, res) => {
    const { month } = req.query; // YYYY-MM
    const selectedMonth = month || new Date().toISOString().slice(0, 7);
    
    // Data dummy rekap harian bulan ini
    const daysData = [];
    const totalDaysInMonth = 31; // Contoh 31 hari

    for (let day = 1; day <= 15; day++) {
        const dateStr = `${selectedMonth}-${String(day).padStart(2, '0')}`;
        const dayName = new Date(dateStr).toLocaleDateString('id-ID', { weekday: 'long' });
        const isWeekend = dayName === 'Sabtu' || dayName === 'Minggu';

        daysData.push({
            day_name_idn: dayName,
            all_days_of_month: dateStr,
            attend: isWeekend ? 0 : 450,
            cuti: isWeekend ? 0 : 25,
            alpha: isWeekend ? 0 : 30,
            total_kary: 505,
            presentase: isWeekend ? 0 : 89.1,
            type: isWeekend ? 'Hari Libur' : 'Hari Kerja'
        });
    }

    res.json({ code: 200, status: 'success', data: daysData });
});

// GET: Detail Karyawan Pada Tanggal Tertentu
app.get('/api/presensi_absensi/get_by_date', (req, res) => {
    const { date } = req.query;
    
    const sampleData = [
        {
            presensi_absensi_id: 101,
            default_user_id: 1,
            kode: 'EMP-001',
            nama_lengkap: 'Syifa Mauliddia',
            dept: 'FASILKOM / Software Engineering',
            jam_kerja: '08:00 - 17:00',
            desc: 'Reguler Pagi',
            absensi: {
                presensi_absensi_id: 101,
                tanggal: date || '2026-10-28',
                status: 'ATTEND',
                checkin_time: '07:45:10',
                checkin_on_scope: true,
                checkin_foto: 'https://via.placeholder.com/166x200.png?text=Foto+Checkin',
                checkin_region: 'Surabaya Karah',
                checkin_address: 'Jl. Ahmad Yani No. 88, Surabaya',
                checkin_lat: '-7.3244677',
                checkin_long: '112.7550714',
                catatan_in: 'Tepat waktu',
                checkout_time: '17:05:30',
                checkout_on_scope: true,
                checkout_foto: 'https://via.placeholder.com/166x200.png?text=Foto+Checkout',
                checkout_region: 'Surabaya Karah',
                checkout_address: 'Jl. Ahmad Yani No. 88, Surabaya',
                checkout_lat: '-7.3244677',
                checkout_long: '112.7550714',
                catatan_out: 'Pulang sesuai jam'
            }
        },
        {
            presensi_absensi_id: null,
            default_user_id: 2,
            kode: 'EMP-002',
            nama_lengkap: 'Budi Santoso',
            dept: 'Operasional',
            jam_kerja: '08:00 - 17:00',
            desc: 'Reguler Pagi',
            absensi: {
                presensi_absensi_id: null,
                tanggal: date || '2026-10-28',
                status: 'NOT ATTEND',
                checkin_time: '',
                checkin_on_scope: false,
                checkin_foto: '',
                checkin_region: '',
                checkin_address: '',
                catatan_in: '',
                checkout_time: '',
                checkout_on_scope: false,
                checkout_foto: '',
                checkout_region: '',
                checkout_address: '',
                catatan_out: ''
            }
        }
    ];

    res.json({ code: 200, status: 'success', data: sampleData });
});

// GET: Ambil Detail Single Record Absensi (Untuk Edit)
app.get('/api/presensi_absensi/:id', (req, res) => {
    res.json({
        status: 'success',
        data: {
            id: req.params.id,
            default_user_id: 1,
            tanggal: '2026-10-28',
            status: 'ATTEND',
            shift: 'Reguler Pagi',
            checkin_time: '07:45',
            checkin_foto: 'https://via.placeholder.com/200x370.png?text=Foto+Checkin',
            checkin_region: 'Surabaya Karah',
            checkin_lat: '-7.3244677',
            checkin_long: '112.7550714',
            checkin_address: 'Jl. Ahmad Yani No. 88, Surabaya',
            catatan_in: 'Checkin aman',
            checkin_on_scope: 1,
            checkout_time: '17:05',
            checkout_foto: 'https://via.placeholder.com/200x370.png?text=Foto+Checkout',
            checkout_region: 'Surabaya Karah',
            checkout_lat: '-7.3244677',
            checkout_long: '112.7550714',
            checkout_address: 'Jl. Ahmad Yani No. 88, Surabaya',
            catatan_out: 'Checkout aman',
            checkout_on_scope: 1
        }
    });
});

// POST & PUT: Simpan / Update Absensi
app.post('/api/presensi_absensi', (req, res) => {
    res.json({ status: 'success', message: 'Data absensi berhasil disimpan' });
});
app.put('/api/presensi_absensi/:id', (req, res) => {
    res.json({ status: 'success', message: 'Data absensi berhasil diperbarui' });
});

// DELETE: Reset / Kosongkan Absensi
app.delete('/api/presensi_absensi/:id', (req, res) => {
    res.json({ status: 'success', message: 'Data absensi berhasil dikosongkan' });
});

app.listen(3000, () => console.log('Backend JS running on port 3000'));

// -------------------------------------------------------------
// API Master Menu (m_menu)
// -------------------------------------------------------------

// Data Dummy Master Menu
let masterMenuData = [
    { id: 1, modul: 'Penilaian', submodul: 'Transaksi', menu: 'Penilaian 360', path: '/t_penilaian360', endpoint: '/t_penilaian360', icon: 'users', sequence: 2.16, description: '', is_active: 1 },
    { id: 2, modul: 'Master', submodul: '', menu: 'Media', path: '/m_media', endpoint: '/m_media', icon: 'box', sequence: 2.24, description: '', is_active: 1 },
    { id: 3, modul: 'Recruitment', submodul: 'Transaksi', menu: 'Pengajuan Reqruitment', path: '/t_req_recruitment', endpoint: '/t_req_recruitment', icon: 'folder-open', sequence: 3.00, description: '', is_active: 1 },
    { id: 4, modul: 'Laporan', submodul: '', menu: 'Laporan Perjalanan Dinas', path: '/l_perjalanan_dinas', endpoint: '/l_perjalanan_dinas', icon: 'bookmark', sequence: 9100.00, description: '', is_active: 1 },
    { id: 5, modul: 'Laporan', submodul: '', menu: 'Laporan Klaim Rawat Inap', path: '/l_klaim_ranap', endpoint: '/l_klaim_ranap', icon: 'bookmark', sequence: 9.99, description: '', is_active: 1 },
    { id: 6, modul: 'Transaksi', submodul: '', menu: 'Klaim Rawat Inap', path: '/t_klaim_ranap', endpoint: '/t_klaim_ranap', icon: 'stopwatch', sequence: 3.08, description: '', is_active: 1 },
    { id: 7, modul: 'Laporan', submodul: '', menu: 'Laporan Klaim Akses', path: '/l_klaim_askes', endpoint: '/l_klaim_askes', icon: 'bookmark', sequence: 9.99, description: '', is_active: 1 },
    { id: 8, modul: 'Transaksi', submodul: '', menu: 'Surat Peringatan Lama', path: '/t_sgp', endpoint: '/t_sgp', icon: 'triangle-exclamation', sequence: 3.15, description: '', is_active: 0 },
    { id: 9, modul: 'Master', submodul: '', menu: 'Cost Center', path: '/m_cost_center', endpoint: '/m_cost_center', icon: 'clipboard', sequence: 2.13, description: '', is_active: 1 }
];

// GET List All Data (Filter Active/Inactive & Search)
app.get('/api/m_menu', (req, res) => {
    let result = [...masterMenuData];
    
    // Filter by Status
    if (req.query.is_active !== undefined && req.query.is_active !== '') {
        const isActive = req.query.is_active === 'true' || req.query.is_active === '1';
        result = result.filter(item => Boolean(item.is_active) === isActive);
    }
    
    // Search
    if (req.query.search) {
        const q = req.query.search.toLowerCase();
        result = result.filter(item => 
            item.modul.toLowerCase().includes(q) || 
            item.menu.toLowerCase().includes(q) ||
            item.path.toLowerCase().includes(q)
        );
    }

    // Sort by sequence ASC
    result.sort((a, b) => parseFloat(a.sequence) - parseFloat(b.sequence));

    res.json({
        status: 'success',
        code: 200,
        data: result
    });
});

// GET Single Data by ID
app.get('/api/m_menu/:id', (req, res) => {
    const item = masterMenuData.find(d => d.id == req.params.id);
    if (!item) return res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
    res.json({ status: 'success', data: item });
});

// POST Create New
app.post('/api/m_menu', (req, res) => {
    const newItem = {
        id: Date.now(),
        modul: req.body.modul || '',
        submodul: req.body.submodul || '',
        menu: req.body.menu || '',
        path: req.body.path || '',
        endpoint: req.body.endpoint || '',
        icon: req.body.icon || '',
        sequence: parseFloat(req.body.sequence) || 0,
        description: req.body.description || '',
        is_active: req.body.is_active ? 1 : 0
    };
    masterMenuData.push(newItem);
    res.json({ status: 'success', message: 'Menu berhasil ditambahkan', data: newItem });
});

// PUT Update Data
app.put('/api/m_menu/:id', (req, res) => {
    const idx = masterMenuData.findIndex(d => d.id == req.params.id);
    if (idx === -1) return res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
    
    masterMenuData[idx] = {
        ...masterMenuData[idx],
        ...req.body,
        sequence: parseFloat(req.body.sequence) || masterMenuData[idx].sequence,
        is_active: req.body.is_active ? 1 : 0
    };
    res.json({ status: 'success', message: 'Menu berhasil diperbarui', data: masterMenuData[idx] });
});

// DELETE Data
app.delete('/api/m_menu/:id', (req, res) => {
    masterMenuData = masterMenuData.filter(d => d.id != req.params.id);
    res.json({ status: 'success', message: 'Menu berhasil dihapus' });
});