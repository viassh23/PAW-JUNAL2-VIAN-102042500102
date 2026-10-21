<?php
require 'koneksi.php';
$id = isset($_GET['id']) && ctype_digit((string)$_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

$stmt = $conn->prepare('SELECT * FROM materi WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$lama = $stmt->get_result()->fetch_assoc();

if (!$lama) {
    die('Data materi tidak ditemukan.');
}

$mk_id  = (int)$lama['mata_kuliah_id'];
$judul  = $lama['judul'];
$isi    = $lama['isi'];
$tautan = $lama['tautan'];

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
            if ((int)$lama['mata_kuliah_id'] === $mk_id && $lama['judul'] === $judul && $lama['isi'] === $isi && $lama['tautan'] === $tautan) {
                $_SESSION['pesan'] = 'Tidak ada perubahan data materi yang disimpan.';
            } else {
                $stmt = $conn->prepare('UPDATE materi SET mata_kuliah_id = ?, judul = ?, isi = ?, tautan = ? WHERE id = ?');
                $stmt->bind_param('isssi', $mk_id, $judul, $isi, $tautan, $id);
                $stmt->execute();
                $_SESSION['pesan'] = 'Data materi berhasil diperbarui.';
            }
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
    <title>Edit Materi - LMS EAD</title>
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

    <h2>Edit Materi</h2>
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

        <label for="judul">Judul materi</label>
        <input id="judul" name="judul" maxlength="150" value="<?= h($judul) ?>">

        <label for="isi">Isi materi</label>
        <textarea id="isi" name="isi" maxlength="2000"><?= h($isi) ?></textarea>

        <label for="tautan">Tautan referensi</label>
        <input id="tautan" name="tautan" maxlength="255" value="<?= h($tautan) ?>">

        <button type="submit">Simpan perubahan</button>
        <a class="tombol tombol-sekunder" href="daftar_materi.php">Batal</a>
    </form>
</main>
</body>
</html>