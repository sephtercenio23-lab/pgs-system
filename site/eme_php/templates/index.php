<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { 
            --primary: #e2e8f0;
            --accent: #3b82f6; 
            --bg: #f1f5f9;
            --text-dark: #334155;
            --menu-hover: #cbd5e1;
            --white: #ffffff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
            background: var(--bg); 
            margin: 0; 
            display: flex; 
            color: var(--text-dark);
            min-height: 100vh;
        }
        
        .sidebar { 
            width: 280px; 
            background: var(--primary); 
            height: 100vh; 
            color: var(--text-dark); 
            padding: 25px; 
            position: fixed; 
            display: flex; 
            flex-direction: column; 
            align-items: center;
            border-right: 1px solid #cbd5e1;
            z-index: 100;
        }
        
        .sidebar-logo-container { 
            margin-bottom: 30px; 
            display: flex; 
            justify-content: center; 
            align-items: center;
            width: 250px; 
            height: auto;
            box-sizing: border-box;
        }
        .sidebar-logo { 
            width: 100%; 
            height: auto; 
            max-height: 200px; 
            object-fit: contain;
            display: block;
        }

        .menu-item { 
            width: 100%; 
            padding: 12px 15px; 
            color: var(--text-dark); 
            text-decoration: none; 
            border-radius: 8px; 
            margin-bottom: 5px; 
            transition: all 0.2s ease; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            box-sizing: border-box;
            font-weight: 500;
        }
        .menu-item:hover { 
            background: var(--menu-hover); 
            transform: translateX(4px);
        }
        .menu-item.active { 
            background: var(--white); 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            color: var(--accent);
        }
        
        .user-section {
            margin-top: auto;
            width: 100%;
            padding-top: 20px;
            border-top: 1px solid #cbd5e1;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .user-info i {
            font-size: 2rem;
            color: var(--accent);
        }
        .logout-btn {
            width: 100%;
            padding: 10px;
            background: #fee2e2;
            color: #991b1b;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .main { 
            margin-left: 280px; 
            padding: 40px; 
            width: calc(100% - 280px); 
            display: flex;
            flex-direction: column;
            align-items: center; 
            box-sizing: border-box;
        }

        .content-container {
            width: 100%;
            max-width: 1100px; 
        }

        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-end; 
            margin-bottom: 30px; 
        }

        .card { 
            background: var(--white); 
            padding: 25px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); 
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-icon.blue { background: #dbeafe; color: #2563eb; }
        .stat-icon.orange { background: #fef3c7; color: #d97706; }
        .stat-icon.red { background: #fee2e2; color: #dc2626; }
        .stat-info h3 {
            margin: 0;
            font-size: 1.75rem;
            color: #1e293b;
        }
        .stat-info p {
            margin: 0;
            color: #64748b;
            font-size: 0.875rem;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; padding: 15px; color: #64748b; font-size: 13px; border-bottom: 2px solid #f1f5f9; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .row-hover:hover { background: #f8fafc; }

        .btn { padding: 10px 18px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: #2563eb; }
        .btn-view { background: #e0f2fe; color: #0284c7; }
        .btn-del { background: #fee2e2; color: #991b1b; }
        .btn-import { background: #64748b; color: white; }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }
        .pagination .current {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; z-index: 1000; }
        .modal-content { background: white; padding: 30px; border-radius: 16px; width: 550px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); max-height: 90vh; overflow-y: auto; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 13px; color: var(--accent); font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; box-sizing: border-box; font-family: inherit; }
        .radio-group { display: flex; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; gap: 15px; }
        .radio-group label { display: flex; align-items: center; gap: 6px; font-weight: 500; color: var(--text-dark); font-size: 12px; margin-bottom: 0 !important; }

        @media (max-width: 1024px) {
            .sidebar { display: none; }
            .main { margin-left: 0; width: 100%; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo-container">
            <img src="<?= url('/static/pgs_logo.png') ?>" class="sidebar-logo" alt="PGS Logo" onerror="this.style.display='none'">
        </div>

        <a href="<?= url('/') ?>" class="menu-item active"><i class="fas fa-users"></i> Students</a>
        <a href="<?= url('/reports') ?>" class="menu-item"><i class="fas fa-chart-pie"></i> Reports</a>
        <a href="<?= url('/settings') ?>" class="menu-item"><i class="fas fa-cog"></i> Settings</a>
        <?php if ((isset($_SESSION['role']) ? $_SESSION['role'] : null) === 'admin'): ?>
        <a href="<?= url('/users') ?>" class="menu-item"><i class="fas fa-users-cog"></i> Users</a>
        <a href="<?= url('/audit-log') ?>" class="menu-item"><i class="fas fa-history"></i> Audit Log</a>
        <?php endif; ?>

        <div class="user-section">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <div>
                    <div><strong><?= h(isset($_SESSION['username']) ? $_SESSION['username'] : '') ?></strong></div>
                    <div class="role" style="font-size: 0.75rem; color: #64748b;"><?= h(isset($_SESSION['role']) ? $_SESSION['role'] : 'User') ?></div>
                </div>
            </div>
            <a href="<?= url('/logout') ?>" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="main">
        <div class="content-container">
            <div class="header">
                <div>
                    <h1 style="margin:0; font-size: 28px;">STUDENT LIST</h1>
                    <p style="margin:5px 0 0 0; color:#64748b;">Manage enrollment and academic records.</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <form id="importForm" action="<?= url('/import_csv') ?>" method="POST" enctype="multipart/form-data" style="display:none;">
                        <input type="file" id="csvFileInput" name="file" accept=".csv" onchange="document.getElementById('importForm').submit()">
                    </form>
                    <button class="btn btn-import" onclick="document.getElementById('csvFileInput').click()">
                        <i class="fas fa-file-import"></i> Import
                    </button>
                    <button class="btn btn-primary" onclick="openModal()">
                        <i class="fas fa-plus"></i> Add Student
                    </button>
                </div>
            </div>

            <?php $messages = get_flashed_messages(); ?>
                <?php if (!empty($messages)): ?>
                    <?php $__n1 = 0; foreach ($messages as list($category, $message)): $__n1++; ?>
                        <div class="alert alert-<?= h(pv($category)) ?>">
                            <i class="fas fa-<?= $category == 'success' ? 'check-circle' : ($category == 'warning' ? 'exclamation-triangle' : 'times-circle') ?>"></i>
                            <?= h(pv($message)) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?= h(pv($total_students)) ?></h3>
                        <p>Total Students</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="stat-info">
                        <h3><?= count($attention_required) ?></h3>
                        <p>Needs Attention</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fas fa-user-graduate"></i></div>
                    <div class="stat-info">
                        <h3><?= $total_students - count($attention_required) ?></h3>
                        <p>Good Standing</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
                    <div style="position:relative;">
                        <i class="fas fa-search" style="position:absolute; left:12px; top:14px; color:#94a3b8;"></i>
                        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search by Name or ID..." 
                               value="<?= h($search_query) ?>" style="padding:12px 12px 12px 40px; border:1px solid #e2e8f0; border-radius:8px; width: 320px;">
                    </div>
                </div>

                <table id="studentTable">
                    <thead>
                        <tr>
                            <th>ID Number</th>
                            <th>Student Name</th>
                            <th>Program</th>
                            <th>Type</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__n2 = 0; foreach ($students as $student): $__n2++; ?>
                        <tr class="row-hover">
                            <td style="font-weight:bold; color:var(--accent);"><?= h(pv(isset($student['id']) ? $student['id'] : null)) ?></td>
                            <td>
                                <?= h(pv(isset($student['full_name']) ? $student['full_name'] : null)) ?>
                                <?php if (truthy(isset($student['prereq_status']) ? $student['prereq_status'] : null)): ?>
                                    <span title="Pre-requisite Required" style="color: #f59e0b; margin-left: 5px;">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><span style="background:#f1f5f9; color:#475569; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700;"><?= h(pv(isset($student['course']) ? $student['course'] : null)) ?></span></td>
                            <td><span style="font-size:12px; color:#64748b;"><?= h(pv(isset($student['type']) ? $student['type'] : null)) ?></span></td>
                            <td style="text-align:right;">
                                <a href="<?= url('/student/' . rawurlencode($student['id'])) ?>" class="btn btn-view"><i class="fas fa-eye"></i> View</a>
                                <form method="POST" action="<?= url('/add_student') ?>" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="target_id" value="<?= h(pv(isset($student['id']) ? $student['id'] : null)) ?>">
                                    <button type="submit" class="btn btn-del" onclick="return confirm('Delete this student?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; if ($__n2 === 0): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:50px; color:#94a3b8;">
                                <i class="fas fa-folder-open" style="font-size: 32px; display:block; margin-bottom:10px;"></i>
                                No student records found.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?><?php if ($search_query !== ''): ?>&q=<?= h(urlencode($search_query)) ?><?php endif; ?>"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <?php $__n3 = 0; foreach (range(1, $total_pages) as $p): $__n3++; ?>
                        <?php if ($p == $page): ?>
                            <span class="current"><?= h(pv($p)) ?></span>
                        <?php else: ?>
                            <a href="?page=<?= h(pv($p)) ?><?php if ($search_query !== ''): ?>&q=<?= h(urlencode($search_query)) ?><?php endif; ?>"><?= h(pv($p)) ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1 ?><?php if ($search_query !== ''): ?>&q=<?= h(urlencode($search_query)) ?><?php endif; ?>"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="studentModal" class="modal">
        <div class="modal-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
                <h3 style="margin:0; font-size:20px;">Add New Student</h3>
                <span style="cursor:pointer; font-size:28px; color:#94a3b8;" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" action="<?= url('/add_student') ?>">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label>Student ID Number</label>
                    <input type="text" name="student_id" required placeholder="e.g. 2023-0001">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="fname" required placeholder="John">
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="lname" required placeholder="Doe">
                    </div>
                </div>
                <div class="form-group">
                    <label>Academic Program</label>
                    <select name="course" required>
                        <option value="" disabled selected>Select Program</option>
                        <option value="MBA">MBA (Master in Business Ad)</option>
                        <option value="MPA">MPA (Master in Public Ad)</option>
                        <option value="MAED-EM">MAEd (Educational Mgt)</option>
                        <option value="MAED-SPED">MAEd (Special Education)</option>
                        <option value="MSC-CRIM">MSc Criminology</option>
                        <option value="PHD-PA">PhD Public Ad</option>
                        <option value="PHD-DEM">PhDEM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Student Classification</label>
                    <div class="radio-group">
                        <label><input type="radio" name="student_type" value="New Enrollee" checked> New Enrollee</label>
                        <label><input type="radio" name="student_type" value="Transferee"> Transferee</label>
                        <label><input type="radio" name="student_type" value="Continuing"> Continuing</label>
                    </div>
                </div>
                <div style="margin: 15px 0; padding: 10px; background: #f8fafc; border-radius: 8px;">
                    <label style="font-size: 13px; display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="prereq_met" value="true"> Has Pre-requisites?
                    </label>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; padding: 14px;">
                    <i class="fas fa-save"></i> Save Student Record
                </button>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('studentModal').style.display = 'flex'; }
        function closeModal() { document.getElementById('studentModal').style.display = 'none'; }
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) event.target.style.display = "none";
        }
        function filterTable() {
            let input = document.getElementById("searchInput").value.toUpperCase();
            let rows = document.querySelectorAll("#studentTable tbody tr");
            rows.forEach(row => {
                let text = row.textContent.toUpperCase();
                row.style.display = text.includes(input) ? "" : "none";
            });
        }
    </script>
</body>
</html>