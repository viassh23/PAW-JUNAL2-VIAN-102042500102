<?php
require 'koneksi.php';
$id = isset($_GET['id']) && ctype_digit((string)$_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

$stmt = $conn->prepare('SELECT * FROM materi WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die('Data materi tidak ditemukan.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!cek_token($_POST['token'] ?? '')) {
        $error = 'Token keamanan tidak valid.';
    } else {
        $stmt = $conn->prepare('DELETE FROM materi WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $_SESSION['pesan'] = 'Data materi berhasil dihapus.';
        header('Location: daftar_materi.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Materi - LMS EAD</title>
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

    <h2>Hapus Materi</h2>
    <?php if ($error !== ''): ?>
        <div class="kesalahan"><?= h($error) ?></div>
    <?php endif; ?>

    <p>Hapus materi <strong><?= h($data['judul']) ?></strong>?</p>
    <p>Data baru dihapus setelah tombol konfirmasi dikirim.</p>

    <form method="post">
        <input type="hidden" name="token" value="<?= h($_SESSION['token']) ?>">
        <button type="submit" class="bahaya">Ya, hapus data</button>
        <a class="tombol tombol-sekunder" href="daftar_materi.php">Batal</a>
    </form>
</main>
</body>
</html>