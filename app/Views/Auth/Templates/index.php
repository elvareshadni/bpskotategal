<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        
        .forgot-password {
            color: #0050B8;
            text-decoration: none;
            font-size: 0.9rem;
            display: block;
            text-align: center;
            margin: 10px 0 20px 0;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
            color: #0050B8;
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

<?= $this->renderSection('content'); ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>