<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log | Graduate School</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= url('/static/app.css') ?>" rel="stylesheet">
</head>
<body class="page-audit">
    <div class="app-body">
        <?php $active = 'audit'; include __DIR__ . '/_sidebar.php'; ?>

        <div class="main">
            <header class="topbar">
                <button class="sidebar-toggle" onclick="App.toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div class="titles">
                    <h1><i class="fas fa-history" style="color: var(--brand);"></i> Audit Log</h1>
                    <p class="subtitle">Latest system activity (last 100 entries).</p>
                </div>
            </header>

            <div class="content-container">
                <div class="card" style="padding: 8px 0;">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>Student ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__n1 = 0; foreach ($logs as $log): $__n1++; ?>
                                <tr>
                                    <td class="timestamp"><?= h(pv(isset($log['timestamp']) ? $log['timestamp'] : null)) ?></td>
                                    <td class="cell-name"><strong><?= h(pv(isset($log['user']) ? $log['user'] : null)) ?></strong></td>
                                    <td class="action-<?= h(strtolower(explode('_', $log['action'])[0])) ?>">
                                        <?= h(pv(isset($log['action']) ? $log['action'] : null)) ?>
                                    </td>
                                    <td><?= h(pv(isset($log['details']) ? $log['details'] : null)) ?></td>
                                    <td><?= h(truthy(isset($log['student_id']) ? $log['student_id'] : null) ? $log['student_id'] : '-') ?></td>
                                </tr>
                                <?php endforeach; if ($__n1 === 0): ?>
                                <tr>
                                    <td colspan="5" class="table-empty">
                                        <i class="fas fa-clipboard-list"></i>
                                        No audit records found.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= url('/static/app.js') ?>"></script>
</body>
</html>
