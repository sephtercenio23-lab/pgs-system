<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?= h(pv($error_code)) ?> | Graduate School</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= url('/static/app.css') ?>" rel="stylesheet">
</head>
<body class="page-error">
    <div class="error-page">
        <div class="error-container">
            <h1 class="error-code"><?= h(pv($error_code)) ?></h1>
            <p class="error-message"><?= h(pv($error_message)) ?></p>
            <a href="<?= url('/') ?>" class="btn btn-primary">
                <i class="fas fa-home"></i> Go Home
            </a>
        </div>
    </div>
</body>
</html>
