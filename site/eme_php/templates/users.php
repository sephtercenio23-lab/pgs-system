<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Graduate School</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= url('/static/app.css') ?>" rel="stylesheet">
</head>
<body class="page-users">
    <div class="app-body">
        <?php $active = 'users'; include __DIR__ . '/_sidebar.php'; ?>

        <div class="main">
            <header class="topbar">
                <button class="sidebar-toggle" onclick="App.toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div class="titles">
                    <h1><i class="fas fa-users-cog" style="color: var(--brand);"></i> User Management</h1>
                    <p class="subtitle">Create accounts and manage roles.</p>
                </div>
            </header>

            <div class="content-container" style="max-width: 900px;">
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
                    <h3><i class="fas fa-user-plus" style="color: var(--success);"></i> Add New User</h3>
                    <hr class="card-divider">
                    <form method="POST" action="<?= url('/users/add') ?>">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="name" required placeholder="Juan Dela Cruz">
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" required placeholder="username">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" required placeholder="••••••••">
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role">
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-submit"><i class="fas fa-plus"></i> Create User</button>
                    </form>
                </div>

                <div class="card">
                    <h3><i class="fas fa-users" style="color: var(--info);"></i> Existing Users</h3>
                    <hr class="card-divider">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th style="text-align:right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="cell-name"><strong><?= h(pv(isset($user['name']) ? $user['name'] : null)) ?></strong></td>
                                    <td><?= h(pv(isset($user['username']) ? $user['username'] : null)) ?></td>
                                    <td>
                                        <span class="badge <?= (isset($user['role']) ? $user['role'] : '') === 'admin' ? 'badge-brand' : 'badge-neutral' ?>">
                                            <?= h(pv(isset($user['role']) ? $user['role'] : null)) ?>
                                        </span>
                                    </td>
                                    <td style="text-align:right;">
                                        <form method="POST" action="<?= url('/users/delete/' . rawurlencode($user['id'])) ?>"
                                              style="display:inline;" onsubmit="return confirm('Delete this user?');">
                                            <button type="submit" class="btn btn-del">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
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
