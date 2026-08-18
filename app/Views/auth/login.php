<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Hudfal Information</title>
    <link rel="shortcut icon" href="<?= base_url('mainicon.png') ?>" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('<?= base_url('assets/img/bg_login.png') ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            /* Menghitamkan background (angka 0.4 adalah tingkat gelapnya, bisa diubah dari 0.1 sampai 0.9) */
            background-color: rgba(0, 0, 0, 0.4);
            background-blend-mode: overlay;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding-left: 8%;
            margin: 0;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .btn-custom {
            background-color: #1e970e;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background-color: #156d0a;
            color: white;
            transform: translateY(-2px);
        }

        .form-control {
            border-radius: 10px;
            padding: 12px;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            border-color: #1e970e;
            box-shadow: 0 0 0 0.25rem rgba(30, 151, 14, 0.25);
        }

        .input-group .btn {
            border-left: none;
            background: white;
            color: #6c757d;
        }

        .input-group .btn:hover {
            background: #f8f9fa;
            color: #1e970e;
        }

        .input-group .form-control:focus+.btn {
            border-color: #1e970e;
        }

        .text-green {
            color: #1e970e !important;
        }

        .text-muted-gray {
            color: #6c757d !important;
        }

        @media (max-width: 768px) {
            body {
                justify-content: center !important;
                padding-left: 15px !important;
                padding-right: 15px !important;
            }

            .login-card {
                width: 100% !important;
                max-width: 420px !important;
                padding: 30px !important;
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid p-0">
        <div class="login-card">
            <div class="text-center mb-4">
                <h3 class="fw-bold" style="color: #1e970e;">Selamat Datang</h3>
                <p class="text-muted">Silahkan Login ke Hudfal Information</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div id="flash-alert"
                    class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 d-flex align-items-center p-3 mb-4"
                    role="alert">
                    <div class="flex-grow-1">
                        <span class="fw-bold d-block text-danger mb-0" style="font-size: 14px;">
                            <i class="fa fa-exclamation-triangle me-2"></i>Login Gagal!
                        </span>
                        <span class="text-secondary small"
                            style="font-size: 10px;"><?= session()->getFlashdata('error') ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/process') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.8rem; color: #1e970e;">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold" style="font-size: 0.8rem; color: #1e970e;">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••"
                            required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                            style="border-color: #ddd;">
                            <i class="fa-solid fa-eye text-muted-gray" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Info Lupa Password diarahkan ke WhatsApp Admin -->
                <div class="d-flex justify-content-start mb-4">
                    <a href="https://wa.me/6285774109738?text=Halo%20Admin,%20saya%20lupa%20password%20akun%20saya%20di%20Sistem%20Monitoring%20Hafalan.%20Mohon%20bantuannya%20untuk%20reset%20password.%20Terima%20kasih."
                        target="_blank" class="text-decoration-none text-muted" style="font-size: 11px;">
                        Lupa password? <b>Hubungi Admin via</b> <span class="text-green fw-bold"><i
                                class="fa-brands fa-whatsapp"></i> WhatsApp</span>
                    </a>
                </div>

                <button type="submit" class="btn btn-custom w-100 shadow-sm">Login Sekarang</button>
            </form>
        </div>
    </div>

    <script>
        // Alert Duration
        window.setTimeout(function () {
            const alertElement = document.getElementById('flash-alert');
            if (alertElement) {
                alertElement.style.transition = "opacity 0.5s ease";
                alertElement.style.opacity = "0";

                window.setTimeout(function () {
                    alertElement.remove();
                }, 500);
            }
        }, 3000);

        // Toggle Eye Icon
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');

            if (type === 'text') {
                eyeIcon.classList.remove('text-muted-gray');
                eyeIcon.classList.add('text-green');
            } else {
                eyeIcon.classList.remove('text-green');
                eyeIcon.classList.add('text-muted-gray');
            }
        });
    </script>
</body>

</html>