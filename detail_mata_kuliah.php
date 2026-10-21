<?php
require 'koneksi.php';
$id = isset($_GET['id']) && ctype_digit((string)$_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare('SELECT * FROM mata_kuliah WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Mata Kuliah - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <p class="label-kecil">PRAKTIKUM PAW / JURNAL 2</p>
    <h1>Learning Management System</h1>
    <p class="subjudul">Pengelolaan mata kuliah, materi pembelajaran, dan tugas.</p>

    <nav class="nav-bar">
        <a href="index.php">Beranda</a>
        <a href="daftar_mata_kuliah.php" class="aktif">Mata kuliah</a>
        <a href="daftar_materi.php">Materi</a>
        <a href="daftar_tugas.php">Tugas</a>
    </nav>

    <?php if (!$data): ?>
        <div class="kesalahan">Data mata kuliah tidak ditemukan.</div>
        <p><a class="tombol tombol-sekunder" href="daftar_mata_kuliah.php">Kembali</a></p>
    <?php else: ?>
        <h2>Detail Mata Kuliah</h2>
        <dl>
            <dt>Kode Mata Kuliah</dt><dd><?= h($data['kode']) ?></dd>
            <dt>Nama Mata Kuliah</dt><dd><?= h($data['nama']) ?></dd>
            <dt>Dosen Pengampu</dt><dd><?= h($data['dosen']) ?></dd>
            <dt>Semester</dt><dd><?= (int)$data['semester'] ?></dd>
        </dl>
        <a class="tombol" href="edit_mata_kuliah.php?id=<?= (int)$data['id'] ?>">Edit data</a>
        <a class="tombol tombol-sekunder" href="daftar_mata_kuliah.php">Kembali</a>
    <?php endif; ?>
</main>
</body>
</html>