<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students | Graduate School Portal</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= url('/static/app.css') ?>" rel="stylesheet">
</head>
<body class="page-dashboard">
    <div class="app-body">
        <?php $active = 'students'; include __DIR__ . '/_sidebar.php'; ?>

        <div class="main">
            <header class="topbar">
                <button class="sidebar-toggle" onclick="App.toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div class="titles">
                    <h1>Student List</h1>
                    <p class="subtitle">Manage enrollment and academic records.</p>
                </div>
                <div class="topbar-actions">
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
            </header>

            <div class="content-container">
                <?php $messages = get_flashed_messages(); ?>
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as list($category, $message)): ?>
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
                        <div class="stat-icon green"><i class="fas fa-user-graduate"></i></div>
                        <div class="stat-info">
                            <h3><?= $total_students - count($attention_required) ?></h3>
                            <p>Good Standing</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="search-box" style="margin-bottom: 20px;">
                        <div class="input-icon-wrap" style="max-width: 420px; position: relative;">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search by Name or ID..."
                                   value="<?= h($search_query) ?>">
                        </div>
                    </div>

                    <div class="table-wrap">
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
                                <tr>
                                    <td class="cell-id"><?= h(pv(isset($student['id']) ? $student['id'] : null)) ?></td>
                                    <td class="cell-name">
                                        <strong><?= h(pv(isset($student['full_name']) ? $student['full_name'] : null)) ?></strong>
                                        <?php if (truthy(isset($student['prereq_status']) ? $student['prereq_status'] : null)): ?>
                                            <span title="Pre-requisite Required" style="color: var(--warning); margin-left: 5px;">
                                                <i class="fas fa-exclamation-circle"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="chip"><?= h(pv(isset($student['course']) ? $student['course'] : null)) ?></span></td>
                                    <td><span class="cell-sub"><?= h(pv(isset($student['type']) ? $student['type'] : null)) ?></span></td>
                                    <td style="text-align:right; white-space: nowrap;">
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
                                    <td colspan="5" class="table-empty">
                                        <i class="fas fa-folder-open"></i>
                                        No student records found.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?><?php if ($search_query !== ''): ?>&q=<?= h(urlencode($search_query)) ?><?php endif; ?>"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        <?php foreach (range(1, $total_pages) as $p): ?>
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
    </div>

    <div id="studentModal" class="modal">
        <div class="modal-content">
            <div class="modal-title-row">
                <h3>Add New Student</h3>
                <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
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
                <div style="margin: 15px 0; padding: 12px 14px; background: #f8fafc; border: 1px solid var(--line); border-radius: 10px;">
                    <label class="check-line">
                        <input type="checkbox" name="prereq_met" value="true"> Has Pre-requisites?
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
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
    <script src="<?= url('/static/app.js') ?>"></script>
</body>
</html>
