<?php
require 'koneksi.php';
$error = '';
$mk_id = 0;
$judul = $isi = $tautan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!cek_token($_POST['token'] ?? '')) {
        $error = 'Token keamanan tidak valid.';
    } else {
        $mk_id  = isset($_POST['mata_kuliah_id']) && ctype_digit((string)$_POST['mata_kuliah_id']) ? (int)$_POST['mata_kuliah_id'] : 0;
        $judul  = trim($_POST['judul'] ?? '');
        $isi    = trim($_POST['isi'] ?? '');
        $tautan = trim($_POST['tautan'] ?? '');

        $stmt_mk = $conn->prepare('SELECT id FROM mata_kuliah WHERE id = ?');
        $stmt_mk->bind_param('i', $mk_id);
        $stmt_mk->execute();
        $mk_valid = $stmt_mk->get_result()->num_rows > 0;

        $protokol_valid = preg_match('/^https?:\/\/.+/i', $tautan) && filter_var($tautan, FILTER_VALIDATE_URL);

        if (!$mk_valid) {
            $error = 'Pilih mata kuliah yang valid dari daftar.';
        } elseif (mb_strlen($judul) < 3 || mb_strlen($judul) > 150) {
            $error = 'Judul materi wajib berisi 3 sampai 150 karakter.';
        } elseif (mb_strlen($isi) < 3 || mb_strlen($isi) > 2000) {
            $error = 'Isi materi wajib berisi 3 sampai 2000 karakter.';
        } elseif (mb_strlen($tautan) > 255 || !$protokol_valid) {
            $error = 'Gunakan tautan http/https yang valid (maksimal 255 karakter).';
        } else {
            $stmt = $conn->prepare('INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('isss', $mk_id, $judul, $isi, $tautan);
            $stmt->execute();
            $_SESSION['pesan'] = 'Materi pembelajaran berhasil ditambahkan.';
            header('Location: daftar_materi.php');
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
    <title>Tambah Materi - LMS EAD</title>
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

    <h2>Tambah Materi</h2>
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

        <label for="judul">Judul materi (3-150 karakter)</label>
        <input id="judul" name="judul" maxlength="150" value="<?= h($judul) ?>">

        <label for="isi">Isi materi (3-2000 karakter)</label>
        <textarea id="isi" name="isi" maxlength="2000"><?= h($isi) ?></textarea>

        <label for="tautan">Tautan referensi (diawali http:// atau https://)</label>
        <input id="tautan" name="tautan" maxlength="255" value="<?= h($tautan) ?>" placeholder="https://...">

        <button type="submit">Simpan data</button>
        <a class="tombol tombol-sekunder" href="daftar_materi.php">Batal</a>
    </form>
</main>
</body>
</html>