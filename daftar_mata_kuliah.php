<?php
require 'koneksi.php';

$cari = trim($_GET['cari'] ?? '');
$like = '%' . $cari . '%';
$stmt = $conn->prepare('SELECT * FROM mata_kuliah WHERE kode LIKE ? OR nama LIKE ? ORDER BY kode ASC');
$stmt->bind_param('ss', $like, $like);
$stmt->execute();
$hasil = $stmt->get_result();

$pesan_sukses = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Mata Kuliah - LMS EAD</title>
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

    <?php if ($pesan_sukses !== ''): ?>
        <div class="pesan"><?= h($pesan_sukses) ?></div>
    <?php endif; ?>

    <h2>Daftar Mata Kuliah</h2>
    <p><a class="tombol" href="tambah_mata_kuliah.php">Tambah mata kuliah</a></p>

    <form method="get" class="form-filter">
        <div>
            <label for="cari">Cari kode atau nama mata kuliah</label>
            <input id="cari" name="cari" value="<?= h($cari) ?>" placeholder="Contoh: PAW atau Algoritma">
        </div>
        <button type="submit">Cari</button>
        <a class="tombol tombol-sekunder" href="daftar_mata_kuliah.php">Reset</a>
    </form>

    <p>Menampilkan <?= $hasil->num_rows ?> data.</p>

    <?php if ($hasil->num_rows === 0): ?>
        <p>Data tidak ditemukan.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Mata kuliah</th>
                    <th>Dosen / Semester</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $hasil->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= h($row['kode']) ?></strong> - <?= h($row['nama']) ?></td>
                        <td><?= h($row['dosen']) ?> / Semester <?= (int)$row['semester'] ?></td>
                        <td class="aksi-link">
                            <a href="detail_mata_kuliah.php?id=<?= (int)$row['id'] ?>">Detail</a>
                            <a href="edit_mata_kuliah.php?id=<?= (int)$row['id'] ?>">Edit</a>
                            <a href="hapus_mata_kuliah.php?id=<?= (int)$row['id'] ?>">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
</body>
</html>