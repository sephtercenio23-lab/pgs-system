<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics | Graduate School</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= url('/static/app.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="page-reports">
    <div class="app-body">
        <?php $active = 'reports'; include __DIR__ . '/_sidebar.php'; ?>

        <div class="main">
            <header class="topbar">
                <button class="sidebar-toggle" onclick="App.toggleSidebar()"><i class="fas fa-bars"></i></button>
                <div class="titles">
                    <h1>Reports Dashboard</h1>
                    <p class="subtitle">Enrollment analytics and graduation candidates.</p>
                </div>
                <div class="topbar-actions no-print">
                    <button class="btn btn-outline" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </header>

            <div class="content-container">
                <div class="report-header-logos">
                    <img src="<?= url('/static/school_logo.png') ?>" alt="School Logo" class="header-logo-img" onerror="this.style.display='none'">
                    <div style="text-align: center;">
                        <h2 style="margin: 0;">PANPACIFIC UNIVERSITY</h2>
                        <p style="margin: 4px 0 0; font-size: 14px; color: var(--muted);">Graduate School Analytics Report</p>
                    </div>
                    <img src="<?= url('/static/pgs_logo.png') ?>" alt="PGS Logo" class="header-logo-img" onerror="this.style.display='none'">
                </div>

                <div class="stats-grid">
                    <div class="stat-card" onclick="filterByStatus('all')" style="cursor: pointer;">
                        <div class="stat-icon blue"><i class="fas fa-user-graduate"></i></div>
                        <div class="stat-info">
                            <h3><?= h(isset($total) ? $total : 0) ?></h3>
                            <p>Total Students</p>
                        </div>
                    </div>
                    <div class="stat-card" onclick="filterByStatus('candidate')" style="cursor: pointer;">
                        <div class="stat-icon green"><i class="fas fa-certificate"></i></div>
                        <div class="stat-info">
                            <h3><?= h(isset($candidates) ? $candidates : 0) ?></h3>
                            <p>Candidates</p>
                        </div>
                    </div>
                    <div class="stat-card" onclick="filterByStatus('failure')" style="cursor: pointer;">
                        <div class="stat-icon red"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="stat-info">
                            <h3><?= h(isset($failures) ? $failures : 0) ?></h3>
                            <p>With Failures</p>
                        </div>
                    </div>
                </div>

                <div class="content-grid">
                    <div class="table-card">
                        <div class="card-header">
                            <i class="fas fa-layer-group"></i> Enrollment Distribution
                        </div>
                        <div class="table-container" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start;">
                            <div style="flex: 1; min-width: 300px;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>Count</th>
                                            <th width="35%">Ratio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $rainbow = array('#4f46e5', '#7c3aed', '#0ea5e9', '#22c55e', '#f59e0b', '#ef4444', '#9400D3'); ?>
                                        <?php if (truthy($course_stats)): ?>
                                            <?php $__ix = -1; foreach ($course_stats as $course => $count): $__ix++; ?>
                                            <tr>
                                                <td style="font-weight:600;"><?= h(pv($course)) ?></td>
                                                <td><?= h(pv($count)) ?></td>
                                                <td>
                                                    <?php $perc = ($total && $total > 0) ? (int)($count / $total * 100) : 0; ?>
                                                    <span style="font-size: 10px; color: var(--muted);"><?= $perc ?>%</span>
                                                    <div class="progress-bar">
                                                        <div class="fill" style="width: <?= $perc ?>%; background-color: <?= $rainbow[$__ix % 7] ?>;"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="3" style="text-align:center; padding: 20px;">No data available</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div style="width: 350px; min-height: 250px; margin: auto;">
                                <canvas id="courseChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="table-card">
                        <div class="card-header" id="table-title">
                            <span><i class="fas fa-users"></i> All Student Records</span>
                        </div>
                        <div class="table-container" style="max-height: 400px; overflow-y: auto;">
                            <table id="reportsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Status / Deficiency</th>
                                        <th class="no-print">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $s): ?>
                                    <tr class="student-row" data-status="<?= h(pv(isset($s['report_status']) ? $s['report_status'] : null)) ?>">
                                        <td class="cell-name">
                                            <strong><?= h(pv(isset($s['full_name']) ? $s['full_name'] : null)) ?></strong>
                                            <span class="cell-sub">ID: <?= h(pv(isset($s['id']) ? $s['id'] : null)) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($s['report_status'] == 'failure'): ?>
                                                <span class="badge badge-failed"><i class="fas fa-times-circle"></i> Failed</span>
                                            <?php elseif ($s['report_status'] == 'candidate'): ?>
                                                <span class="badge badge-passed"><i class="fas fa-check-circle"></i> Graduating Candidate</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning"><i class="fas fa-clock"></i> Incomplete / Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="no-print">
                                            <a href="<?= url('/student/' . rawurlencode($s['id'])) ?>" class="btn btn-view" style="padding: 6px 12px; font-size: .75rem;">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="table-card" style="margin-bottom: 22px;">
                    <div class="card-header">
                        <i class="fas fa-info-circle"></i> Student Classification Breakdown
                    </div>
                    <div class="table-container">
                        <?php $new_e = 0; $trans = 0; $cont = 0; foreach ($students as $__s) { $__t = isset($__s['type']) ? $__s['type'] : null; if ($__t === 'New Enrollee') $new_e++; elseif ($__t === 'Transferee') $trans++; elseif ($__t === 'Continuing') $cont++; } ?>

                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding: 12px 0;">
                            <div class="mini-stat">
                                <p>New Enrollees</p>
                                <h2><?= h(pv($new_e)) ?></h2>
                            </div>
                            <div class="mini-stat">
                                <p>Transferees</p>
                                <h2><?= h(pv($trans)) ?></h2>
                            </div>
                            <div class="mini-stat">
                                <p>Continuing</p>
                                <h2><?= h(pv($cont)) ?></h2>
                            </div>
                        </div>

                        <div class="report-footer">
                            <img src="<?= url('/static/pu_footer1.png') ?>" alt="PU Footer" class="footer-logo-img" onerror="this.style.display='none'">
                            <p>This is a computer-generated report from the Graduate School Portal. Generated on: <?= date('Y-m-d') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartEl = document.getElementById('courseChart');
            if (!chartEl) return;
            const ctx = chartEl.getContext('2d');

            const labels = <?= json_encode(array_keys($course_stats)) ?>;
            const counts = <?= json_encode(array_values($course_stats)) ?>;

            if (labels.length > 0) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Students',
                            data: counts,
                            backgroundColor: ['#4f46e5', '#7c3aed', '#0ea5e9', '#22c55e', '#f59e0b', '#ef4444', '#9400D3'],
                            borderRadius: 5
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, grid: { display: false } },
                            y: { grid: { display: false } }
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        });

        window.filterByStatus = function(statusType) {
            const rows = document.querySelectorAll('.student-row');
            const titleSpan = document.querySelector('#table-title span');

            rows.forEach(row => {
                const studentStatus = row.getAttribute('data-status');
                if (statusType === 'all') {
                    row.style.display = '';
                } else {
                    if (studentStatus === statusType) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            if(statusType === 'candidate') {
                titleSpan.innerHTML = '<i class="fas fa-certificate"></i> Official Candidates for Graduation';
            } else if(statusType === 'failure') {
                titleSpan.innerHTML = '<i class="fas fa-exclamation-circle"></i> Students with Deficiency/Failures';
            } else {
                titleSpan.innerHTML = '<i class="fas fa-users"></i> All Student Records';
            }
        };
    </script>
    <script src="<?= url('/static/app.js') ?>"></script>
</body>
</html>
