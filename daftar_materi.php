<?php
require 'koneksi.php';

$cari  = trim($_GET['cari'] ?? '');
$mk_id = isset($_GET['mk_id']) && ctype_digit((string)$_GET['mk_id']) ? (int)$_GET['mk_id'] : 0;
$like  = '%' . $cari . '%';

$stmt_mk = $conn->prepare('SELECT id, kode, nama FROM mata_kuliah ORDER BY kode ASC');
$stmt_mk->execute();
$list_mk = $stmt_mk->get_result();

if ($mk_id > 0) {
    $stmt = $conn->prepare('SELECT m.*, mk.kode AS kode_mk, mk.nama AS nama_mk
                            FROM materi m JOIN mata_kuliah mk ON m.mata_kuliah_id = mk.id
                            WHERE m.judul LIKE ? AND m.mata_kuliah_id = ?
                            ORDER BY m.id DESC');
    $stmt->bind_param('si', $like, $mk_id);
} else {
    $stmt = $conn->prepare('SELECT m.*, mk.kode AS kode_mk, mk.nama AS nama_mk
                            FROM materi m JOIN mata_kuliah mk ON m.mata_kuliah_id = mk.id
                            WHERE m.judul LIKE ?
                            ORDER BY m.id DESC');
    $stmt->bind_param('s', $like);
}
$stmt->execute();
$hasil = $stmt->get_result();

$pesan_sukses = $_SESSION['pesan'] ?? '';
unset($_SESSION['pesan']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Materi - LMS EAD</title>
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

    <?php if ($pesan_sukses !== ''): ?>
        <div class="pesan"><?= h($pesan_sukses) ?></div>
    <?php endif; ?>

    <h2>Materi</h2>
    <p><a class="tombol" href="tambah_materi.php">Tambah materi</a></p>

    <form method="get" class="form-filter">
        <div>
            <label for="cari">Cari judul materi</label>
            <input id="cari" name="cari" value="<?= h($cari) ?>">
        </div>
        <div>
            <label for="mk_id">Filter mata kuliah</label>
            <select id="mk_id" name="mk_id">
                <option value="0">Semua mata kuliah</option>
                <?php while ($mk = $list_mk->fetch_assoc()): ?>
                    <option value="<?= (int)$mk['id'] ?>" <?= $mk_id === (int)$mk['id'] ? 'selected' : '' ?>>
                        <?= h($mk['kode']) ?> - <?= h($mk['nama']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit">Cari</button>
        <a class="tombol tombol-sekunder" href="daftar_materi.php">Reset</a>
    </form>

    <p>Menampilkan <?= $hasil->num_rows ?> data.</p>

    <?php if ($hasil->num_rows === 0): ?>
        <p>Data tidak ditemukan.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Mata kuliah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $hasil->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= h($row['judul']) ?></strong></td>
                        <td><?= h($row['kode_mk']) ?> - <?= h($row['nama_mk']) ?></td>
                        <td class="aksi-link">
                            <a href="detail_materi.php?id=<?= (int)$row['id'] ?>">Detail</a>
                            <a href="edit_materi.php?id=<?= (int)$row['id'] ?>">Edit</a>
                            <a href="hapus_materi.php?id=<?= (int)$row['id'] ?>">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>
</body>
</html>