-- =====================================================================
-- Talent DB schema + dummy data seed (idempotent, MariaDB 10.4)
-- Generated from existing frontend/backend source data.
-- Re-runnable: drops no tables, only deletes/re-inserts seeded rows.
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
SET NAMES utf8mb4;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ---------------------------------------------------------------
-- users (existing Laravel table): add missing columns
-- ---------------------------------------------------------------
ALTER TABLE users ADD COLUMN IF NOT EXISTS username VARCHAR(100) NULL UNIQUE;
ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(50) NOT NULL DEFAULT 'Staff';
ALTER TABLE users ADD COLUMN IF NOT EXISTS token_fcm VARCHAR(255) NULL;

-- ---------------------------------------------------------------
-- Schema
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS employees (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  kode VARCHAR(50) NOT NULL,
  nama VARCHAR(150) NOT NULL,
  jabatan VARCHAR(150) NULL,
  divisi VARCHAR(150) NULL,
  level VARCHAR(100) NULL,
  atasan_id BIGINT UNSIGNED NULL,
  sbu VARCHAR(100) NULL,
  sub VARCHAR(150) NULL,
  branch VARCHAR(150) NULL,
  jam_kerja VARCHAR(50) NOT NULL DEFAULT '08:00 - 17:00',
  shift VARCHAR(50) NOT NULL DEFAULT 'Reguler Pagi',
  is_active TINYINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_employees_kode (kode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS master_assessments (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  sbu VARCHAR(100) NULL,
  sub VARCHAR(150) NULL,
  branch VARCHAR(150) NULL,
  level VARCHAR(100) NULL,
  divisi VARCHAR(150) NULL,
  tipe_penilaian VARCHAR(100) NULL,
  deskripsi VARCHAR(255) NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'Active',
  indicators JSON NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS transaction_360 (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  employee_id BIGINT UNSIGNED NULL,
  penilai_id BIGINT UNSIGNED NULL,
  master_assessment_id BIGINT UNSIGNED NULL,
  periode VARCHAR(10) NULL,
  hubungan_penilai VARCHAR(50) NULL,
  tanggal DATE NULL,
  total_nilai DECIMAL(6,2) NOT NULL DEFAULT 0,
  nama VARCHAR(150) NULL,
  nama_jabatan VARCHAR(150) NULL,
  nama_divisi VARCHAR(150) NULL,
  nama_level VARCHAR(100) NULL,
  tipe_penilaian VARCHAR(100) NULL,
  penilaian VARCHAR(255) NULL,
  catatan_1 TEXT NULL,
  catatan_2 TEXT NULL,
  catatan_3 TEXT NULL,
  catatan_4 TEXT NULL,
  detail_json JSON NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'DRAFT',
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_tx_employee (employee_id),
  KEY idx_tx_master (master_assessment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS m_menu (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  modul VARCHAR(100) NULL,
  submodul VARCHAR(100) NULL,
  menu VARCHAR(150) NULL,
  path VARCHAR(255) NULL,
  endpoint VARCHAR(255) NULL,
  icon VARCHAR(100) NULL,
  sequence DECIMAL(10,2) NOT NULL DEFAULT 0,
  description TEXT NULL,
  is_active TINYINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS fingerprints (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  kode VARCHAR(50) NOT NULL,
  nama VARCHAR(150) NOT NULL,
  id_machine VARCHAR(100) NULL,
  is_active TINYINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_fingerprints_kode (kode)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS akses_respo (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(150) NULL,
  roles TEXT NULL,
  sbu VARCHAR(100) NULL,
  sub VARCHAR(150) NULL,
  branch VARCHAR(150) NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS outstanding_approvals (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  trx_name VARCHAR(150) NULL,
  nomor VARCHAR(100) NULL,
  trx_nomor VARCHAR(100) NULL,
  creator VARCHAR(150) NULL,
  trx_date DATE NULL,
  created_at DATETIME NULL DEFAULT NULL,
  status VARCHAR(50) NULL,
  form_name VARCHAR(150) NULL,
  m_branch_id BIGINT UNSIGNED NULL,
  m_subcomp_id BIGINT UNSIGNED NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS branches (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  branch_name VARCHAR(150) NOT NULL,
  subcomp_name VARCHAR(150) NULL,
  total_gaji DECIMAL(15,2) NOT NULL DEFAULT 0,
  is_active TINYINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS subcompanies (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  subcomp_name VARCHAR(150) NOT NULL,
  total_gaji DECIMAL(15,2) NOT NULL DEFAULT 0,
  is_active TINYINT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS presensi_absensi (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  default_user_id BIGINT UNSIGNED NOT NULL,
  tanggal DATE NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'NOT ATTEND',
  shift VARCHAR(50) NULL,
  checkin_time TIME NULL,
  checkin_on_scope TINYINT NOT NULL DEFAULT 0,
  checkin_foto VARCHAR(255) NULL,
  checkin_region VARCHAR(150) NULL,
  checkin_lat VARCHAR(50) NULL,
  checkin_long VARCHAR(50) NULL,
  checkin_address VARCHAR(255) NULL,
  catatan_in VARCHAR(255) NULL,
  checkout_time TIME NULL,
  checkout_on_scope TINYINT NOT NULL DEFAULT 0,
  checkout_foto VARCHAR(255) NULL,
  checkout_region VARCHAR(150) NULL,
  checkout_lat VARCHAR(50) NULL,
  checkout_long VARCHAR(50) NULL,
  checkout_address VARCHAR(255) NULL,
  catatan_out VARCHAR(255) NULL,
  created_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_presensi_user_tanggal (default_user_id, tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Clear seeded data (idempotent)
-- ---------------------------------------------------------------
DELETE FROM presensi_absensi;
DELETE FROM transaction_360;
DELETE FROM master_assessments;
DELETE FROM employees;
DELETE FROM m_menu;
DELETE FROM fingerprints;
DELETE FROM akses_respo;
DELETE FROM outstanding_approvals;
DELETE FROM branches;
DELETE FROM subcompanies;
DELETE FROM users WHERE username IN ('syifa','budi','julyandra','khabibi','dewi','agus','nadia','fajar','aru','rina','andi','admin') OR email IN ('syifa@talent.local','budi@talent.local','julyandra@talent.local','khabibi@talent.local','dewi@talent.local','agus@talent.local','nadia@talent.local','fajar@talent.local','aru@talent.local','rina@talent.local','andi@talent.local','admin@talent.local');

-- users (plain-text passwords: server.js compares plain text)
INSERT INTO users (username, name, email, password, role, token_fcm, created_at, updated_at) VALUES
('syifa', 'Syifa Mauliddia', 'syifa@talent.local', 'password123', 'Manager', NULL, NOW(), NOW()),
('budi', 'Budi Hartono', 'budi@talent.local', 'password123', 'Direktur Holding', NULL, NOW(), NOW()),
('julyandra', 'Julyandra Andi Santoso', 'julyandra@talent.local', 'password123', 'Staff', NULL, NOW(), NOW()),
('khabibi', 'Khabibi Al Munif', 'khabibi@talent.local', 'password123', 'Staff', NULL, NOW(), NOW()),
('dewi', 'Dewi Lestari', 'dewi@talent.local', 'password123', 'Staff', NULL, NOW(), NOW()),
('agus', 'Agus Prasetyo', 'agus@talent.local', 'password123', 'Manager', NULL, NOW(), NOW()),
('nadia', 'Nadia Putri', 'nadia@talent.local', 'password123', 'Staff', NULL, NOW(), NOW()),
('fajar', 'Fajar Ramadhan', 'fajar@talent.local', 'password123', 'Manager', NULL, NOW(), NOW()),
('aru', 'ARU TEST', 'aru@talent.local', 'password123', 'Staff', NULL, NOW(), NOW()),
('rina', 'Rina Wulandari', 'rina@talent.local', 'password123', 'Staff', NULL, NOW(), NOW()),
('andi', 'Andi Pratama', 'andi@talent.local', 'password123', 'Staff', NULL, NOW(), NOW()),
('admin', 'Admin', 'admin@talent.local', 'admin123', 'Administrator', NULL, NOW(), NOW());

-- employees
INSERT INTO employees (id, kode, nama, jabatan, divisi, level, atasan_id, sbu, sub, branch, jam_kerja, shift, is_active, created_at, updated_at) VALUES
(1, 'EMP-001', 'Syifa Mauliddia', 'Manager IT', 'Information Technology', 'Manager', 2, 'HOLDING', 'HOLDING', 'HOLDING', '08:00 - 17:00', 'Reguler Pagi', 1, NOW(), NOW()),
(2, 'EMP-002', 'Budi Hartono', 'Direktur Holding', 'Information Technology', 'Direktur Holding', NULL, 'HOLDING', 'HOLDING', 'HOLDING', '08:00 - 17:00', 'Reguler Pagi', 1, NOW(), NOW()),
(3, 'EMP-003', 'Julyandra Andi Santoso', 'Software Engineer', 'Information Technology', 'Staff', 1, 'HOLDING', 'HOLDING', 'HOLDING', '08:00 - 17:00', 'Reguler Pagi', 1, NOW(), NOW()),
(4, 'EMP-004', 'Khabibi Al Munif', 'IT Support', 'Information Technology', 'Staff', 1, 'HOLDING', 'HOLDING', 'HOLDING', '08:00 - 17:00', 'Reguler Pagi', 1, NOW(), NOW()),
(5, 'EMP-005', 'Dewi Lestari', 'HR Staff', 'HRD', 'Staff', 6, 'HOLDING', 'HOLDING', 'HOLDING', '08:00 - 17:00', 'Reguler Pagi', 1, NOW(), NOW()),
(6, 'EMP-006', 'Agus Prasetyo', 'HR Manager', 'HRD', 'Manager', 2, 'HOLDING', 'HOLDING', 'HOLDING', '08:00 - 17:00', 'Reguler Pagi', 1, NOW(), NOW()),
(7, 'EMP-007', 'Nadia Putri', 'Staff Finance', 'Finance', 'Staff', 8, 'HOLDING', 'HOLDING', 'HOLDING', '08:00 - 17:00', 'Reguler Pagi', 1, NOW(), NOW()),
(8, 'EMP-008', 'Fajar Ramadhan', 'Finance Manager', 'Finance', 'Manager', 2, 'HOLDING', 'HOLDING', 'HOLDING', '08:00 - 17:00', 'Reguler Pagi', 1, NOW(), NOW()),
(9, 'EMP-009', 'ARU TEST', 'Staff', 'Information Technology', 'Staff', 1, 'HOLDING', 'HOLDING', 'HOLDING', '08:00 - 17:00', 'Reguler Pagi', 1, NOW(), NOW()),
(10, 'EMP-010', 'Rina Wulandari', 'Marketing Staff', 'Marketing', 'Staff', NULL, 'HOLDING', 'HOLDING', 'HOLDING', '08:00 - 17:00', 'Reguler Pagi', 1, NOW(), NOW()),
(11, 'EMP-011', 'Andi Pratama', 'Data Analyst', 'Business Intelligence', 'Staff', NULL, 'HOLDING', 'HOLDING', 'HOLDING', '08:00 - 17:00', 'Reguler Pagi', 1, NOW(), NOW());

-- master_assessments
INSERT INTO master_assessments (id, sbu, sub, branch, level, divisi, tipe_penilaian, deskripsi, status, indicators, created_at, updated_at) VALUES
(1, 'HOLDING', 'HOLDING', 'HOLDING', 'Direktur Holding', 'Information Technology', 'Kompetensi SoftSkill', 'Penilaian Direktur', 'Active', '[{"nama_indicator":"Bagaimana penilaian Anda terhadap individu dalam penetapan target kerja yang tinggi?","kategori":"Komponen Penilaian / KPI","bobot":3,"details":[{"nilai":5,"keterangan":"Selalu menetapkan target kerja yang sangat tinggi dan menantang untuk dirinya"},{"nilai":4,"keterangan":"Sering menetapkan target kerja yang tinggi untuk dirinya"},{"nilai":3,"keterangan":"Menetapkan target kerja yang cukup sesuai tuntutan pekerjaan"},{"nilai":2,"keterangan":"Kurang menetapkan target kerja yang tinggi, hanya mengikuti target minimum"},{"nilai":1,"keterangan":"Tidak pernah menetapkan target kerja sendiri"}]},{"nama_indicator":"Bagaimana kemampuan individu dalam bekerja sama dengan tim?","kategori":"Komponen Penilaian / KPI","bobot":2,"details":[{"nilai":5,"keterangan":"Selalu aktif membangun kerja sama dan membantu rekan tim"},{"nilai":4,"keterangan":"Sering bekerja sama dengan baik bersama tim"},{"nilai":3,"keterangan":"Dapat bekerja sama jika diperlukan"},{"nilai":2,"keterangan":"Kurang aktif dalam kerja sama tim"},{"nilai":1,"keterangan":"Sulit bekerja sama dengan tim"}]},{"nama_indicator":"Bagaimana individu berkomunikasi dengan atasan, rekan, dan bawahan?","kategori":"Komponen Penilaian / KPI","bobot":2,"details":[{"nilai":5,"keterangan":"Selalu berkomunikasi dengan jelas, terbuka, dan tepat waktu"},{"nilai":4,"keterangan":"Sering berkomunikasi dengan jelas dan terbuka"},{"nilai":3,"keterangan":"Komunikasi cukup baik untuk kebutuhan pekerjaan"},{"nilai":2,"keterangan":"Kurang jelas dalam menyampaikan informasi"},{"nilai":1,"keterangan":"Sulit berkomunikasi dan sering menimbulkan salah paham"}]},{"nama_indicator":"Bagaimana individu menyelesaikan masalah dan mengambil keputusan?","kategori":"Komponen Penilaian / KPI","bobot":3,"details":[{"nilai":5,"keterangan":"Selalu menyelesaikan masalah dengan cepat dan keputusan tepat"},{"nilai":4,"keterangan":"Sering mengambil keputusan yang baik"},{"nilai":3,"keterangan":"Mampu menyelesaikan masalah umum dengan arahan"},{"nilai":2,"keterangan":"Kurang tegas dan sering menunda keputusan"},{"nilai":1,"keterangan":"Tidak mampu menyelesaikan masalah secara mandiri"}]}]', NOW(), NOW()),
(2, 'HOLDING', 'HOLDING', 'HOLDING', 'Manager', 'Information Technology', 'Kompetensi SoftSkill', 'Penilaian Manager', 'Active', '[{"nama_indicator":"Bagaimana penilaian Anda terhadap individu dalam penetapan target kerja yang tinggi?","kategori":"Komponen Penilaian / KPI","bobot":3,"details":[{"nilai":5,"keterangan":"Selalu menetapkan target kerja yang sangat tinggi dan menantang untuk dirinya"},{"nilai":4,"keterangan":"Sering menetapkan target kerja yang tinggi untuk dirinya"},{"nilai":3,"keterangan":"Menetapkan target kerja yang cukup sesuai tuntutan pekerjaan"},{"nilai":2,"keterangan":"Kurang menetapkan target kerja yang tinggi, hanya mengikuti target minimum"},{"nilai":1,"keterangan":"Tidak pernah menetapkan target kerja sendiri"}]},{"nama_indicator":"Bagaimana kemampuan individu dalam bekerja sama dengan tim?","kategori":"Komponen Penilaian / KPI","bobot":2,"details":[{"nilai":5,"keterangan":"Selalu aktif membangun kerja sama dan membantu rekan tim"},{"nilai":4,"keterangan":"Sering bekerja sama dengan baik bersama tim"},{"nilai":3,"keterangan":"Dapat bekerja sama jika diperlukan"},{"nilai":2,"keterangan":"Kurang aktif dalam kerja sama tim"},{"nilai":1,"keterangan":"Sulit bekerja sama dengan tim"}]},{"nama_indicator":"Bagaimana individu berkomunikasi dengan atasan, rekan, dan bawahan?","kategori":"Komponen Penilaian / KPI","bobot":2,"details":[{"nilai":5,"keterangan":"Selalu berkomunikasi dengan jelas, terbuka, dan tepat waktu"},{"nilai":4,"keterangan":"Sering berkomunikasi dengan jelas dan terbuka"},{"nilai":3,"keterangan":"Komunikasi cukup baik untuk kebutuhan pekerjaan"},{"nilai":2,"keterangan":"Kurang jelas dalam menyampaikan informasi"},{"nilai":1,"keterangan":"Sulit berkomunikasi dan sering menimbulkan salah paham"}]},{"nama_indicator":"Bagaimana individu menyelesaikan masalah dan mengambil keputusan?","kategori":"Komponen Penilaian / KPI","bobot":3,"details":[{"nilai":5,"keterangan":"Selalu menyelesaikan masalah dengan cepat dan keputusan tepat"},{"nilai":4,"keterangan":"Sering mengambil keputusan yang baik"},{"nilai":3,"keterangan":"Mampu menyelesaikan masalah umum dengan arahan"},{"nilai":2,"keterangan":"Kurang tegas dan sering menunda keputusan"},{"nilai":1,"keterangan":"Tidak mampu menyelesaikan masalah secara mandiri"}]}]', NOW(), NOW()),
(3, 'HOLDING', 'HOLDING', 'HOLDING', 'Staff', 'HRD', 'KPI', 'KPI Staff', 'Active', '[{"nama_indicator":"Bagaimana penilaian Anda terhadap individu dalam penetapan target kerja yang tinggi?","kategori":"Komponen Penilaian / KPI","bobot":3,"details":[{"nilai":5,"keterangan":"Selalu menetapkan target kerja yang sangat tinggi dan menantang untuk dirinya"},{"nilai":4,"keterangan":"Sering menetapkan target kerja yang tinggi untuk dirinya"},{"nilai":3,"keterangan":"Menetapkan target kerja yang cukup sesuai tuntutan pekerjaan"},{"nilai":2,"keterangan":"Kurang menetapkan target kerja yang tinggi, hanya mengikuti target minimum"},{"nilai":1,"keterangan":"Tidak pernah menetapkan target kerja sendiri"}]},{"nama_indicator":"Bagaimana individu menyelesaikan masalah dan mengambil keputusan?","kategori":"Komponen Penilaian / KPI","bobot":3,"details":[{"nilai":5,"keterangan":"Selalu menyelesaikan masalah dengan cepat dan keputusan tepat"},{"nilai":4,"keterangan":"Sering mengambil keputusan yang baik"},{"nilai":3,"keterangan":"Mampu menyelesaikan masalah umum dengan arahan"},{"nilai":2,"keterangan":"Kurang tegas dan sering menunda keputusan"},{"nilai":1,"keterangan":"Tidak mampu menyelesaikan masalah secara mandiri"}]}]', NOW(), NOW());

-- transaction_360 (45 list rows + 3 detailed summary rows)
INSERT INTO transaction_360 (id, employee_id, penilai_id, master_assessment_id, periode, hubungan_penilai, tanggal, total_nilai, nama, nama_jabatan, nama_divisi, nama_level, tipe_penilaian, penilaian, catatan_1, catatan_2, catatan_3, catatan_4, detail_json, status, created_at, updated_at) VALUES
(1, NULL, NULL, NULL, '2026-08', '', '2026-08-22', 0, 'ARU TEST', NULL, 'Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(2, NULL, NULL, NULL, '2026-08', '', '2026-08-28', 0, 'ARU TEST', NULL, 'HRD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(3, NULL, NULL, NULL, '2026-08', '', '2026-08-22', 0, 'ARU TEST', NULL, 'Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'POSTED', NOW(), NOW()),
(4, NULL, NULL, NULL, '2026-08', '', '2026-08-26', 0, 'ARU TEST', NULL, 'Marketing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(5, NULL, NULL, NULL, '2026-08', '', '2026-08-26', 0, '', NULL, 'Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(6, NULL, NULL, NULL, '2026-08', '', '2026-08-26', 0, '', NULL, 'HRD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(7, NULL, NULL, NULL, '2026-08', '', '2026-08-26', 0, 'ARU TEST', NULL, 'Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(8, NULL, NULL, NULL, '2026-08', '', '2026-08-22', 0, 'Julyandra Andi Santoso', NULL, 'Marketing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'POSTED', NOW(), NOW()),
(9, NULL, NULL, NULL, '2026-08', '', '2026-08-22', 0, '', NULL, 'Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(10, NULL, NULL, NULL, '2026-08', '', '2026-08-22', 0, 'Julyandra Andi Santoso', NULL, 'HRD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(11, NULL, NULL, NULL, '2026-08', '', '2026-08-22', 0, '', NULL, 'Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'POSTED', NOW(), NOW()),
(12, NULL, NULL, NULL, '2026-08', '', '2026-08-22', 0, 'Khabibi Al Munif', NULL, 'Marketing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(13, NULL, NULL, NULL, '2026-08', '', '2026-08-22', 0, 'ARU TEST', NULL, 'Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(14, NULL, NULL, NULL, '2026-08', 'Rekan', '2026-08-14', 0, 'ARU TEST', NULL, 'HRD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(15, NULL, NULL, NULL, '2026-08', 'Bawahan', '2026-08-15', 0, 'Rina Wulandari', NULL, 'Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'POSTED', NOW(), NOW()),
(16, NULL, NULL, NULL, '2026-08', 'Diri Sendiri', '2026-08-16', 0, 'Budi Hartono', NULL, 'Marketing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(17, NULL, NULL, NULL, '2026-08', 'Atasan', '2026-08-17', 0, 'Dewi Lestari', NULL, 'Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(18, NULL, NULL, NULL, '2026-08', 'Rekan', '2026-08-18', 0, 'Agus Prasetyo', NULL, 'HRD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(19, NULL, NULL, NULL, '2026-08', 'Bawahan', '2026-08-19', 0, 'Nadia Putri', NULL, 'Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'POSTED', NOW(), NOW()),
(20, NULL, NULL, NULL, '2026-08', 'Diri Sendiri', '2026-08-20', 0, 'Fajar Ramadhan', NULL, 'Marketing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(21, NULL, NULL, NULL, '2026-08', 'Atasan', '2026-08-21', 0, 'Syifa Mauliddia', NULL, 'Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(22, NULL, NULL, NULL, '2026-08', 'Rekan', '2026-08-22', 0, 'Julyandra Andi Santoso', NULL, 'HRD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(23, NULL, NULL, NULL, '2026-08', 'Bawahan', '2026-08-23', 0, 'Khabibi Al Munif', NULL, 'Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'POSTED', NOW(), NOW()),
(24, NULL, NULL, NULL, '2026-08', 'Diri Sendiri', '2026-08-24', 0, 'ARU TEST', NULL, 'Marketing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(25, NULL, NULL, NULL, '2026-08', 'Atasan', '2026-08-25', 0, 'Rina Wulandari', NULL, 'Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(26, NULL, NULL, NULL, '2026-08', 'Rekan', '2026-08-26', 0, 'Budi Hartono', NULL, 'HRD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(27, NULL, NULL, NULL, '2026-08', 'Bawahan', '2026-08-27', 0, 'Dewi Lestari', NULL, 'Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'POSTED', NOW(), NOW()),
(28, NULL, NULL, NULL, '2026-08', 'Diri Sendiri', '2026-08-28', 0, 'Agus Prasetyo', NULL, 'Marketing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(29, NULL, NULL, NULL, '2026-08', 'Atasan', '2026-08-01', 0, 'Nadia Putri', NULL, 'Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(30, NULL, NULL, NULL, '2026-08', 'Rekan', '2026-08-02', 0, 'Fajar Ramadhan', NULL, 'HRD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(31, NULL, NULL, NULL, '2026-08', 'Bawahan', '2026-08-03', 0, 'Syifa Mauliddia', NULL, 'Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'POSTED', NOW(), NOW()),
(32, NULL, NULL, NULL, '2026-08', 'Diri Sendiri', '2026-08-04', 0, 'Julyandra Andi Santoso', NULL, 'Marketing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(33, NULL, NULL, NULL, '2026-08', 'Atasan', '2026-08-05', 0, 'Khabibi Al Munif', NULL, 'Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(34, NULL, NULL, NULL, '2026-08', 'Rekan', '2026-08-06', 0, 'ARU TEST', NULL, 'HRD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(35, NULL, NULL, NULL, '2026-08', 'Bawahan', '2026-08-07', 0, 'Rina Wulandari', NULL, 'Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'POSTED', NOW(), NOW()),
(36, NULL, NULL, NULL, '2026-08', 'Diri Sendiri', '2026-08-08', 0, 'Budi Hartono', NULL, 'Marketing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(37, NULL, NULL, NULL, '2026-08', 'Atasan', '2026-08-09', 0, 'Dewi Lestari', NULL, 'Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(38, NULL, NULL, NULL, '2026-08', 'Rekan', '2026-08-10', 0, 'Agus Prasetyo', NULL, 'HRD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(39, NULL, NULL, NULL, '2026-08', 'Bawahan', '2026-08-11', 0, 'Nadia Putri', NULL, 'Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'POSTED', NOW(), NOW()),
(40, NULL, NULL, NULL, '2026-08', 'Diri Sendiri', '2026-08-12', 0, 'Fajar Ramadhan', NULL, 'Marketing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(41, NULL, NULL, NULL, '2026-08', 'Atasan', '2026-08-13', 0, 'Syifa Mauliddia', NULL, 'Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(42, NULL, NULL, NULL, '2026-08', 'Rekan', '2026-08-14', 0, 'Julyandra Andi Santoso', NULL, 'HRD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(43, NULL, NULL, NULL, '2026-08', 'Bawahan', '2026-08-15', 0, 'Khabibi Al Munif', NULL, 'Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'POSTED', NOW(), NOW()),
(44, NULL, NULL, NULL, '2026-08', 'Diri Sendiri', '2026-08-16', 0, 'ARU TEST', NULL, 'Marketing', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(45, NULL, NULL, NULL, '2026-08', 'Atasan', '2026-08-17', 0, 'Rina Wulandari', NULL, 'Information Technology', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', 'DRAFT', NOW(), NOW()),
(46, 1, NULL, 2, '2026-11', 'Atasan Langsung', '2026-11-01', 85, 'Syifa Mauliddia', 'Fullstack Developer Intern', 'Information Technology', 'Staff', 'Kompetensi SoftSkill', 'Ahmad (Manager)', NULL, NULL, NULL, NULL, '[{"kategori":"Komponen Penilaian / KPI","pertanyaan":"Bagaimana penilaian Anda terhadap individu dalam penetapan target kerja yang tinggi?","bobot":3,"jawaban":"Sering menetapkan target kerja yang tinggi untuk dirinya","total_nilai":12,"options":["Selalu menetapkan target kerja yang sangat tinggi dan menantang untuk dirinya","Sering menetapkan target kerja yang tinggi untuk dirinya","Menetapkan target kerja yang cukup sesuai tuntutan pekerjaan","Kurang menetapkan target kerja yang tinggi, hanya mengikuti target minimum","Tidak pernah menetapkan target kerja sendiri"]}]', 'POSTED', NOW(), NOW()),
(47, 1, NULL, 2, '2026-11', 'Rekan Kerja', '2026-11-01', 80, 'Syifa Mauliddia', 'Fullstack Developer Intern', 'Information Technology', 'Staff', 'Kompetensi SoftSkill', 'Budi (Peer)', NULL, NULL, NULL, NULL, '[{"kategori":"Komponen Penilaian / KPI","pertanyaan":"Bagaimana penilaian Anda terhadap individu dalam penetapan target kerja yang tinggi?","bobot":3,"jawaban":"Menetapkan target kerja yang cukup sesuai tuntutan pekerjaan","total_nilai":12,"options":["Selalu menetapkan target kerja yang sangat tinggi dan menantang untuk dirinya","Sering menetapkan target kerja yang tinggi untuk dirinya","Menetapkan target kerja yang cukup sesuai tuntutan pekerjaan","Kurang menetapkan target kerja yang tinggi, hanya mengikuti target minimum","Tidak pernah menetapkan target kerja sendiri"]}]', 'POSTED', NOW(), NOW()),
(48, 11, NULL, 2, '2026-11', 'Atasan Langsung', '2026-11-01', 75, 'Andi Pratama', 'Data Analyst', 'Business Intelligence', 'Staff', 'Kompetensi SoftSkill', 'Ahmad (Manager)', NULL, NULL, NULL, NULL, '[{"kategori":"Komponen Penilaian / KPI","pertanyaan":"Bagaimana penilaian Anda terhadap individu dalam penetapan target kerja yang tinggi?","bobot":3,"jawaban":"Menetapkan target kerja yang cukup sesuai tuntutan pekerjaan","total_nilai":9,"options":["Selalu menetapkan target kerja yang sangat tinggi dan menantang untuk dirinya","Sering menetapkan target kerja yang tinggi untuk dirinya","Menetapkan target kerja yang cukup sesuai tuntutan pekerjaan","Kurang menetapkan target kerja yang tinggi, hanya mengikuti target minimum","Tidak pernah menetapkan target kerja sendiri"]}]', 'POSTED', NOW(), NOW());

-- m_menu
INSERT INTO m_menu (id, modul, submodul, menu, path, endpoint, icon, sequence, description, is_active, created_at, updated_at) VALUES
(1, 'Penilaian', 'Transaksi', 'Penilaian 360', '/t_penilaian360', '/t_penilaian360', 'users', 2.16, '', 1, NOW(), NOW()),
(2, 'Master', '', 'Media', '/m_media', '/m_media', 'box', 2.24, '', 1, NOW(), NOW()),
(3, 'Recruitment', 'Transaksi', 'Pengajuan Reqruitment', '/t_req_recruitment', '/t_req_recruitment', 'folder-open', 3, '', 1, NOW(), NOW()),
(4, 'Laporan', '', 'Laporan Perjalanan Dinas', '/l_perjalanan_dinas', '/l_perjalanan_dinas', 'bookmark', 9100, '', 1, NOW(), NOW()),
(5, 'Laporan', '', 'Laporan Klaim Rawat Inap', '/l_klaim_ranap', '/l_klaim_ranap', 'bookmark', 9.99, '', 1, NOW(), NOW()),
(6, 'Transaksi', '', 'Klaim Rawat Inap', '/t_klaim_ranap', '/t_klaim_ranap', 'stopwatch', 3.08, '', 1, NOW(), NOW()),
(7, 'Laporan', '', 'Laporan Klaim Akses', '/l_klaim_askes', '/l_klaim_askes', 'bookmark', 9.99, '', 1, NOW(), NOW()),
(8, 'Transaksi', '', 'Surat Peringatan Lama', '/t_sgp', '/t_sgp', 'triangle-exclamation', 3.15, '', 0, NOW(), NOW()),
(9, 'Master', '', 'Cost Center', '/m_cost_center', '/m_cost_center', 'clipboard', 2.13, '', 1, NOW(), NOW()),
(10, 'Laporan', '', 'Export Karyawan', '/l_karyawan_export', '/l_karyawan_export', 'file invoice dollar', 4.98, '', 1, NOW(), NOW()),
(11, 'Laporan', '', 'Eksport BPJS Kesehatan', '/export_bpjs_kes', '/export_bpjs_kes', 'file-csv', 4.1, '', 1, NOW(), NOW()),
(12, 'Laporan', '', 'Eksport BPJS Ketenagakerjaan', '/export_bpjs_tk', '/export_bpjs_tk', 'file-csv', 4.1, '', 1, NOW(), NOW()),
(13, 'Master', '', 'Master Gaji Harian', '/m_standart_gaji_harian', '/m_standart_gaji_harian', 'money-bill', 2.22, '', 1, NOW(), NOW()),
(14, 'Transaksi', '', 'Lembur Harian', '/t_lembur_harian', '/t_lembur_harian', 'stopwatch', 3.08, '', 1, NOW(), NOW()),
(15, 'Outsourcing', 'Master', 'Master Gaji Harian OS', '/m_standart_gaji_harian_os', '/m_standart_gaji_harian_os', 'money-bill', 3.14, '', 1, NOW(), NOW());

-- fingerprints
INSERT INTO fingerprints (id, kode, nama, id_machine, is_active, created_at, updated_at) VALUES
(1, 'FNG-001', 'Fingerprint Pintu Utama', 'MCH-1001', 1, NOW(), NOW()),
(2, 'FNG-002', 'Fingerprint Ruang IT', 'MCH-1002', 1, NOW(), NOW()),
(3, 'FNG-003', 'Fingerprint Gudang', 'MCH-1003', 0, NOW(), NOW());

-- akses_respo
INSERT INTO akses_respo (id, name, roles, sbu, sub, branch, created_at, updated_at) VALUES
(1, 'Manager', 'Assistant Manager, Wakadiv, Kadiv, Wakaru, Karu, Staff, Manager', 'PACKAGING', 'Temprina Packaging Komersial', 'Packaging Komersial Karah', NOW(), NOW()),
(2, 'Staff IT', 'Software Engineer, IT Support', 'HEAD OFFICE', 'Corporate IT', 'Surabaya Pusat', NOW(), NOW());

-- outstanding_approvals
INSERT INTO outstanding_approvals (id, trx_name, nomor, trx_nomor, creator, trx_date, created_at, status, form_name, m_branch_id, m_subcomp_id, updated_at) VALUES
(1, 'Pengajuan Cuti', 'APP/CUTI/2026/10/001', 'TRX-CT-001', 'Budi Santoso', '2026-10-25', '2026-10-25 10:00:00', 'Menunggu Approval', 'transaksi/cuti', NULL, NULL, NOW()),
(2, 'Pengajuan Cuti', 'APP/CUTI/2026/10/002', 'TRX-CT-002', 'Siti Aminah', '2026-10-26', '2026-10-26 14:30:00', 'Menunggu Approval', 'transaksi/cuti', NULL, NULL, NOW()),
(3, 'Lembur Harian', 'APP/LMBR/2026/10/001', 'TRX-LM-001', 'Andi Wijaya', '2026-10-27', '2026-10-27 08:15:00', 'Menunggu Approval', 'transaksi/lembur', NULL, NULL, NOW());

-- branches
INSERT INTO branches (id, branch_name, subcomp_name, total_gaji, is_active, created_at, updated_at) VALUES
(1, 'Temprina Perkom Sumengko', 'Temprina Perkom SBN', 0, 1, NOW(), NOW()),
(2, 'Temprina Perkom Nganjuk', 'Perkom DCI', 0, 1, NOW(), NOW()),
(3, 'Temprina Perkom Bawen', 'Temprina Perkom SBN', 0, 1, NOW(), NOW()),
(4, 'TWL Malang', 'Publishing DCP', 0, 1, NOW(), NOW()),
(5, 'Shopping Bag Bekasi', 'Temprina Packaging Shopping Bag', 0, 1, NOW(), NOW()),
(6, 'Packaging Komersial Bekasi', 'Temprina Packaging Komersial', 0, 1, NOW(), NOW()),
(7, 'Perkom DCI Nganjuk', 'Perkom DCI', 0, 1, NOW(), NOW()),
(8, 'Perkom Aliansi Cengkareng', 'Perkom Aliansi', 0, 1, NOW(), NOW()),
(9, 'JPBOOK Surabaya', 'Publishing DCP', 0, 1, NOW(), NOW()),
(10, 'DIM Surabaya', 'DIM', 0, 1, NOW(), NOW()),
(11, 'HOLDING', 'HOLDING', 0, 1, NOW(), NOW()),
(12, 'Temprina Perkom SBN', 'Temprina Perkom SBN', 0, 1, NOW(), NOW()),
(13, 'Temprina Perkom Bali', 'Temprina Perkom Bali', 0, 1, NOW(), NOW()),
(14, 'Packaging Komersial Karah', 'Temprina Packaging Komersial', 0, 1, NOW(), NOW()),
(15, 'Surabaya Pusat', 'HOLDING', 0, 1, NOW(), NOW()),
(16, 'Jakarta Selatan', 'HOLDING', 0, 1, NOW(), NOW()),
(17, 'Bandung', 'HOLDING', 0, 1, NOW(), NOW());

-- subcompanies
INSERT INTO subcompanies (id, subcomp_name, total_gaji, is_active, created_at, updated_at) VALUES
(1, 'Temprina Perkom SBN', 0, 1, NOW(), NOW()),
(2, 'Temprina Perkom Bali', 0, 1, NOW(), NOW()),
(3, 'Temprina Packaging Komersial', 0, 1, NOW(), NOW()),
(4, 'Temprina Packaging Shopping Bag', 0, 1, NOW(), NOW()),
(5, 'DIM', 0, 1, NOW(), NOW()),
(6, 'Wrapping Craft', 0, 1, NOW(), NOW()),
(7, 'Perkom Aliansi', 0, 1, NOW(), NOW()),
(8, 'Perkom DCI', 0, 1, NOW(), NOW()),
(9, 'Publishing DCP', 0, 1, NOW(), NOW()),
(10, 'HOLDING', 0, 1, NOW(), NOW());

-- presensi_absensi (workdays 2026-11-01 .. 2026-11-15, employees 1..11)
INSERT INTO presensi_absensi (default_user_id, tanggal, status, shift, checkin_time, checkin_on_scope, checkin_foto, checkin_region, checkin_lat, checkin_long, checkin_address, catatan_in, checkout_time, checkout_on_scope, checkout_foto, checkout_region, checkout_lat, checkout_long, checkout_address, catatan_out, created_at, updated_at) VALUES
(1, '2026-11-02', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(2, '2026-11-02', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(3, '2026-11-02', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(4, '2026-11-02', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(5, '2026-11-02', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(6, '2026-11-02', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(7, '2026-11-02', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(8, '2026-11-02', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(9, '2026-11-02', 'ALPHA', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NOW(), NOW()),
(10, '2026-11-02', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(11, '2026-11-02', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(1, '2026-11-03', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(2, '2026-11-03', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(3, '2026-11-03', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(4, '2026-11-03', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(5, '2026-11-03', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(6, '2026-11-03', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(7, '2026-11-03', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(8, '2026-11-03', 'ALPHA', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NOW(), NOW()),
(9, '2026-11-03', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(10, '2026-11-03', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(11, '2026-11-03', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(1, '2026-11-04', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(2, '2026-11-04', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(3, '2026-11-04', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(4, '2026-11-04', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(5, '2026-11-04', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(6, '2026-11-04', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(7, '2026-11-04', 'ALPHA', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NOW(), NOW()),
(8, '2026-11-04', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(9, '2026-11-04', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(10, '2026-11-04', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(11, '2026-11-04', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(1, '2026-11-05', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(2, '2026-11-05', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(3, '2026-11-05', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(4, '2026-11-05', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(5, '2026-11-05', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(6, '2026-11-05', 'ALPHA', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NOW(), NOW()),
(7, '2026-11-05', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(8, '2026-11-05', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(9, '2026-11-05', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(10, '2026-11-05', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(11, '2026-11-05', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(1, '2026-11-06', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(2, '2026-11-06', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(3, '2026-11-06', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(4, '2026-11-06', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(5, '2026-11-06', 'ALPHA', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NOW(), NOW()),
(6, '2026-11-06', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(7, '2026-11-06', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(8, '2026-11-06', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(9, '2026-11-06', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(10, '2026-11-06', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(11, '2026-11-06', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(1, '2026-11-09', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(2, '2026-11-09', 'ALPHA', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NOW(), NOW()),
(3, '2026-11-09', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(4, '2026-11-09', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(5, '2026-11-09', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(6, '2026-11-09', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(7, '2026-11-09', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(8, '2026-11-09', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(9, '2026-11-09', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(10, '2026-11-09', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(11, '2026-11-09', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(1, '2026-11-10', 'ALPHA', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NOW(), NOW()),
(2, '2026-11-10', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(3, '2026-11-10', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(4, '2026-11-10', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(5, '2026-11-10', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(6, '2026-11-10', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(7, '2026-11-10', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(8, '2026-11-10', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(9, '2026-11-10', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(10, '2026-11-10', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(11, '2026-11-10', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(1, '2026-11-11', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(2, '2026-11-11', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(3, '2026-11-11', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(4, '2026-11-11', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(5, '2026-11-11', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(6, '2026-11-11', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(7, '2026-11-11', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(8, '2026-11-11', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(9, '2026-11-11', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(10, '2026-11-11', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(11, '2026-11-11', 'ALPHA', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NOW(), NOW()),
(1, '2026-11-12', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(2, '2026-11-12', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(3, '2026-11-12', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(4, '2026-11-12', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(5, '2026-11-12', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(6, '2026-11-12', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(7, '2026-11-12', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(8, '2026-11-12', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(9, '2026-11-12', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(10, '2026-11-12', 'ALPHA', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NOW(), NOW()),
(11, '2026-11-12', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(1, '2026-11-13', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(2, '2026-11-13', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(3, '2026-11-13', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(4, '2026-11-13', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(5, '2026-11-13', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(6, '2026-11-13', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(7, '2026-11-13', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(8, '2026-11-13', 'CUTI', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Cuti tahunan', NOW(), NOW()),
(9, '2026-11-13', 'ALPHA', 'Reguler Pagi', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NULL, 0, NULL, NULL, NULL, NULL, NULL, 'Tidak hadir tanpa keterangan', NOW(), NOW()),
(10, '2026-11-13', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW()),
(11, '2026-11-13', 'ATTEND', 'Reguler Pagi', '07:45:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', '17:05:00', 1, NULL, 'Surabaya Karah', '-7.3244677', '112.7550714', 'Jl. Ahmad Yani No. 88, Surabaya', 'Hadir tepat waktu', NOW(), NOW());

SET FOREIGN_KEY_CHECKS=1;

-- Row summary: users 12, employees 11, master_assessments 3, transaction_360 48,
-- m_menu 15, fingerprints 3, akses_respo 2, outstanding_approvals 3,
-- branches 17, subcompanies 10, presensi_absensi 110.
