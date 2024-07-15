<?php

require_once 'vendor/autoload.php'; // Sesuaikan path ke autoload.php jika diperlukan

use lfkeitel\phptotp\{Base32, Totp};

// Ambil kunci rahasia yang sama dengan yang digunakan untuk membuat QR code
// Ganti ini dengan mekanisme yang sesuai untuk mengambil kunci rahasia
$secret = 'SIMPAN_KUNCI_RAHASIA_DI_SINI'; // Ganti dengan kunci rahasia yang sesuai

// Dekode kunci rahasia
$decodedSecret = Base32::decode($secret);

// Buat instance TOTP dengan interval 30 detik
$totp = new Totp('sha1', 0, 30);

// Hasilkan kunci TOTP saat ini
$currentKey = $totp->GenerateToken($decodedSecret);

// Kembalikan kunci TOTP saat ini dalam format JSON
echo json_encode(['totp_key' => $currentKey]);
?>
