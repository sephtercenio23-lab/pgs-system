<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings | Graduate School</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= url('/static/app.css') ?>" rel="stylesheet">
</head>
<body class="page-settings">
    <div class="app-body">
        <?php $active = 'settings'; include __DIR__ . '/_sidebar.php'; ?>

        <div class="main">
            <header class="topbar">
                <button class="sidebar-toggle" onclick="App.toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div class="titles">
                    <h1>System Settings</h1>
                    <p class="subtitle">Backup, restore and maintenance of your records.</p>
                </div>
            </header>

            <div class="content-container">
                <div class="settings-container">
                    <?php $messages = get_flashed_messages(); ?>
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as list($category, $message)): ?>
                            <div class="alert alert-<?= h(pv($category)) ?>">
                                <i class="fas fa-<?= $category == 'success' ? 'check-circle' : ($category == 'warning' ? 'exclamation-triangle' : 'times-circle') ?>"></i>
                                <?= h(pv($message)) ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="card">
                        <h3><i class="fas fa-download" style="color: var(--info);"></i> Backup Data</h3>
                        <p style="color: var(--muted); font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                            Download a copy of the student database in <b>JSON format</b>. It is recommended to back up your records regularly to prevent data loss.
                        </p>
                        <a href="<?= url('/settings/backup') ?>" class="btn btn-download"><i class="fas fa-file-download"></i> Download Backup</a>
                    </div>

                    <div class="card">
                        <h3><i class="fas fa-upload" style="color: var(--success);"></i> Restore Data</h3>
                        <p style="color: var(--muted); font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                            Upload a previously saved JSON backup file. <b>Note:</b> This action will completely overwrite the current data in the system.
                        </p>
                        <form action="<?= url('/settings/restore') ?>" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="file" name="backup_file" required>
                            </div>
                            <button type="submit" class="btn btn-upload"><i class="fas fa-file-import"></i> Restore Database</button>
                        </form>
                    </div>

                    <div class="card danger-card">
                        <h3><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
                        <p style="color: var(--muted); font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                            This action will <b>delete all student records</b> and system data. Once wiped, the information cannot be recovered unless you have a backup.
                        </p>
                        <form action="<?= url('/settings/reset') ?>" method="POST" onsubmit="return confirm('SYSTEM RESET: Are you absolutely sure? This cannot be undone!')">
                            <button type="submit" class="btn btn-reset"><i class="fas fa-trash-alt"></i> Reset System Data</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= url('/static/app.js') ?>"></script>
</body>
</html>
