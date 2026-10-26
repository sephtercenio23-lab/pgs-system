<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 800px;
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
            background: #64748b;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            box-sizing: border-box;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .btn-delete {
            padding: 6px 12px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-danger { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-users-cog"></i> User Management</h1>
            <a href="<?= url('/settings') ?>" class="btn-back">Back</a>
        </div>

        <?php $messages = get_flashed_messages(); ?>
            <?php if (!empty($messages)): ?>
                <?php $__n1 = 0; foreach ($messages as list($category, $message)): $__n1++; ?>
                    <div class="alert alert-<?= h(pv($category)) ?>"><?= h(pv($message)) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
        

        <div class="card">
            <h3>Add New User</h3>
            <form method="POST" action="<?= url('/users/add') ?>">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Create User</button>
            </form>
        </div>

        <div class="card">
            <h3>Existing Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__n2 = 0; foreach ($users as $user): $__n2++; ?>
                    <tr>
                        <td><?= h(pv(isset($user['name']) ? $user['name'] : null)) ?></td>
                        <td><?= h(pv(isset($user['username']) ? $user['username'] : null)) ?></td>
                        <td><?= h(pv(isset($user['role']) ? $user['role'] : null)) ?></td>
                        <td>
                            <form method="POST" action="<?= url('/users/delete/' . rawurlencode($user['id'])) ?>" 
                                  onsubmit="return confirm('Delete this user?');">
                                <button type="submit" class="btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>