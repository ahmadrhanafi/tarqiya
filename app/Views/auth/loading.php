<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarqiya | Web App Monitoring Hafalan Qur'an</title>
    <link rel="shortcut icon" href="<?= base_url('assets/img/mainicon.png') ?>" type="image/png">

    <style>
        body {
            margin: 0;
            background: rgba(0, 128, 128, 0.38);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .logo-load {
            width: 150px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.9);
                opacity: 0.7;
            }

            50% {
                transform: scale(1.1);
                opacity: 1;
            }

            100% {
                transform: scale(0.9);
                opacity: 0.7;
            }
        }
    </style>
</head>

<body>
    <img src="<?= base_url('assets/img/mainicon.png') ?>" class="logo-load" alt="Logo Tarqiya">

    <script>
        const userRole = "<?= session()->get('role') ?>";

        // Default target jika admin tenant
        let targetUrl = "<?= base_url('admin/dashboard') ?>";

        // Periksa role lainnya secara spesifik
        if (userRole === 'superadmin') {
            targetUrl = "<?= base_url('superadmin/dashboard') ?>";
        } else if (userRole === 'guru') {
            targetUrl = "<?= base_url('guru/dashboard') ?>";
        } else if (userRole === 'wali') {
            targetUrl = "<?= base_url('wali/dashboard') ?>";
        }

        // Redirect otomatis ke dashboard setelah 2.5 detik sesuai role
        setTimeout(function () {
            window.location.href = targetUrl;
        }, 2500);
    </script>
</body>

</html>