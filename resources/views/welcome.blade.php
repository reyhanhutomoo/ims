<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - IMS</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #89CFF0 0%, #4A90E2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Source Sans Pro', sans-serif;
        }
        .landing-container {
            max-width: 900px;
            width: 100%;
            padding: 20px;
        }
        .welcome-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .welcome-header {
            background: linear-gradient(135deg, #89CFF0 0%, #4A90E2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .welcome-header img {
            width: 100px;
            height: auto;
            margin-bottom: 20px;
        }
        .welcome-header h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .welcome-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .welcome-body {
            padding: 50px 40px;
        }
        .option-card {
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .option-card:hover {
            border-color: #4A90E2;
            box-shadow: 0 10px 30px rgba(74, 144, 226, 0.2);
            transform: translateY(-5px);
        }
        .option-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #89CFF0 0%, #4A90E2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .option-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }
        .option-description {
            color: #666;
            margin-bottom: 25px;
            flex-grow: 1;
        }
        .btn-option {
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-login {
            background: linear-gradient(135deg, #89CFF0 0%, #4A90E2 100%);
            color: white;
            border: none;
        }
        .btn-login:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(74, 144, 226, 0.4);
            color: white;
            text-decoration: none;
        }
        .btn-viewer {
            background: white;
            color: #4A90E2;
            border: 2px solid #4A90E2;
        }
        .btn-viewer:hover {
            background: #4A90E2;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(74, 144, 226, 0.4);
            text-decoration: none;
        }
        @media (max-width: 768px) {
            .welcome-header h1 {
                font-size: 1.5rem;
            }
            .welcome-body {
                padding: 30px 20px;
            }
            .option-card {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <div class="welcome-card">
            <div class="welcome-header">
                <img src="{{ asset('/images/divhub.png') }}" alt="IMS Logo">
                <h1>INTERNSHIP MANAGEMENT SYSTEM</h1>
                <p>Sistem Manajemen Magang Terintegrasi</p>
            </div>
            <div class="welcome-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="option-card">
                            <div>
                                <div class="option-icon">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <h3 class="option-title">Login</h3>
                                <p class="option-description">
                                    Masuk ke sistem untuk mengelola data magang, absensi, dan laporan mingguan. 
                                    Khusus untuk admin dan karyawan terdaftar.
                                </p>
                            </div>
                            <a href="{{ route('login') }}" class="btn btn-option btn-login">
                                <i class="fas fa-user-lock mr-2"></i>Masuk ke Sistem
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="option-card">
                            <div>
                                <div class="option-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <h3 class="option-title">Lihat sebagai Viewer</h3>
                                <p class="option-description">
                                    Akses publik untuk melihat informasi MoA/IA (Memorandum of Agreement/Internship Agreement). 
                                    Khusus untuk universitas dan mitra.
                                </p>
                            </div>
                            <a href="{{ route('public.moa.index') }}" class="btn btn-option btn-viewer">
                                <i class="fas fa-university mr-2"></i>Lihat MoA/IA
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>