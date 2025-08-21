<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .auth-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-width: 800px;
            width: 100%;
        }
        
        .auth-left {
            background: linear-gradient(135deg, #0050B8, #1262cbff);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            min-height: 500px;
        }
        
        .auth-right {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .auth-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .auth-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 30px;
            line-height: 1.5;
        }
        
        .form-control {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 1rem;
            margin-bottom: 20px;
        }
        
        .form-control:focus {
            border-color: #0050B8;
            box-shadow: 0 0 0 0.2rem rgba(255, 149, 0, 0.25);
        }
        
        .btn-primary {
            background: #0050B8;
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
        }
        
        .btn-primary:hover {
            background: #0050B8;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid white;
            border-radius: 8px;
            color: white;
            padding: 12px 30px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-outline:hover {
            background: white;
            color: #0050B8;
            text-decoration: none;
        }
        
        .form-title {
            color: #0050B8;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 40px;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .auth-left, .auth-right {
                padding: 40px 30px;
            }
            
            .auth-title {
                font-size: 1.8rem;
            }
            
            .form-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="auth-card">
            <div class="row g-0">
                <div class="col-lg-6">
                    <div class="auth-left">
                        <div>
                            <h1 class="auth-title">Selamat Datang Kembali!</h1>
                            <p class="auth-subtitle">untuk tetap terhubung dengan kami, silakan login dengan info pribadi Anda</p>
                            <a href="login.html" class="btn-outline">Masuk</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="auth-right">
                        <h2 class="form-title">Daftar</h2>
                        <form>
                            <input type="email" class="form-control" placeholder="E-mail" required>
                            <input type="text" class="form-control" placeholder="Nama" required>
                            <input type="password" class="form-control" placeholder="Password" required>
                            <button type="submit" class="btn btn-primary">Daftar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>