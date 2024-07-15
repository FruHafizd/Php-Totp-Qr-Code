<?php
require_once 'vendor/autoload.php';

use lfkeitel\phptotp\{Base32, Totp};

// Mulai session PHP
session_start();

// Inisialisasi encodedSecret dari session jika sudah ada, atau generate baru jika belum
if (isset($_SESSION['encodedSecret'])) {
    $encodedSecret = $_SESSION['encodedSecret'];
} else {
    // Generate TOTP Secret Key
    $secret = Totp::GenerateSecret(16);
    
    // Encode the secret key for display to the user
    $encodedSecret = Base32::encode($secret);
    
    // Simpan encodedSecret ke dalam session
    $_SESSION['encodedSecret'] = $encodedSecret;
}

// Prepare issuer and label for OTPAuth URL
$issuer = 'TOTP';
$label = 'Nama_Pengguna';

// Generate OTPAuth URL secara dinamis
$otpauthURL = 'otpauth://totp/' . rawurlencode($issuer) . ':' . rawurlencode($label) . '?secret=' . $encodedSecret . '&issuer=' . rawurlencode($issuer);

// Create TOTP instance dengan interval 30 detik
$totp = new Totp('sha1', 0, 30);

// Generate the current TOTP key untuk ditampilkan (opsional)
$currentKey = $totp->GenerateToken(Base32::decode($encodedSecret)); // Decode the encoded secret here

// Inisialisasi hasil verifikasi
$verificationResult = '';

// Handle form submission untuk verifikasi kunci TOTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userKey = $_POST['totp_key'];
    
    // Verifikasi kunci TOTP yang dimasukkan oleh pengguna
    if ($userKey === $currentKey) {
        $verificationResult = 'Kunci TOTP valid.';
    } else {
        $verificationResult = 'Kunci TOTP tidak valid.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>TOTP QR Code</title>
    <!-- Sesuaikan path ke QRCode.js yang telah Anda clone -->
    <script src="qrcodejs/qrcode.min.js"></script>
</head>
<body>
    <div id="qrcode"></div>
    <br>
    <p>Secret Key (to be entered in Google Authenticator or Authy): <?php echo $encodedSecret; ?></p>
    <p>Current TOTP Key: <?php echo isset($currentKey) ? $currentKey : ''; ?></p>

    <form method="post" action="">
        <label for="totp_key">Masukkan Kunci TOTP:</label>
        <input type="text" id="totp_key" name="totp_key" required>
        <button type="submit">Verifikasi</button>
    </form>

    <p><?php echo $verificationResult; ?></p>

    <script>
        // Generate QR Code based on OTPAuth URL obtained from PHP
        var otpauthURL = '<?php echo $otpauthURL; ?>';
        
        // Initialize QRCode.js and generate QR code
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: otpauthURL,
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H // Sesuaikan level koreksi kesalahan seperti yang dibutuhkan
        });
    </script>
</body>
</html>
