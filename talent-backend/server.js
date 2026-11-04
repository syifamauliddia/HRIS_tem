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

// -------------------------------------------------------------
// Helpers
// -------------------------------------------------------------
function parseJsonArray(value) {
    if (value === null || value === undefined || value === '') return [];
    if (Array.isArray(value)) return value;
    if (typeof value === 'object') return value;
    try {
        const parsed = JSON.parse(value);
        return Array.isArray(parsed) ? parsed : (parsed || []);
    } catch (e) {
        return [];
    }
}

// Keep raw detail_json as a JSON string in responses.
function normalizeJsonString(value) {
    if (value === null || value === undefined) return value;
    if (typeof value === 'string') return value;
    return JSON.stringify(value);
}

function toNumber(value) {
    const n = Number(value);
    return Number.isFinite(n) ? n : 0;
}

// MySQL TIME -> "HH:MM"
function formatTime(value) {
    if (value === null || value === undefined || value === '') return '';
    const s = String(value);
    const m = s.match(/^(\d{1,2}):(\d{2})/);
    if (m) return String(m[1]).padStart(2, '0') + ':' + m[2];
    return s;
}

// Normalize MySQL DATE / JS Date -> "YYYY-MM-DD"
function formatDate(value) {
    if (value === null || value === undefined || value === '') return '';
    if (value instanceof Date && !isNaN(value)) {
        const y = value.getFullYear();
        const m = String(value.getMonth() + 1).padStart(2, '0');
        const d = String(value.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }
    const s = String(value);
    const m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
    return m ? `${m[1]}-${m[2]}-${m[3]}` : s;
}

// Normalize MySQL DATETIME / JS Date -> "YYYY-MM-DD HH:MM:SS"
function formatDateTime(value) {
    if (value === null || value === undefined || value === '') return '';
    if (value instanceof Date && !isNaN(value)) {
        const time = [value.getHours(), value.getMinutes(), value.getSeconds()]
            .map((n) => String(n).padStart(2, '0')).join(':');
        return `${formatDate(value)} ${time}`;
    }
    const s = String(value);
    const m = s.match(/^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})(?::(\d{2}))?/);
    if (m) return `${m[1]}-${m[2]}-${m[3]} ${m[4]}:${m[5]}:${m[6] || '00'}`;
    return formatDate(value);
}

function toBit(value) {
    return (value === true || value === 1 || value === '1' || value === 'true') ? 1 : 0;
}

