<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error <?= h(pv($error_code)) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .error-container {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            color: #3b82f6;
            margin: 0;
        }
        .error-message {
            color: #64748b;
            margin: 1rem 0 2rem;
        }
        .btn-home {
            display: inline-block;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code"><?= h(pv($error_code)) ?></h1>
        <p class="error-message"><?= h(pv($error_message)) ?></p>
        <a href="<?= url('/') ?>" class="btn-home">
            <i class="fas fa-home"></i> Go Home
        </a>
    </div>
</body>
</html>