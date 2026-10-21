<?php
require 'koneksi.php';
$error = '';
$mk_id     = 0;
$judul     = $instruksi = $tenggat = '';
$status    = 'Draf';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!cek_token($_POST['token'] ?? '')) {
        $error = 'Token keamanan tidak valid.';
    } else {
        $mk_id     = isset($_POST['mata_kuliah_id']) && ctype_digit((string)$_POST['mata_kuliah_id']) ? (int)$_POST['mata_kuliah_id'] : 0;
        $judul     = trim($_POST['judul'] ?? '');
        $instruksi = trim($_POST['instruksi'] ?? '');
        $tenggat   = trim($_POST['tenggat'] ?? '');
        $status    = trim($_POST['status'] ?? '');

        $stmt_mk = $conn->prepare('SELECT id FROM mata_kuliah WHERE id = ?');
        $stmt_mk->bind_param('i', $mk_id);
        $stmt_mk->execute();
        $mk_valid = $stmt_mk->get_result()->num_rows > 0;

        $tgl_valid = false;
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $tenggat, $m)) {
            $tgl_valid = checkdate((int)$m[2], (int)$m[3], (int)$m[1]);
        }

        $status_diizinkan = ['Draf', 'Aktif', 'Selesai'];

        if (!$mk_valid) {
            $error = 'Pilih mata kuliah yang valid dari daftar.';
        } elseif (mb_strlen($judul) < 3 || mb_strlen($judul) > 150) {
            $error = 'Judul tugas wajib berisi 3 sampai 150 karakter.';
        } elseif (mb_strlen($instruksi) < 3 || mb_strlen($instruksi) > 2000) {
            $error = 'Instruksi tugas wajib berisi 3 sampai 2000 karakter.';
        } elseif (!$tgl_valid) {
            $error = 'Tanggal tenggat tidak valid.';
        } elseif (!in_array($status, $status_diizinkan, true)) {
            $error = 'Status tugas hanya boleh bernilai Draf, Aktif, atau Selesai.';
        } else {
            $stmt = $conn->prepare('INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('issss', $mk_id, $judul, $instruksi, $tenggat, $status);
            $stmt->execute();
            $_SESSION['pesan'] = 'Tugas baru berhasil ditambahkan.';
            header('Location: daftar_tugas.php');
            exit;
        }
    }
}

$stmt_list = $conn->prepare('SELECT id, kode, nama FROM mata_kuliah ORDER BY kode ASC');
$stmt_list->execute();
$list_mk = $stmt_list->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Tugas - LMS EAD</title>
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

    <h2>Tambah Tugas</h2>
    <?php if ($error !== ''): ?>
        <div class="kesalahan"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="token" value="<?= h($_SESSION['token']) ?>">

        <label for="mata_kuliah_id">Mata kuliah</label>
        <select id="mata_kuliah_id" name="mata_kuliah_id">
            <option value="0">Pilih Mata kuliah</option>
            <?php while ($mk = $list_mk->fetch_assoc()): ?>
                <option value="<?= (int)$mk['id'] ?>" <?= $mk_id === (int)$mk['id'] ? 'selected' : '' ?>>
                    <?= h($mk['kode']) ?> - <?= h($mk['nama']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="judul">Judul tugas (3-150 karakter)</label>
        <input id="judul" name="judul" maxlength="150" value="<?= h($judul) ?>">

        <label for="instruksi">Instruksi tugas (3-2000 karakter)</label>
        <textarea id="instruksi" name="instruksi" maxlength="2000"><?= h($instruksi) ?></textarea>

        <label for="tenggat">Tenggat</label>
        <input id="tenggat" type="date" name="tenggat" value="<?= h($tenggat) ?>">

        <label for="status">Status</label>
        <select id="status" name="status">
            <?php foreach (['Draf', 'Aktif', 'Selesai'] as $st): ?>
                <option value="<?= $st ?>" <?= $status === $st ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Simpan data</button>
        <a class="tombol tombol-sekunder" href="daftar_tugas.php">Batal</a>
    </form>
</main>
</body>
</html>