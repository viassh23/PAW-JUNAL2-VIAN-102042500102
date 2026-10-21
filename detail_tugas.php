<?php
require 'koneksi.php';
$id = isset($_GET['id']) && ctype_digit((string)$_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare('SELECT t.*, mk.kode AS kode_mk, mk.nama AS nama_mk
                        FROM tugas t JOIN mata_kuliah mk ON t.mata_kuliah_id = mk.id
                        WHERE t.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Tugas - LMS EAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <p class="label-kecil">PRAKTIKUM PAW / JURNAL 2</p>
    <h1>Learning Management System</h1>
    <p class="subjudul">Pengelolaan mata kuliah, materi pembelajaran, dan tugas.</p>

    <nav class="nav-bar">
        <a href="index.php">Beranda</a>
        <a href="daftar_mata_kuliah.php">Mata kuliah</a>
        <a href="daftar_materi.php">Materi</a>
        <a href="daftar_tugas.php" class="aktif">Tugas</a>
    </nav>

    <?php if (!$data): ?>
        <div class="kesalahan">Data tugas tidak ditemukan.</div>
        <p><a class="tombol tombol-sekunder" href="daftar_tugas.php">Kembali</a></p>
    <?php else: ?>
        <p class="label-kecil">Tugas</p>
        <h2><?= h($data['judul']) ?></h2>
        <dl>
            <dt>Mata kuliah</dt><dd><?= h($data['kode_mk']) ?> - <?= h($data['nama_mk']) ?></dd>
            <dt>Judul tugas</dt><dd><?= h($data['judul']) ?></dd>
            <dt>Instruksi tugas</dt><dd><?= h($data['instruksi']) ?></dd>
            <dt>Tenggat</dt><dd><?= h($data['tenggat']) ?></dd>
            <dt>Status</dt><dd><span class="badge"><?= h($data['status']) ?></span></dd>
        </dl>
        <a class="tombol" href="edit_tugas.php?id=<?= (int)$data['id'] ?>">Edit data</a>
        <a class="tombol tombol-sekunder" href="daftar_tugas.php">Kembali</a>
    <?php endif; ?>
</main>
</body>
</html>