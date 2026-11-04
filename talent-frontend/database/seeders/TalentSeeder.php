<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TalentSeeder extends Seeder
{
    /**
     * Seed talent_db with the same dummy data as
     * talent-backend/database/seed_talent_db.sql.
     *
     * Re-runnable: deletes seeded rows before inserting.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // users is an existing Laravel table: add columns if missing.
        DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS username VARCHAR(100) NULL UNIQUE");
        DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(50) NOT NULL DEFAULT 'Staff'");
        DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS token_fcm VARCHAR(255) NULL");

        // ---------------------------------------------------------
        // Clear seeded data
        // ---------------------------------------------------------
        DB::table('presensi_absensi')->delete();
        DB::table('transaction_360')->delete();
        DB::table('master_assessments')->delete();
        DB::table('employees')->delete();
        DB::table('m_menu')->delete();
        DB::table('fingerprints')->delete();
        DB::table('akses_respo')->delete();
        DB::table('outstanding_approvals')->delete();
        DB::table('branches')->delete();
        DB::table('subcompanies')->delete();

        $usernames = ['syifa', 'budi', 'julyandra', 'khabibi', 'dewi', 'agus', 'nadia', 'fajar', 'aru', 'rina', 'andi', 'admin'];
        DB::table('users')->whereIn('username', $usernames)->delete();
        DB::table('users')->whereIn('email', array_map(function ($u) {
            return $u . '@talent.local';
        }, $usernames))->delete();

        $now = now();

        // ---------------------------------------------------------
        // Users (plain text passwords, server.js compares plain text)
        // ---------------------------------------------------------
        $users = [
            ['username' => 'syifa', 'name' => 'Syifa Mauliddia', 'role' => 'Manager'],
            ['username' => 'budi', 'name' => 'Budi Hartono', 'role' => 'Direktur Holding'],
            ['username' => 'julyandra', 'name' => 'Julyandra Andi Santoso', 'role' => 'Staff'],
            ['username' => 'khabibi', 'name' => 'Khabibi Al Munif', 'role' => 'Staff'],
            ['username' => 'dewi', 'name' => 'Dewi Lestari', 'role' => 'Staff'],
            ['username' => 'agus', 'name' => 'Agus Prasetyo', 'role' => 'Manager'],
            ['username' => 'nadia', 'name' => 'Nadia Putri', 'role' => 'Staff'],
            ['username' => 'fajar', 'name' => 'Fajar Ramadhan', 'role' => 'Manager'],
            ['username' => 'aru', 'name' => 'ARU TEST', 'role' => 'Staff'],
            ['username' => 'rina', 'name' => 'Rina Wulandari', 'role' => 'Staff'],
            ['username' => 'andi', 'name' => 'Andi Pratama', 'role' => 'Staff'],
            ['username' => 'admin', 'name' => 'Admin', 'role' => 'Administrator'],
        ];

        $userRows = [];
        foreach ($users as $u) {
            $userRows[] = [
                'username' => $u['username'],
                'name' => $u['name'],
                'email' => $u['username'] . '@talent.local',
                'password' => $u['username'] === 'admin' ? 'admin123' : 'password123',
                'role' => $u['role'],
                'token_fcm' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('users')->insert($userRows);

        // ---------------------------------------------------------
        // Employees
        // ---------------------------------------------------------
        $employees = [
            ['id' => 1, 'kode' => 'EMP-001', 'nama' => 'Syifa Mauliddia', 'jabatan' => 'Manager IT', 'divisi' => 'Information Technology', 'level' => 'Manager', 'atasan_id' => 2],
            ['id' => 2, 'kode' => 'EMP-002', 'nama' => 'Budi Hartono', 'jabatan' => 'Direktur Holding', 'divisi' => 'Information Technology', 'level' => 'Direktur Holding', 'atasan_id' => null],
            ['id' => 3, 'kode' => 'EMP-003', 'nama' => 'Julyandra Andi Santoso', 'jabatan' => 'Software Engineer', 'divisi' => 'Information Technology', 'level' => 'Staff', 'atasan_id' => 1],
            ['id' => 4, 'kode' => 'EMP-004', 'nama' => 'Khabibi Al Munif', 'jabatan' => 'IT Support', 'divisi' => 'Information Technology', 'level' => 'Staff', 'atasan_id' => 1],
            ['id' => 5, 'kode' => 'EMP-005', 'nama' => 'Dewi Lestari', 'jabatan' => 'HR Staff', 'divisi' => 'HRD', 'level' => 'Staff', 'atasan_id' => 6],
            ['id' => 6, 'kode' => 'EMP-006', 'nama' => 'Agus Prasetyo', 'jabatan' => 'HR Manager', 'divisi' => 'HRD', 'level' => 'Manager', 'atasan_id' => 2],
            ['id' => 7, 'kode' => 'EMP-007', 'nama' => 'Nadia Putri', 'jabatan' => 'Staff Finance', 'divisi' => 'Finance', 'level' => 'Staff', 'atasan_id' => 8],
            ['id' => 8, 'kode' => 'EMP-008', 'nama' => 'Fajar Ramadhan', 'jabatan' => 'Finance Manager', 'divisi' => 'Finance', 'level' => 'Manager', 'atasan_id' => 2],
            ['id' => 9, 'kode' => 'EMP-009', 'nama' => 'ARU TEST', 'jabatan' => 'Staff', 'divisi' => 'Information Technology', 'level' => 'Staff', 'atasan_id' => 1],
            ['id' => 10, 'kode' => 'EMP-010', 'nama' => 'Rina Wulandari', 'jabatan' => 'Marketing Staff', 'divisi' => 'Marketing', 'level' => 'Staff', 'atasan_id' => null],
            ['id' => 11, 'kode' => 'EMP-011', 'nama' => 'Andi Pratama', 'jabatan' => 'Data Analyst', 'divisi' => 'Business Intelligence', 'level' => 'Staff', 'atasan_id' => null],
        ];

        $employeeRows = [];
        foreach ($employees as $e) {
            $employeeRows[] = $e + [
                'sbu' => 'HOLDING',
                'sub' => 'HOLDING',
                'branch' => 'HOLDING',
                'jam_kerja' => '08:00 - 17:00',
                'shift' => 'Reguler Pagi',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('employees')->insert($employeeRows);

        // ---------------------------------------------------------
        // Master assessments (indicators JSON)
        // ---------------------------------------------------------
        $questionBank = [
            [
                'n' => 'Bagaimana penilaian Anda terhadap individu dalam penetapan target kerja yang tinggi?',
                'b' => 3,
                'd' => ['Selalu menetapkan target kerja yang sangat tinggi dan menantang untuk dirinya', 'Sering menetapkan target kerja yang tinggi untuk dirinya', 'Menetapkan target kerja yang cukup sesuai tuntutan pekerjaan', 'Kurang menetapkan target kerja yang tinggi, hanya mengikuti target minimum', 'Tidak pernah menetapkan target kerja sendiri'],
            ],
            [
                'n' => 'Bagaimana kemampuan individu dalam bekerja sama dengan tim?',
                'b' => 2,
                'd' => ['Selalu aktif membangun kerja sama dan membantu rekan tim', 'Sering bekerja sama dengan baik bersama tim', 'Dapat bekerja sama jika diperlukan', 'Kurang aktif dalam kerja sama tim', 'Sulit bekerja sama dengan tim'],
            ],
            [
                'n' => 'Bagaimana individu berkomunikasi dengan atasan, rekan, dan bawahan?',
                'b' => 2,
                'd' => ['Selalu berkomunikasi dengan jelas, terbuka, dan tepat waktu', 'Sering berkomunikasi dengan jelas dan terbuka', 'Komunikasi cukup baik untuk kebutuhan pekerjaan', 'Kurang jelas dalam menyampaikan informasi', 'Sulit berkomunikasi dan sering menimbulkan salah paham'],
            ],
            [
                'n' => 'Bagaimana individu menyelesaikan masalah dan mengambil keputusan?',
                'b' => 3,
                'd' => ['Selalu menyelesaikan masalah dengan cepat dan keputusan tepat', 'Sering mengambil keputusan yang baik', 'Mampu menyelesaikan masalah umum dengan arahan', 'Kurang tegas dan sering menunda keputusan', 'Tidak mampu menyelesaikan masalah secara mandiri'],
            ],
        ];

        $makeIndicators = function (array $indexes) use ($questionBank) {
            $out = [];
            foreach ($indexes as $i) {
                $q = $questionBank[$i];
                $details = [];
                foreach ($q['d'] as $k => $keterangan) {
                    $details[] = ['nilai' => 5 - $k, 'keterangan' => $keterangan];
                }
                $out[] = [
                    'nama_indicator' => $q['n'],
                    'kategori' => 'Komponen Penilaian / KPI',
                    'bobot' => $q['b'],
                    'details' => $details,
                ];
            }

            return $out;
        };

        $masters = [
            ['id' => 1, 'level' => 'Direktur Holding', 'divisi' => 'Information Technology', 'tipe_penilaian' => 'Kompetensi SoftSkill', 'deskripsi' => 'Penilaian Direktur', 'indicators' => [0, 1, 2, 3]],
            ['id' => 2, 'level' => 'Manager', 'divisi' => 'Information Technology', 'tipe_penilaian' => 'Kompetensi SoftSkill', 'deskripsi' => 'Penilaian Manager', 'indicators' => [0, 1, 2, 3]],
            ['id' => 3, 'level' => 'Staff', 'divisi' => 'HRD', 'tipe_penilaian' => 'KPI', 'deskripsi' => 'KPI Staff', 'indicators' => [0, 3]],
        ];

        $masterRows = [];
        foreach ($masters as $m) {
            $masterRows[] = [
                'id' => $m['id'],
                'sbu' => 'HOLDING',
                'sub' => 'HOLDING',
                'branch' => 'HOLDING',
                'level' => $m['level'],
                'divisi' => $m['divisi'],
                'tipe_penilaian' => $m['tipe_penilaian'],
                'deskripsi' => $m['deskripsi'],
                'status' => 'Active',
                'indicators' => json_encode($makeIndicators($m['indicators'])),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('master_assessments')->insert($masterRows);

        // ---------------------------------------------------------
        // transaction_360: 45 list rows + 3 detailed summary rows
        // ---------------------------------------------------------
        $dummyNama = ['Syifa Mauliddia', 'Julyandra Andi Santoso', 'Khabibi Al Munif', 'ARU TEST', 'Rina Wulandari', 'Budi Hartono', 'Dewi Lestari', 'Agus Prasetyo', 'Nadia Putri', 'Fajar Ramadhan'];
        $dummyHub = ['Atasan', 'Rekan', 'Bawahan', 'Diri Sendiri'];
        $divisi = ['Information Technology', 'HRD', 'Finance', 'Marketing'];
        $fixed = [
            ['ARU TEST', '', '2026-08-22', 'DRAFT'],
            ['ARU TEST', '', '2026-08-28', 'DRAFT'],
            ['ARU TEST', '', '2026-08-22', 'POSTED'],
            ['ARU TEST', '', '2026-08-26', 'DRAFT'],
            ['', '', '2026-08-26', 'DRAFT'],
            ['', '', '2026-08-26', 'DRAFT'],
            ['ARU TEST', '', '2026-08-26', 'DRAFT'],
            ['Julyandra Andi Santoso', '', '2026-08-22', 'POSTED'],
            ['', '', '2026-08-22', 'DRAFT'],
            ['Julyandra Andi Santoso', '', '2026-08-22', 'DRAFT'],
            ['', '', '2026-08-22', 'POSTED'],
            ['Khabibi Al Munif', '', '2026-08-22', 'DRAFT'],
            ['ARU TEST', '', '2026-08-22', 'DRAFT'],
        ];

        $txRows = [];
        for ($i = 0; $i < 45; $i++) {
            $f = isset($fixed[$i]) ? $fixed[$i] : null;
            $txRows[] = [
                'id' => $i + 1,
                'employee_id' => null,
                'penilai_id' => null,
                'master_assessment_id' => null,
                'periode' => '2026-08',
                'hubungan_penilai' => $f ? $f[1] : $dummyHub[$i % 4],
                'tanggal' => $f ? $f[2] : sprintf('2026-08-%02d', ($i % 28) + 1),
                'total_nilai' => 0,
                'nama' => $f ? $f[0] : $dummyNama[$i % count($dummyNama)],
                'nama_jabatan' => null,
                'nama_divisi' => $divisi[$i % 4],
                'nama_level' => null,
                'tipe_penilaian' => null,
                'penilaian' => null,
                'catatan_1' => null,
                'catatan_2' => null,
                'catatan_3' => null,
                'catatan_4' => null,
                'detail_json' => '[]',
                'status' => $f ? $f[3] : ($i % 4 === 2 ? 'POSTED' : 'DRAFT'),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $pilihan = [
            'Selalu menetapkan target kerja yang sangat tinggi dan menantang untuk dirinya',
            'Sering menetapkan target kerja yang tinggi untuk dirinya',
            'Menetapkan target kerja yang cukup sesuai tuntutan pekerjaan',
            'Kurang menetapkan target kerja yang tinggi, hanya mengikuti target minimum',
            'Tidak pernah menetapkan target kerja sendiri',
        ];
        $pertanyaanTarget = 'Bagaimana penilaian Anda terhadap individu dalam penetapan target kerja yang tinggi?';
        $makeKomponen = function ($selectedIndex, $nilai) use ($pilihan, $pertanyaanTarget) {
            return [[
                'kategori' => 'Komponen Penilaian / KPI',
                'pertanyaan' => $pertanyaanTarget,
                'bobot' => 3,
                'jawaban' => $pilihan[$selectedIndex],
                'total_nilai' => $nilai,
                'options' => $pilihan,
            ]];
        };

        $detailed = [
            [
                'id' => 46, 'employee_id' => 1, 'penilai_id' => null, 'master_assessment_id' => 2,
                'periode' => '2026-11', 'hubungan_penilai' => 'Atasan Langsung', 'tanggal' => '2026-11-01',
                'total_nilai' => 85, 'nama' => 'Syifa Mauliddia', 'nama_jabatan' => 'Fullstack Developer Intern',
                'nama_divisi' => 'Information Technology', 'nama_level' => 'Staff',
                'tipe_penilaian' => 'Kompetensi SoftSkill', 'penilaian' => 'Ahmad (Manager)',
                'detail_json' => json_encode($makeKomponen(1, 12)), 'status' => 'POSTED',
            ],
            [
                'id' => 47, 'employee_id' => 1, 'penilai_id' => null, 'master_assessment_id' => 2,
                'periode' => '2026-11', 'hubungan_penilai' => 'Rekan Kerja', 'tanggal' => '2026-11-01',
                'total_nilai' => 80, 'nama' => 'Syifa Mauliddia', 'nama_jabatan' => 'Fullstack Developer Intern',
                'nama_divisi' => 'Information Technology', 'nama_level' => 'Staff',
                'tipe_penilaian' => 'Kompetensi SoftSkill', 'penilaian' => 'Budi (Peer)',
                'detail_json' => json_encode($makeKomponen(2, 12)), 'status' => 'POSTED',
            ],
            [
                'id' => 48, 'employee_id' => 11, 'penilai_id' => null, 'master_assessment_id' => 2,
                'periode' => '2026-11', 'hubungan_penilai' => 'Atasan Langsung', 'tanggal' => '2026-11-01',
                'total_nilai' => 75, 'nama' => 'Andi Pratama', 'nama_jabatan' => 'Data Analyst',
                'nama_divisi' => 'Business Intelligence', 'nama_level' => 'Staff',
                'tipe_penilaian' => 'Kompetensi SoftSkill', 'penilaian' => 'Ahmad (Manager)',
                'detail_json' => json_encode($makeKomponen(2, 9)), 'status' => 'POSTED',
            ],
        ];

        foreach ($detailed as $d) {
            $txRows[] = $d + [
                'catatan_1' => null,
                'catatan_2' => null,
                'catatan_3' => null,
                'catatan_4' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('transaction_360')->insert($txRows);

        // ---------------------------------------------------------
        // m_menu
        // ---------------------------------------------------------
        $menus = [
            [1, 'Penilaian', 'Transaksi', 'Penilaian 360', '/t_penilaian360', '/t_penilaian360', 'users', 2.16, 1],
            [2, 'Master', '', 'Media', '/m_media', '/m_media', 'box', 2.24, 1],
            [3, 'Recruitment', 'Transaksi', 'Pengajuan Reqruitment', '/t_req_recruitment', '/t_req_recruitment', 'folder-open', 3, 1],
            [4, 'Laporan', '', 'Laporan Perjalanan Dinas', '/l_perjalanan_dinas', '/l_perjalanan_dinas', 'bookmark', 9100, 1],
            [5, 'Laporan', '', 'Laporan Klaim Rawat Inap', '/l_klaim_ranap', '/l_klaim_ranap', 'bookmark', 9.99, 1],
            [6, 'Transaksi', '', 'Klaim Rawat Inap', '/t_klaim_ranap', '/t_klaim_ranap', 'stopwatch', 3.08, 1],
            [7, 'Laporan', '', 'Laporan Klaim Akses', '/l_klaim_askes', '/l_klaim_askes', 'bookmark', 9.99, 1],
            [8, 'Transaksi', '', 'Surat Peringatan Lama', '/t_sgp', '/t_sgp', 'triangle-exclamation', 3.15, 0],
            [9, 'Master', '', 'Cost Center', '/m_cost_center', '/m_cost_center', 'clipboard', 2.13, 1],
            [10, 'Laporan', '', 'Export Karyawan', '/l_karyawan_export', '/l_karyawan_export', 'file invoice dollar', 4.98, 1],
            [11, 'Laporan', '', 'Eksport BPJS Kesehatan', '/export_bpjs_kes', '/export_bpjs_kes', 'file-csv', 4.1, 1],
            [12, 'Laporan', '', 'Eksport BPJS Ketenagakerjaan', '/export_bpjs_tk', '/export_bpjs_tk', 'file-csv', 4.1, 1],
            [13, 'Master', '', 'Master Gaji Harian', '/m_standart_gaji_harian', '/m_standart_gaji_harian', 'money-bill', 2.22, 1],
            [14, 'Transaksi', '', 'Lembur Harian', '/t_lembur_harian', '/t_lembur_harian', 'stopwatch', 3.08, 1],
            [15, 'Outsourcing', 'Master', 'Master Gaji Harian OS', '/m_standart_gaji_harian_os', '/m_standart_gaji_harian_os', 'money-bill', 3.14, 1],
        ];

        $menuRows = [];
        foreach ($menus as $m) {
            $menuRows[] = [
                'id' => $m[0], 'modul' => $m[1], 'submodul' => $m[2], 'menu' => $m[3],
                'path' => $m[4], 'endpoint' => $m[5], 'icon' => $m[6], 'sequence' => $m[7],
                'description' => '', 'is_active' => $m[8],
                'created_at' => $now, 'updated_at' => $now,
            ];
        }
        DB::table('m_menu')->insert($menuRows);

        // ---------------------------------------------------------
        // fingerprints
        // ---------------------------------------------------------
        DB::table('fingerprints')->insert([
            ['id' => 1, 'kode' => 'FNG-001', 'nama' => 'Fingerprint Pintu Utama', 'id_machine' => 'MCH-1001', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'kode' => 'FNG-002', 'nama' => 'Fingerprint Ruang IT', 'id_machine' => 'MCH-1002', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'kode' => 'FNG-003', 'nama' => 'Fingerprint Gudang', 'id_machine' => 'MCH-1003', 'is_active' => 0, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ---------------------------------------------------------
        // akses_respo
        // ---------------------------------------------------------
        DB::table('akses_respo')->insert([
            ['id' => 1, 'name' => 'Manager', 'roles' => 'Assistant Manager, Wakadiv, Kadiv, Wakaru, Karu, Staff, Manager', 'sbu' => 'PACKAGING', 'sub' => 'Temprina Packaging Komersial', 'branch' => 'Packaging Komersial Karah', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'Staff IT', 'roles' => 'Software Engineer, IT Support', 'sbu' => 'HEAD OFFICE', 'sub' => 'Corporate IT', 'branch' => 'Surabaya Pusat', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ---------------------------------------------------------
        // outstanding_approvals
        // ---------------------------------------------------------
        DB::table('outstanding_approvals')->insert([
            ['id' => 1, 'trx_name' => 'Pengajuan Cuti', 'nomor' => 'APP/CUTI/2026/10/001', 'trx_nomor' => 'TRX-CT-001', 'creator' => 'Budi Santoso', 'trx_date' => '2026-10-25', 'created_at' => '2026-10-25 10:00:00', 'status' => 'Menunggu Approval', 'form_name' => 'transaksi/cuti', 'm_branch_id' => null, 'm_subcomp_id' => null, 'updated_at' => $now],
            ['id' => 2, 'trx_name' => 'Pengajuan Cuti', 'nomor' => 'APP/CUTI/2026/10/002', 'trx_nomor' => 'TRX-CT-002', 'creator' => 'Siti Aminah', 'trx_date' => '2026-10-26', 'created_at' => '2026-10-26 14:30:00', 'status' => 'Menunggu Approval', 'form_name' => 'transaksi/cuti', 'm_branch_id' => null, 'm_subcomp_id' => null, 'updated_at' => $now],
            ['id' => 3, 'trx_name' => 'Lembur Harian', 'nomor' => 'APP/LMBR/2026/10/001', 'trx_nomor' => 'TRX-LM-001', 'creator' => 'Andi Wijaya', 'trx_date' => '2026-10-27', 'created_at' => '2026-10-27 08:15:00', 'status' => 'Menunggu Approval', 'form_name' => 'transaksi/lembur', 'm_branch_id' => null, 'm_subcomp_id' => null, 'updated_at' => $now],
        ]);

        // ---------------------------------------------------------
        // branches
        // ---------------------------------------------------------
        $branches = [
            ['id' => 1, 'branch_name' => 'Temprina Perkom Sumengko', 'subcomp_name' => 'Temprina Perkom SBN'],
            ['id' => 2, 'branch_name' => 'Temprina Perkom Nganjuk', 'subcomp_name' => 'Perkom DCI'],
            ['id' => 3, 'branch_name' => 'Temprina Perkom Bawen', 'subcomp_name' => 'Temprina Perkom SBN'],
            ['id' => 4, 'branch_name' => 'TWL Malang', 'subcomp_name' => 'Publishing DCP'],
            ['id' => 5, 'branch_name' => 'Shopping Bag Bekasi', 'subcomp_name' => 'Temprina Packaging Shopping Bag'],
            ['id' => 6, 'branch_name' => 'Packaging Komersial Bekasi', 'subcomp_name' => 'Temprina Packaging Komersial'],
            ['id' => 7, 'branch_name' => 'Perkom DCI Nganjuk', 'subcomp_name' => 'Perkom DCI'],
            ['id' => 8, 'branch_name' => 'Perkom Aliansi Cengkareng', 'subcomp_name' => 'Perkom Aliansi'],
            ['id' => 9, 'branch_name' => 'JPBOOK Surabaya', 'subcomp_name' => 'Publishing DCP'],
            ['id' => 10, 'branch_name' => 'DIM Surabaya', 'subcomp_name' => 'DIM'],
            ['id' => 11, 'branch_name' => 'HOLDING', 'subcomp_name' => 'HOLDING'],
            ['id' => 12, 'branch_name' => 'Temprina Perkom SBN', 'subcomp_name' => 'Temprina Perkom SBN'],
            ['id' => 13, 'branch_name' => 'Temprina Perkom Bali', 'subcomp_name' => 'Temprina Perkom Bali'],
            ['id' => 14, 'branch_name' => 'Packaging Komersial Karah', 'subcomp_name' => 'Temprina Packaging Komersial'],
            ['id' => 15, 'branch_name' => 'Surabaya Pusat', 'subcomp_name' => 'HOLDING'],
            ['id' => 16, 'branch_name' => 'Jakarta Selatan', 'subcomp_name' => 'HOLDING'],
            ['id' => 17, 'branch_name' => 'Bandung', 'subcomp_name' => 'HOLDING'],
        ];
        $branchRows = [];
        foreach ($branches as $b) {
            $branchRows[] = $b + ['total_gaji' => 0, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('branches')->insert($branchRows);

        // ---------------------------------------------------------
        // subcompanies
        // ---------------------------------------------------------
        $subcompanies = ['Temprina Perkom SBN', 'Temprina Perkom Bali', 'Temprina Packaging Komersial', 'Temprina Packaging Shopping Bag', 'DIM', 'Wrapping Craft', 'Perkom Aliansi', 'Perkom DCI', 'Publishing DCP', 'HOLDING'];
        $subRows = [];
        foreach ($subcompanies as $i => $name) {
            $subRows[] = ['id' => $i + 1, 'subcomp_name' => $name, 'total_gaji' => 0, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now];
        }
        DB::table('subcompanies')->insert($subRows);

        // ---------------------------------------------------------
        // presensi_absensi: workdays 2026-11-01 .. 2026-11-15, employees 1..11
        // ---------------------------------------------------------
        $presensiRows = [];
        for ($day = 1; $day <= 15; $day++) {
            $date = new \DateTime(sprintf('2026-11-%02d', $day));
            $dow = (int) $date->format('w');
            if ($dow === 0 || $dow === 6) {
                continue; // skip Sat/Sun
            }
            $tanggal = $date->format('Y-m-d');

            for ($employeeId = 1; $employeeId <= 11; $employeeId++) {
                $status = 'ATTEND';
                $checkin = '07:45:00';
                $checkout = '17:05:00';
                $note = 'Hadir tepat waktu';

                if (($employeeId + $day) % 11 === 0) {
                    $status = 'ALPHA';
                    $checkin = null;
                    $checkout = null;
                    $note = 'Tidak hadir tanpa keterangan';
                } elseif (($employeeId + $day) % 7 === 0) {
                    $status = 'CUTI';
                    $checkin = null;
                    $checkout = null;
                    $note = 'Cuti tahunan';
                }

                if ($checkin === null) {
                    $presensiRows[] = [
                        'default_user_id' => $employeeId, 'tanggal' => $tanggal, 'status' => $status,
                        'shift' => 'Reguler Pagi',
                        'checkin_time' => null, 'checkin_on_scope' => 0, 'checkin_foto' => null,
                        'checkin_region' => null, 'checkin_lat' => null, 'checkin_long' => null,
                        'checkin_address' => null, 'catatan_in' => $note,
                        'checkout_time' => null, 'checkout_on_scope' => 0, 'checkout_foto' => null,
                        'checkout_region' => null, 'checkout_lat' => null, 'checkout_long' => null,
                        'checkout_address' => null, 'catatan_out' => $note,
                        'created_at' => $now, 'updated_at' => $now,
                    ];
                } else {
                    $presensiRows[] = [
                        'default_user_id' => $employeeId, 'tanggal' => $tanggal, 'status' => $status,
                        'shift' => 'Reguler Pagi',
                        'checkin_time' => $checkin, 'checkin_on_scope' => 1, 'checkin_foto' => null,
                        'checkin_region' => 'Surabaya Karah', 'checkin_lat' => '-7.3244677',
                        'checkin_long' => '112.7550714', 'checkin_address' => 'Jl. Ahmad Yani No. 88, Surabaya',
                        'catatan_in' => $note,
                        'checkout_time' => $checkout, 'checkout_on_scope' => 1, 'checkout_foto' => null,
                        'checkout_region' => 'Surabaya Karah', 'checkout_lat' => '-7.3244677',
                        'checkout_long' => '112.7550714', 'checkout_address' => 'Jl. Ahmad Yani No. 88, Surabaya',
                        'catatan_out' => $note,
                        'created_at' => $now, 'updated_at' => $now,
                    ];
                }
            }
        }
        DB::table('presensi_absensi')->insert($presensiRows);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
