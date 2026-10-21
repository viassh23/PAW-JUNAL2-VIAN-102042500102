<?php
session_start();

mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect('127.0.0.1', 'root', '', 'paw_jurnal2_lms');
if (!$conn) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

function h($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

function cek_token($token_input) {
    return isset($_SESSION['token']) && is_string($token_input) && hash_equals($_SESSION['token'], $token_input);
}