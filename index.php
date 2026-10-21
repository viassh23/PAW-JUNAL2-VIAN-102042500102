<?php
require 'koneksi.php';

$jml_mk     = $conn->query('SELECT COUNT(*) AS c FROM mata_kuliah')->fetch_assoc()['c'];
$jml_materi = $conn->query('SELECT COUNT(*) AS c FROM materi')->fetch_assoc()['c'];
$jml_tugas  = $conn->query('SELECT COUNT(*) AS c FROM tugas')->fetch_assoc()['c'];

$stmt = $conn->prepare('SELECT id, kode, nama FROM mata_kuliah ORDER BY kode ASC');
$stmt->execute();
$list_mk = $stmt->get_result();

$pesan_sukses = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LMS EAD - Praktikum PAW Jurnal 2</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <p class="label-kecil">PRAKTIKUM PAW / JURNAL 2</p>
    <h1>Learning Management System</h1>
    <p class="subjudul">Pengelolaan mata kuliah, materi pembelajaran, dan tugas.</p>

    <nav class="nav-bar">
        <a href="index.php" class="aktif">Beranda</a>
        <a href="daftar_mata_kuliah.php">Mata kuliah</a>
        <a href="daftar_materi.php">Materi</a>
        <a href="daftar_tugas.php">Tugas</a>
    </nav>

    <?php if ($pesan_sukses !== ''): ?>
        <div class="pesan"><?= h($pesan_sukses) ?></div>
    <?php endif; ?>

    <div class="grid-ringkasan">
        <div class="kartu">
            <div class="label-kecil">Mata kuliah</div>
            <div class="angka"><?= (int)$jml_mk ?></div>
            <a href="daftar_mata_kuliah.php">Kelola mata kuliah &rarr;</a>
        </div>
        <div class="kartu">
            <div class="label-kecil">Materi</div>
            <div class="angka"><?= (int)$jml_materi ?></div>
            <a href="daftar_materi.php">Kelola materi &rarr;</a>
        </div>
        <div class="kartu">
            <div class="label-kecil">Tugas</div>
            <div class="angka"><?= (int)$jml_tugas ?></div>
            <a href="daftar_tugas.php">Kelola tugas &rarr;</a>
        </div>
    </div>

    <h2>Mata kuliah tersedia</h2>
    <?php if ($list_mk->num_rows === 0): ?>
        <p>Belum ada mata kuliah terdaftar.</p>
    <?php else: ?>
        <ul>
            <?php while ($mk = $list_mk->fetch_assoc()): ?>
                <li>
                    <strong><?= h($mk['kode']) ?> - <?= h($mk['nama']) ?></strong>
                    (<a href="daftar_materi.php?mk_id=<?= (int)$mk['id'] ?>">Lihat Materi</a> |
                     <a href="daftar_tugas.php?mk_id=<?= (int)$mk['id'] ?>">Lihat Tugas</a>)
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</main>
</body>
</html>