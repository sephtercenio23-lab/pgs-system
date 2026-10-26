<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Graduate School Portal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= url('/static/app.css') ?>" rel="stylesheet">
</head>
<body class="page-login">
    <div class="login-page">
        <div class="login-card">
            <div class="login-logo">
                <img src="<?= url('/static/pgs_logo.png') ?>" alt="PGS Logo" onerror="this.style.display='none'">
                <h1>Graduate School Portal</h1>
                <p>Panpacific University</p>
            </div>

            <?php $messages = get_flashed_messages(); ?>
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as list($category, $message)): ?>
                    <div class="alert alert-<?= h(pv($category)) ?>">
                        <i class="fas fa-<?= $category == 'success' ? 'check-circle' : ($category == 'warning' ? 'exclamation-triangle' : 'times-circle') ?>"></i>
                        <?= h(pv($message)) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <form method="POST" action="<?= url('/login') ?>">
                <div class="form-group">
                    <label>Username</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" class="form-control" placeholder="Enter username" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-icon-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="login-footer">
                <p>Default: <code>admin / admin123</code></p>
            </div>
        </div>
    </div>
</body>
</html>