function nullIfEmpty(value) {
    return (value === '' || value === undefined) ? null : value;
}

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

        if (token_fcm) {
            await pool.query('UPDATE users SET token_fcm = ? WHERE id = ?', [token_fcm, user.id]);
        }

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
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 2. API Employees
// -------------------------------------------------------------
app.get('/api/employees', async (req, res) => {
    try {
        const [rows] = await pool.query(
            `SELECT id, kode, nama, jabatan, divisi, level, atasan_id, sbu, sub, branch, is_active
             FROM employees WHERE is_active = 1 ORDER BY id`
        );
        res.json({ status: 'success', data: rows });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 3. API Master Assessment (Penilaian Karyawan)
// -------------------------------------------------------------
// GET: Ambil daftar list master assessment
app.get('/api/master-assessments', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM master_assessments ORDER BY id ASC');
        const data = rows.map((row) => ({
            ...row,
            indicators: parseJsonArray(row.indicators)
        }));
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// GET: Ambil satu master assessment
app.get('/api/master-assessments/:id', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM master_assessments WHERE id = ?', [req.params.id]);
        if (!rows.length) {
            return res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
        }
        res.json({
            status: 'success',
            data: { ...rows[0], indicators: parseJsonArray(rows[0].indicators) }
        });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// POST: Simpan data form master assessment baru
app.post('/api/master-assessments', async (req, res) => {
    const body = req.body || {};
    const sbu = body.sbu !== undefined ? body.sbu : body.sbu_id;
    const sub = body.sub !== undefined ? body.sub : body.sub_id;
    const branch = body.branch !== undefined ? body.branch : body.branch_id;
    const level = body.level !== undefined ? body.level : body.level_id;
    const divisi = body.divisi !== undefined ? body.divisi : body.division_id;
    const indicators = Array.isArray(body.indicators) ? body.indicators : parseJsonArray(body.indicators);

    try {
        const [result] = await pool.query(
            `INSERT INTO master_assessments (sbu, sub, branch, level, divisi, tipe_penilaian, deskripsi, status, indicators)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
            [
                nullIfEmpty(sbu), nullIfEmpty(sub), nullIfEmpty(branch), nullIfEmpty(level),
                nullIfEmpty(divisi), nullIfEmpty(body.tipe_penilaian), nullIfEmpty(body.deskripsi),
                body.status || 'Active', JSON.stringify(indicators)
            ]
        );
        res.json({ status: 'success', message: 'Data master assessment berhasil disimpan', id: result.insertId });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// PUT: Update master assessment
app.put('/api/master-assessments/:id', async (req, res) => {
    const body = req.body || {};
    const sbu = body.sbu !== undefined ? body.sbu : body.sbu_id;
    const sub = body.sub !== undefined ? body.sub : body.sub_id;
    const branch = body.branch !== undefined ? body.branch : body.branch_id;
    const level = body.level !== undefined ? body.level : body.level_id;
    const divisi = body.divisi !== undefined ? body.divisi : body.division_id;
    const indicators = Array.isArray(body.indicators) ? body.indicators : parseJsonArray(body.indicators);

    try {
        const [result] = await pool.query(
            `UPDATE master_assessments
             SET sbu = ?, sub = ?, branch = ?, level = ?, divisi = ?, tipe_penilaian = ?, deskripsi = ?, status = ?, indicators = ?
             WHERE id = ?`,
            [
                nullIfEmpty(sbu), nullIfEmpty(sub), nullIfEmpty(branch), nullIfEmpty(level),
                nullIfEmpty(divisi), nullIfEmpty(body.tipe_penilaian), nullIfEmpty(body.deskripsi),
                body.status || 'Active', JSON.stringify(indicators), req.params.id
            ]
        );
        if (!result.affectedRows) {
            return res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
        }
        res.json({ status: 'success', message: 'Data master assessment berhasil diperbarui' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// DELETE: Hapus master assessment
app.delete('/api/master-assessments/:id', async (req, res) => {
    try {
        await pool.query('DELETE FROM master_assessments WHERE id = ?', [req.params.id]);
        res.json({ status: 'success', message: 'Data master assessment berhasil dihapus' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 4. API Transaksi Penilaian 360
// -------------------------------------------------------------
// GET: Ambil daftar list transaksi 360
app.get('/api/transaction-360', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM transaction_360 ORDER BY id DESC');
        const data = rows.map((row) => ({
            ...row,
            tanggal: formatDate(row.tanggal),
            created_at: formatDateTime(row.created_at),
            updated_at: formatDateTime(row.updated_at),
            detail_json: normalizeJsonString(row.detail_json)
        }));
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 5. API Dashboard
// -------------------------------------------------------------
app.get('/api/dashboard', async (req, res) => {
    try {
        const [[subCount]] = await pool.query('SELECT COUNT(*) AS c FROM subcompanies WHERE is_active = 1');
        const [[branchCount]] = await pool.query('SELECT COUNT(*) AS c FROM branches WHERE is_active = 1');
        const [[employeeCount]] = await pool.query('SELECT COUNT(*) AS c FROM employees WHERE is_active = 1');
        const [[presentCount]] = await pool.query(
            "SELECT COUNT(*) AS c FROM presensi_absensi WHERE tanggal = CURDATE() AND UPPER(status) = 'ATTEND'"
        );
        const [branchSalary] = await pool.query(
            'SELECT branch_name, total_gaji FROM branches ORDER BY id ASC'
        );
        const [subcompSalary] = await pool.query(
            'SELECT subcomp_name, total_gaji FROM subcompanies ORDER BY id ASC'
        );

        res.json({
            sub_count: toNumber(subCount.c),
            branch_count: toNumber(branchCount.c),
            total_absen: toNumber(employeeCount.c),
            total_hadir: toNumber(presentCount.c),
            branch_salary: branchSalary.map((r) => ({
                branch_name: r.branch_name,
                total_gaji: toNumber(r.total_gaji)
            })),
            subcomp_salary: subcompSalary.map((r) => ({
                subcomp_name: r.subcomp_name,
                total_gaji: toNumber(r.total_gaji)
            }))
        });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 6. API Akses Responsibility
// -------------------------------------------------------------
// GET: Ambil daftar akses responsibility
app.get('/api/akses-respo', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM akses_respo ORDER BY id ASC');
        res.json({ status: 'success', data: rows });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// POST: Ubah akses responsibility
app.post('/api/akses-respo/change', async (req, res) => {
    const { m_respo_id } = req.body || {};
    try {
        const [rows] = await pool.query('SELECT * FROM akses_respo WHERE id = ?', [m_respo_id]);
        if (!rows.length) {
            return res.status(404).json({ status: 'error', message: 'Data akses tidak ditemukan' });
        }
        const row = rows[0];
        res.json({
            status: 'success',
            message: 'Akses berhasil diubah',
            data: {
                ...row,
                role_aktif: row.name,
                m_branch_id: null,
                m_subcomp_id: null
            }
        });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 7. API Notification (Outstanding Approval)
// -------------------------------------------------------------
app.get('/api/notification/outstanding', async (req, res) => {
    try {
        const conditions = [];
        const params = [];

        if (req.query.search) {
            const q = '%' + req.query.search + '%';
            conditions.push('(trx_name LIKE ? OR nomor LIKE ? OR trx_nomor LIKE ? OR creator LIKE ?)');
            params.push(q, q, q, q);
        }
        if (req.query.m_branch_id !== undefined && req.query.m_branch_id !== '') {
            conditions.push('m_branch_id = ?');
            params.push(req.query.m_branch_id);
        }
        if (req.query.m_subcomp_id !== undefined && req.query.m_subcomp_id !== '') {
            conditions.push('m_subcomp_id = ?');
            params.push(req.query.m_subcomp_id);
        }

        let limit = parseInt(req.query.limit, 10);
        if (!Number.isFinite(limit) || limit <= 0) limit = 20;
        limit = Math.min(limit, 500);

        const where = conditions.length ? ' WHERE ' + conditions.join(' AND ') : '';
        const [rows] = await pool.query(
            `SELECT * FROM outstanding_approvals${where} ORDER BY created_at DESC LIMIT ${limit}`,
            params
        );
        const data = rows.map((row) => ({
            ...row,
            trx_date: formatDate(row.trx_date),
            created_at: formatDateTime(row.created_at),
            updated_at: formatDateTime(row.updated_at)
        }));
        res.json({ status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 8. API Transaksi Absensi Karyawan (Presensi Absensi)
// -------------------------------------------------------------

// GET: Rekapitulasi Absensi Harian Per Bulan
app.get('/api/presensi_absensi/get_by_daily', async (req, res) => {
    const { month } = req.query;
    const selectedMonth = (month && /^\d{4}-\d{2}$/.test(month))
        ? month
        : new Date().toISOString().slice(0, 7);

    try {
        const [yearStr, monthStr] = selectedMonth.split('-');
        const year = parseInt(yearStr, 10);
        const monthIndex = parseInt(monthStr, 10); // 1-12
        const totalDaysInMonth = new Date(year, monthIndex, 0).getDate();
        const firstDate = `${selectedMonth}-01`;
        const lastDate = `${selectedMonth}-${String(totalDaysInMonth).padStart(2, '0')}`;

        const [[employeeCount]] = await pool.query(
            'SELECT COUNT(*) AS c FROM employees WHERE is_active = 1'
        );
        const totalKary = toNumber(employeeCount.c);

        const [aggregates] = await pool.query(
            `SELECT DAY(p.tanggal) AS d,
                    SUM(UPPER(p.status) = 'ATTEND') AS attend,
                    SUM(UPPER(p.status) = 'CUTI') AS cuti,
                    SUM(UPPER(p.status) = 'ALPHA') AS alpha
             FROM presensi_absensi p
             INNER JOIN employees e ON e.id = p.default_user_id AND e.is_active = 1
             WHERE p.tanggal BETWEEN ? AND ?
             GROUP BY DAY(p.tanggal)`,
            [firstDate, lastDate]
        );

        const byDay = {};
        aggregates.forEach((a) => {
            byDay[Number(a.d)] = {
                attend: toNumber(a.attend),
                cuti: toNumber(a.cuti),
                alpha: toNumber(a.alpha)
            };
        });

        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const daysData = [];

        for (let day = 1; day <= totalDaysInMonth; day++) {
            const dateStr = `${selectedMonth}-${String(day).padStart(2, '0')}`;
            const dow = new Date(year, monthIndex - 1, day).getDay();
            const isWeekend = dow === 0 || dow === 6;

            if (isWeekend) {
                daysData.push({
                    day_name_idn: dayNames[dow],
                    all_days_of_month: dateStr,
                    attend: 0,
                    cuti: 0,
                    alpha: 0,
                    total_kary: 0,
                    presentase: 0,
                    type: 'Hari Libur'
                });
            } else {
                const agg = byDay[day] || { attend: 0, cuti: 0, alpha: 0 };
                const presentase = totalKary ? Number(((agg.attend / totalKary) * 100).toFixed(2)) : 0;
                daysData.push({
                    day_name_idn: dayNames[dow],
                    all_days_of_month: dateStr,
                    attend: agg.attend,
                    cuti: agg.cuti,
                    alpha: agg.alpha,
                    total_kary: totalKary,
                    presentase: presentase,
                    type: 'Hari Kerja'
                });
            }
        }

        res.json({ code: 200, status: 'success', data: daysData });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// GET: Detail Karyawan Pada Tanggal Tertentu
app.get('/api/presensi_absensi/get_by_date', async (req, res) => {
    const date = req.query.date || new Date().toISOString().slice(0, 10);

    try {
        const [rows] = await pool.query(
            `SELECT e.id AS employee_id, e.kode, e.nama, e.divisi, e.jam_kerja, e.shift,
                    p.id AS presensi_absensi_id, p.tanggal, p.status AS p_status,
                    p.checkin_time, p.checkin_on_scope, p.checkin_foto, p.checkin_region,
                    p.checkin_lat, p.checkin_long, p.checkin_address, p.catatan_in,
                    p.checkout_time, p.checkout_on_scope, p.checkout_foto, p.checkout_region,
                    p.checkout_lat, p.checkout_long, p.checkout_address, p.catatan_out
             FROM employees e
             LEFT JOIN presensi_absensi p
                ON p.default_user_id = e.id AND p.tanggal = ?
             WHERE e.is_active = 1
             ORDER BY e.id`,
            [date]
        );

        const data = rows.map((r) => {
            const hasPresensi = r.presensi_absensi_id !== null && r.presensi_absensi_id !== undefined;

            if (!hasPresensi) {
                return {
                    presensi_absensi_id: null,
                    default_user_id: r.employee_id,
                    kode: r.kode,
                    nama_lengkap: r.nama,
                    dept: r.divisi,
                    jam_kerja: r.jam_kerja,
                    desc: r.shift,
                    absensi: {
                        presensi_absensi_id: null,
                        tanggal: date,
                        status: 'NOT ATTEND',
                        checkin_time: '',
                        checkin_on_scope: false,
                        checkin_foto: '',
                        checkin_region: '',
                        checkin_address: '',
                        checkin_lat: '',
                        checkin_long: '',
                        catatan_in: '',
                        checkout_time: '',
                        checkout_on_scope: false,
                        checkout_foto: '',
                        checkout_region: '',
                        checkout_address: '',
                        checkout_lat: '',
                        checkout_long: '',
                        catatan_out: ''
                    }
                };
            }

            return {
                presensi_absensi_id: r.presensi_absensi_id,
                default_user_id: r.employee_id,
                kode: r.kode,
                nama_lengkap: r.nama,
                dept: r.divisi,
                jam_kerja: r.jam_kerja,
                desc: r.shift,
                absensi: {
                    presensi_absensi_id: r.presensi_absensi_id,
                    tanggal: formatDate(r.tanggal),
                    status: r.p_status,
                    checkin_time: formatTime(r.checkin_time),
                    checkin_on_scope: Boolean(toNumber(r.checkin_on_scope)),
                    checkin_foto: r.checkin_foto || '',
                    checkin_region: r.checkin_region || '',
                    checkin_address: r.checkin_address || '',
                    checkin_lat: r.checkin_lat || '',
                    checkin_long: r.checkin_long || '',
                    catatan_in: r.catatan_in || '',
                    checkout_time: formatTime(r.checkout_time),
                    checkout_on_scope: Boolean(toNumber(r.checkout_on_scope)),
                    checkout_foto: r.checkout_foto || '',
                    checkout_region: r.checkout_region || '',
                    checkout_address: r.checkout_address || '',
                    checkout_lat: r.checkout_lat || '',
                    checkout_long: r.checkout_long || '',
                    catatan_out: r.catatan_out || ''
                }
            };
        });

        res.json({ code: 200, status: 'success', data });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// GET: Ambil Detail Single Record Absensi (Untuk Edit)
app.get('/api/presensi_absensi/:id', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM presensi_absensi WHERE id = ?', [req.params.id]);
        if (!rows.length) {
            return res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
        }
        const row = rows[0];
        res.json({
            status: 'success',
            data: {
                ...row,
                tanggal: formatDate(row.tanggal),
                checkin_time: formatTime(row.checkin_time),
                checkout_time: formatTime(row.checkout_time)
            }
        });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

const presensiFields = [
    'default_user_id', 'tanggal', 'status', 'shift',
    'checkin_time', 'checkin_foto', 'checkin_region', 'checkin_lat', 'checkin_long',
    'checkin_address', 'catatan_in', 'checkin_on_scope',
    'checkout_time', 'checkout_foto', 'checkout_region', 'checkout_lat', 'checkout_long',
    'checkout_address', 'catatan_out', 'checkout_on_scope'
];

function presensiValues(body) {
    const b = body || {};
    return [
        nullIfEmpty(b.default_user_id),
        nullIfEmpty(b.tanggal),
        b.status || 'NOT ATTEND',
        nullIfEmpty(b.shift),
        nullIfEmpty(b.checkin_time),
        nullIfEmpty(b.checkin_foto),
        nullIfEmpty(b.checkin_region),
        nullIfEmpty(b.checkin_lat),
        nullIfEmpty(b.checkin_long),
        nullIfEmpty(b.checkin_address),
        nullIfEmpty(b.catatan_in),
        toBit(b.checkin_on_scope),
        nullIfEmpty(b.checkout_time),
        nullIfEmpty(b.checkout_foto),
        nullIfEmpty(b.checkout_region),
        nullIfEmpty(b.checkout_lat),
        nullIfEmpty(b.checkout_long),
        nullIfEmpty(b.checkout_address),
        nullIfEmpty(b.catatan_out),
        toBit(b.checkout_on_scope)
    ];
}

// POST: Simpan Absensi (upsert per default_user_id + tanggal)
app.post('/api/presensi_absensi', async (req, res) => {
    try {
        const updateCols = presensiFields
            .filter((c) => c !== 'default_user_id' && c !== 'tanggal')
            .map((c) => `${c} = VALUES(${c})`)
            .join(', ');

        await pool.query(
            `INSERT INTO presensi_absensi (${presensiFields.join(', ')}, created_at, updated_at)
             VALUES (${presensiFields.map(() => '?').join(', ')}, NOW(), NOW())
             ON DUPLICATE KEY UPDATE ${updateCols}, updated_at = NOW()`,
            presensiValues(req.body)
        );
        res.json({ status: 'success', message: 'Data absensi berhasil disimpan' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// PUT: Update Absensi
app.put('/api/presensi_absensi/:id', async (req, res) => {
    try {
        const sets = presensiFields.map((c) => `${c} = ?`).join(', ');
        const [result] = await pool.query(
            `UPDATE presensi_absensi SET ${sets}, updated_at = NOW() WHERE id = ?`,
            [...presensiValues(req.body), req.params.id]
        );
        if (!result.affectedRows) {
            return res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
        }
        res.json({ status: 'success', message: 'Data absensi berhasil diperbarui' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// DELETE: Hapus Absensi
app.delete('/api/presensi_absensi/:id', async (req, res) => {
    try {
        await pool.query('DELETE FROM presensi_absensi WHERE id = ?', [req.params.id]);
        res.json({ status: 'success', message: 'Data absensi berhasil dikosongkan' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 9. API Master Menu (m_menu)
// -------------------------------------------------------------
// GET List All Data (Filter Active/Inactive & Search)
app.get('/api/m_menu', async (req, res) => {
    try {
        const conditions = [];
        const params = [];

        if (req.query.is_active !== undefined && req.query.is_active !== '') {
            const isActive = req.query.is_active === 'true' || req.query.is_active === '1';
            conditions.push('is_active = ?');
            params.push(isActive ? 1 : 0);
        }
        if (req.query.search) {
            const q = '%' + req.query.search + '%';
            conditions.push('(modul LIKE ? OR menu LIKE ? OR path LIKE ?)');
            params.push(q, q, q);
        }

        const where = conditions.length ? ' WHERE ' + conditions.join(' AND ') : '';
        const [rows] = await pool.query(
            `SELECT * FROM m_menu${where} ORDER BY sequence ASC`,
            params
        );
        res.json({ status: 'success', code: 200, data: rows });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// GET Single Data by ID
app.get('/api/m_menu/:id', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM m_menu WHERE id = ?', [req.params.id]);
        if (!rows.length) {
            return res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
        }
        res.json({ status: 'success', data: rows[0] });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// POST Create New
app.post('/api/m_menu', async (req, res) => {
    const b = req.body || {};
    try {
        const [result] = await pool.query(
            `INSERT INTO m_menu (modul, submodul, menu, path, endpoint, icon, sequence, description, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())`,
            [
                b.modul || '', b.submodul || '', b.menu || '', b.path || '', b.endpoint || '',
                b.icon || '', parseFloat(b.sequence) || 0, b.description || '',
                b.is_active ? 1 : 0
            ]
        );
        const [rows] = await pool.query('SELECT * FROM m_menu WHERE id = ?', [result.insertId]);
        res.json({ status: 'success', message: 'Menu berhasil ditambahkan', data: rows[0] });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// PUT Update Data
app.put('/api/m_menu/:id', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM m_menu WHERE id = ?', [req.params.id]);
        if (!rows.length) {
            return res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
        }
        const cur = rows[0];
        const b = req.body || {};
        const merged = {
            modul: b.modul !== undefined ? b.modul : cur.modul,
            submodul: b.submodul !== undefined ? b.submodul : cur.submodul,
            menu: b.menu !== undefined ? b.menu : cur.menu,
            path: b.path !== undefined ? b.path : cur.path,
            endpoint: b.endpoint !== undefined ? b.endpoint : cur.endpoint,
            icon: b.icon !== undefined ? b.icon : cur.icon,
            sequence: b.sequence !== undefined ? (parseFloat(b.sequence) || 0) : cur.sequence,
            description: b.description !== undefined ? b.description : cur.description,
            is_active: b.is_active !== undefined ? (b.is_active ? 1 : 0) : cur.is_active
        };

        await pool.query(
            `UPDATE m_menu SET modul = ?, submodul = ?, menu = ?, path = ?, endpoint = ?, icon = ?,
                    sequence = ?, description = ?, is_active = ?, updated_at = NOW()
             WHERE id = ?`,
            [
                merged.modul, merged.submodul, merged.menu, merged.path, merged.endpoint,
                merged.icon, merged.sequence, merged.description, merged.is_active, req.params.id
            ]
        );
        const [updated] = await pool.query('SELECT * FROM m_menu WHERE id = ?', [req.params.id]);
        res.json({ status: 'success', message: 'Menu berhasil diperbarui', data: updated[0] });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// DELETE Data
app.delete('/api/m_menu/:id', async (req, res) => {
    try {
        await pool.query('DELETE FROM m_menu WHERE id = ?', [req.params.id]);
        res.json({ status: 'success', message: 'Menu berhasil dihapus' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 10. API Fingerprints
// -------------------------------------------------------------
// GET List (Filter Active/Inactive & Search)
app.get('/api/fingerprint', async (req, res) => {
    try {
        const conditions = [];
        const params = [];

        if (req.query.is_active !== undefined && req.query.is_active !== '') {
            const isActive = req.query.is_active === 'true' || req.query.is_active === '1';
            conditions.push('is_active = ?');
            params.push(isActive ? 1 : 0);
        }
        if (req.query.search) {
            const q = '%' + req.query.search + '%';
            conditions.push('(kode LIKE ? OR nama LIKE ? OR id_machine LIKE ?)');
            params.push(q, q, q);
        }

        const where = conditions.length ? ' WHERE ' + conditions.join(' AND ') : '';
        const [rows] = await pool.query(
            `SELECT * FROM fingerprints${where} ORDER BY id ASC`,
            params
        );
        res.json({ status: 'success', data: rows });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// GET Single
app.get('/api/fingerprint/:id', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM fingerprints WHERE id = ?', [req.params.id]);
        if (!rows.length) {
            return res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
        }
        res.json({ status: 'success', data: rows[0] });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// POST Create
app.post('/api/fingerprint', async (req, res) => {
    const b = req.body || {};
    try {
        const [result] = await pool.query(
            `INSERT INTO fingerprints (kode, nama, id_machine, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, NOW(), NOW())`,
            [b.kode || '', b.nama || '', b.id_machine || '', b.is_active ? 1 : 0]
        );
        const [rows] = await pool.query('SELECT * FROM fingerprints WHERE id = ?', [result.insertId]);
        res.json({ status: 'success', message: 'Fingerprint berhasil ditambahkan', data: rows[0] });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// PUT Update
app.put('/api/fingerprint/:id', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM fingerprints WHERE id = ?', [req.params.id]);
        if (!rows.length) {
            return res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
        }
        const cur = rows[0];
        const b = req.body || {};
        const merged = {
            kode: b.kode !== undefined ? b.kode : cur.kode,
            nama: b.nama !== undefined ? b.nama : cur.nama,
            id_machine: b.id_machine !== undefined ? b.id_machine : cur.id_machine,
            is_active: b.is_active !== undefined ? (b.is_active ? 1 : 0) : cur.is_active
        };

        await pool.query(
            `UPDATE fingerprints SET kode = ?, nama = ?, id_machine = ?, is_active = ?, updated_at = NOW()
             WHERE id = ?`,
            [merged.kode, merged.nama, merged.id_machine, merged.is_active, req.params.id]
        );
        const [updated] = await pool.query('SELECT * FROM fingerprints WHERE id = ?', [req.params.id]);
        res.json({ status: 'success', message: 'Fingerprint berhasil diperbarui', data: updated[0] });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// DELETE
app.delete('/api/fingerprint/:id', async (req, res) => {
    try {
        await pool.query('DELETE FROM fingerprints WHERE id = ?', [req.params.id]);
        res.json({ status: 'success', message: 'Fingerprint berhasil dihapus' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 11. API Penilaian Summary (from transaction_360.detail_json)
// -------------------------------------------------------------
app.get('/api/penilaian-summary', async (req, res) => {
    try {
        const [rows] = await pool.query(
            `SELECT t.id, t.employee_id, t.nama, t.nama_jabatan, t.nama_divisi, t.nama_level,
                    t.total_nilai, t.penilai_id, t.hubungan_penilai, t.penilaian, t.detail_json,
                    COALESCE(ev.nama, t.penilaian) AS evaluator_nama
             FROM transaction_360 t
             LEFT JOIN employees ev ON ev.id = t.penilai_id
             WHERE t.detail_json IS NOT NULL AND CHAR_LENGTH(t.detail_json) > 2
             ORDER BY t.nama ASC, t.id ASC`
        );

        const groups = [];
        const byName = {};

        rows.forEach((row, idx) => {
            const namaKaryawan = row.nama || 'Tanpa Nama';
            if (!byName[namaKaryawan]) {
                byName[namaKaryawan] = {
                    id: row.employee_id !== null && row.employee_id !== undefined ? row.employee_id : idx + 1,
                    nama_karyawan: namaKaryawan,
                    jabatan: row.nama_jabatan || '',
                    divisi: row.nama_divisi || '',
                    level: row.nama_level || '',
                    evaluators: []
                };
                groups.push(byName[namaKaryawan]);
            }

            const komponenRaw = parseJsonArray(row.detail_json);
            const komponen = (Array.isArray(komponenRaw) ? komponenRaw : []).map((k) => {
                const optionsRaw = Array.isArray(k.options) ? k.options : [];
                const isObjectOptions = optionsRaw.length > 0 && typeof optionsRaw[0] === 'object';
                const pilihan = optionsRaw.map((o) => (o && typeof o === 'object') ? (o.text !== undefined ? o.text : String(o.nilai)) : String(o));

                let selectedIndex = -1;
                if (isObjectOptions) {
                    selectedIndex = optionsRaw.findIndex((o) => Number(o.nilai) === Number(k.jawaban));
                }
                if (selectedIndex === -1) {
                    selectedIndex = pilihan.indexOf(String(k.jawaban));
                }

                return {
                    pertanyaan: k.pertanyaan || '',
                    bobot: toNumber(k.bobot),
                    rating: toNumber(k.jawaban),
                    nilai: toNumber(k.total_nilai),
                    pilihan: pilihan,
                    selected_index: selectedIndex
                };
            });

            const idEv = row.penilai_id !== null && row.penilai_id !== undefined ? row.penilai_id : row.id;
            groups[groups.length - 1].evaluators.push({
                id_ev: idEv,
                nama: row.evaluator_nama || ('Penilai #' + idEv),
                hubungan: row.hubungan_penilai || '',
                skor_rata: toNumber(row.total_nilai),
                komponen: komponen
            });
        });

        res.json({ status: 'success', data: groups });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

// -------------------------------------------------------------
// 12. Transaksi 360 : detail, simpan, ubah, hapus, post
// -------------------------------------------------------------
const t360Fields = ['employee_id', 'penilai_id', 'master_assessment_id', 'periode', 'hubungan_penilai', 'tanggal', 'total_nilai',
    'nama', 'nama_jabatan', 'nama_divisi', 'nama_level', 'tipe_penilaian', 'penilaian',
    'catatan_1', 'catatan_2', 'catatan_3', 'catatan_4'];

function t360DetailJson(details) {
    if (typeof details === 'string') {
        try {
            return JSON.stringify(JSON.parse(details));
        } catch (e) {
            return '[]';
        }
    }
    return JSON.stringify(Array.isArray(details) ? details : []);
}

const t360Values = (b) => {
    const body = b || {};
    const values = t360Fields.map((f) => {
        if (f === 'total_nilai') {
            return (body[f] === '' || body[f] === undefined || body[f] === null) ? 0 : body[f];
        }
        return (body[f] === '' || body[f] === undefined) ? null : body[f];
    });
    values.push(t360DetailJson(body.details));
    return values;
};

app.get('/api/transaction-360/:id', async (req, res) => {
    try {
        const [rows] = await pool.query('SELECT * FROM transaction_360 WHERE id = ?', [req.params.id]);
        if (!rows.length) return res.status(404).json({ status: 'error', message: 'Data tidak ditemukan' });
        res.json({
            status: 'success',
            data: {
                ...rows[0],
                tanggal: formatDate(rows[0].tanggal),
                created_at: formatDateTime(rows[0].created_at),
                updated_at: formatDateTime(rows[0].updated_at),
                detail_json: normalizeJsonString(rows[0].detail_json)
            }
        });
    } catch (e) { res.status(500).json({ status: 'error', message: e.message }); }
});

app.post('/api/transaction-360', async (req, res) => {
    try {
        const cols = [...t360Fields, 'detail_json'];
        const [r] = await pool.query(
            `INSERT INTO transaction_360 (${cols.join(',')}) VALUES (${cols.map(() => '?').join(',')})`,
            t360Values(req.body)
        );
        res.json({ status: 'success', message: 'Data Penilaian 360 berhasil disimpan', id: r.insertId });
    } catch (e) { res.status(500).json({ status: 'error', message: e.message }); }
});

app.put('/api/transaction-360/:id', async (req, res) => {
    try {
        const sets = [...t360Fields, 'detail_json'].map(c => `${c} = ?`).join(',');
        const [r] = await pool.query(
            `UPDATE transaction_360 SET ${sets} WHERE id = ? AND status = 'DRAFT'`,
            [...t360Values(req.body), req.params.id]
        );
        if (!r.affectedRows) return res.status(400).json({ status: 'error', message: 'Data tidak ditemukan atau sudah POSTED' });
        res.json({ status: 'success', message: 'Data Penilaian 360 berhasil diperbarui' });
    } catch (e) { res.status(500).json({ status: 'error', message: e.message }); }
});

app.delete('/api/transaction-360/:id', async (req, res) => {
    try {
        const [r] = await pool.query(`DELETE FROM transaction_360 WHERE id = ? AND status = 'DRAFT'`, [req.params.id]);
        if (!r.affectedRows) return res.status(400).json({ status: 'error', message: 'Data tidak ditemukan atau sudah POSTED' });
        res.json({ status: 'success', message: 'Data berhasil dihapus' });
    } catch (e) { res.status(500).json({ status: 'error', message: e.message }); }
});

app.post('/api/transaction-360/post', async (req, res) => {
    try {
        const [r] = await pool.query(`UPDATE transaction_360 SET status = 'POSTED' WHERE id = ? AND status = 'DRAFT'`, [req.body.id]);
        if (!r.affectedRows) return res.status(400).json({ status: 'error', message: 'Data tidak ditemukan atau sudah POSTED' });
        res.json({ status: 'success', message: 'Data berhasil di-post' });
    } catch (e) { res.status(500).json({ status: 'error', message: e.message }); }
});

// -------------------------------------------------------------
// Startup
// -------------------------------------------------------------
pool.query('SELECT 1')
    .then(() => console.log('Database talent_db connected'))
    .catch((err) => console.error('Database connection failed:', err.message));

app.listen(3000, () => console.log('Backend JS running on port 3000'));
