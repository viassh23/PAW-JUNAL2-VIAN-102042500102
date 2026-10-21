CREATE DATABASE IF NOT EXISTS paw_jurnal2_lms
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE paw_jurnal2_lms;

-- 1. Tabel Induk: mata_kuliah
CREATE TABLE mata_kuliah (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    dosen VARCHAR(100) NOT NULL,
    semester TINYINT UNSIGNED NOT NULL
);

-- 2. Tabel: materi
CREATE TABLE materi (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    isi TEXT NOT NULL,
    tautan VARCHAR(255) NOT NULL,
    FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE RESTRICT
);

-- 3. Tabel: tugas
CREATE TABLE tugas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    mata_kuliah_id INT UNSIGNED NOT NULL,
    judul VARCHAR(150) NOT NULL,
    instruksi TEXT NOT NULL,
    tenggat DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE RESTRICT
);

-- Data Awal Mata Kuliah
INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES
('ALP-101', 'Algoritma dan Pemrograman', 'Dosen ALP', 1),
('PAW-101', 'Pengembangan Aplikasi Website', 'Tiara Rahmania Hadiningrum', 3);

-- Data Awal Materi
INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES
(1, 'Pengenalan Logika dan Flowchart', 'Materi ini membahas konsep dasar algoritma, pseudocode, serta pembuatan diagram alir untuk pemecahan masalah.', 'https://www.php.net/manual/en/langref.php'),
(2, 'Operasi CRUD PHP dan MySQLi', 'Materi membahas cara menghubungkan aplikasi web PHP dengan database MySQL menggunakan prepared statement.', 'https://www.php.net/manual/en/book.mysqli.php');

-- Data Awal Tugas
INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES
(1, 'Latihan Percabangan If-Else', 'Buatlah algoritma dan kode program studi kasus kasir sederhana dengan struktur kondisi percabangan.', '2026-10-25', 'Aktif'),
(2, 'Jurnal 2 Pembuatan Aplikasi LMS', 'Bangun aplikasi LMS pengelolaan mata kuliah, materi, dan tugas menggunakan PHP Native dan MySQL.', '2026-10-30', 'Draf');