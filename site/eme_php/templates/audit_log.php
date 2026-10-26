<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Log</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .btn-back {
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-collapse: collapse;
        }
        th {
            background: #1e293b;
            color: white;
            padding: 1rem;
            text-align: left;
        }
        td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:hover {
            background: #f8fafc;
        }
        .timestamp {
            color: #64748b;
            font-size: 0.875rem;
        }
        .action-add { color: #10b981; font-weight: 600; }
        .action-delete { color: #ef4444; font-weight: 600; }
        .action-update { color: #3b82f6; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-history"></i> Audit Log</h1>
            <a href="<?= url('/settings') ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        
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
                    <td><?= h(pv(isset($log['user']) ? $log['user'] : null)) ?></td>
                    <td class="action-<?= h(strtolower(explode('_', $log['action'])[0])) ?>">
                        <?= h(pv(isset($log['action']) ? $log['action'] : null)) ?>
                    </td>
                    <td><?= h(pv(isset($log['details']) ? $log['details'] : null)) ?></td>
                    <td><?= h(truthy(isset($log['student_id']) ? $log['student_id'] : null) ? $log['student_id'] : '-') ?></td>
                </tr>
                <?php endforeach; if ($__n1 === 0): ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #64748b;">
                        No audit records found.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>