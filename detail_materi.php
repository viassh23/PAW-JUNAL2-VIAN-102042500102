<?php
require 'koneksi.php';
$id = isset($_GET['id']) && ctype_digit((string)$_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare('SELECT m.*, mk.kode AS kode_mk, mk.nama AS nama_mk
                        FROM materi m JOIN mata_kuliah mk ON m.mata_kuliah_id = mk.id
                        WHERE m.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Materi - LMS EAD</title>
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
        <a href="daftar_materi.php" class="aktif">Materi</a>
        <a href="daftar_tugas.php">Tugas</a>
    </nav>

    <?php if (!$data): ?>
        <div class="kesalahan">Data materi tidak ditemukan.</div>
        <p><a class="tombol tombol-sekunder" href="daftar_materi.php">Kembali</a></p>
    <?php else: ?>
        <p class="label-kecil">Materi</p>
        <h2><?= h($data['judul']) ?></h2>
        <dl>
            <dt>Mata kuliah</dt><dd><?= h($data['kode_mk']) ?> - <?= h($data['nama_mk']) ?></dd>
            <dt>Judul materi</dt><dd><?= h($data['judul']) ?></dd>
            <dt>Isi materi</dt><dd><?= h($data['isi']) ?></dd>
            <dt>Tautan referensi</dt>
            <dd><a href="<?= h($data['tautan']) ?>" target="_blank" rel="noopener noreferrer"><?= h($data['tautan']) ?></a></dd>
        </dl>
        <a class="tombol" href="edit_materi.php?id=<?= (int)$data['id'] ?>">Edit data</a>
        <a class="tombol tombol-sekunder" href="daftar_materi.php">Kembali</a>
    <?php endif; ?>
</main>
</body>
</html>