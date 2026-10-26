<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics | Graduate School</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { 
            --primary: #e2e8f0; 
            --sidebar-text: #334155;
            --accent: #3b82f6; 
            --bg: #f1f5f9; 
            --success: #22c55e; 
            --danger: #ef4444; 
            --menu-hover: #cbd5e1;
        }
        
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; display: flex; min-height: 100vh; color: #334155; }
        
        /* SIDEBAR */
        .sidebar { 
            width: 280px; 
            background: var(--primary); 
            height: 100vh; 
            color: var(--sidebar-text); 
            padding: 25px; 
            position: fixed; 
            z-index: 100; 
            box-sizing: border-box; 
            border-right: 1px solid #cbd5e1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .sidebar-logo-container { margin-bottom: 30px; text-align: center; }
        .sidebar-logo { width: 200px; height: auto; object-fit: contain; }

        .menu-item { 
            width: 100%;
            padding: 12px 15px; 
            color: var(--sidebar-text); 
            text-decoration: none; 
            border-radius: 8px; 
            margin-bottom: 5px; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            transition: 0.3s; 
            font-weight: 500;
            box-sizing: border-box;
        }
        .menu-item:hover { background: var(--menu-hover); }
        .menu-item.active { background: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        
        .main { margin-left: 280px; padding: 40px; width: calc(100% - 280px); box-sizing: border-box; }
        
        /* HEADER LOGOS */
        .report-header-logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        .header-logo-img { height: 80px; width: auto; object-fit: contain; }

        /* STAT CARDS */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; border-left: 5px solid #94a3b8; }
        .stat-card.grad { border-left-color: var(--success); }
        .stat-card.fail { border-left-color: var(--danger); }
        
        .stat-info h3 { margin: 0; font-size: 32px; color: #1e293b; }
        .stat-info p { margin: 0; color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: 600; }
        .icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        
        .blue { background: #f1f5f9; color: #475569; }
        .red { background: #fee2e2; color: #991b1b; }
        .green { background: #dcfce7; color: #166534; }

        /* CONTENT GRID */
        .content-grid { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 30px; margin-bottom: 30px; }
        .table-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); height: 100%; box-sizing: border-box; }
        .card-header { font-size: 16px; font-weight: bold; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 10px; color: #64748b; font-size: 11px; text-transform: uppercase; border-bottom: 2px solid #eee; }
        td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        
        .progress-bar { background: #f1f5f9; height: 8px; border-radius: 4px; overflow: hidden; width: 100%; margin-top: 5px; }
        .fill { height: 100%; border-radius: 4px; }

        /* FOOTER */
        .footer-logo-img { width: 100%; max-width: 900px; height: auto; margin-top: 20px; }

        /* PRINT SETTINGS */
        @media print {
            .sidebar, .no-print, button { display: none !important; }
            .main { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
            .stat-card { border: 1px solid #eee !important; box-shadow: none !important; }
            .content-grid { grid-template-columns: 1fr !important; }
            .table-card { box-shadow: none; border: 1px solid #eee; }
            body { background: white; }
        }

        @media (max-width: 1100px) {
            .content-grid { grid-template-columns: 1fr; }
            .main { margin-left: 0; width: 100%; padding: 20px; }
            .sidebar { display: none; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo-container">
            <img src="<?= url('/static/pgs_logo.png') ?>" class="sidebar-logo" alt="Logo" onerror="this.src='https://via.placeholder.com/120'">
        </div>
        <a href="<?= url('/') ?>" class="menu-item"><i class="fas fa-users"></i> Students</a>
        <a href="<?= url('/reports') ?>" class="menu-item active"><i class="fas fa-chart-pie"></i> Reports</a>
        <a href="<?= url('/settings') ?>" class="menu-item"><i class="fas fa-cog"></i> Settings</a>
    </div>

    <div class="main">
        <div class="report-header-logos">
            <img src="<?= url('/static/school_logo.png') ?>" alt="School Logo" class="header-logo-img" onerror="this.style.display='none'">
            <div style="text-align: center;">
                <h2 style="margin: 0; color: #1e293b;">PANPACIFIC UNIVERSITY</h2>
                <p style="margin: 0; font-size: 14px; color: #64748b;">Graduate School Analytics Report</p>
            </div>
            <img src="<?= url('/static/pgs_logo.png') ?>" alt="PGS Logo" class="header-logo-img" onerror="this.style.display='none'">
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;" class="no-print">
            <h1 style="margin: 0; color: #1e293b;">Reports Dashboard</h1>
            <button onclick="window.print()" style="background: white; border: 1px solid #cbd5e1; padding: 10px 20px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-weight: 600; color: #334155;">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>

        <div class="stats-grid">
            <div class="stat-card" onclick="filterByStatus('all')" style="cursor: pointer;">
                <div class="stat-info">
                    <h3><?= h(isset($total) ? $total : 0) ?></h3>
                    <p>Total Students</p>
                </div>
                <div class="icon-box blue"><i class="fas fa-user-graduate"></i></div>
            </div>
            <div class="stat-card grad" onclick="filterByStatus('candidate')" style="cursor: pointer;">
                <div class="stat-info">
                    <h3><?= h(isset($candidates) ? $candidates : 0) ?></h3>
                    <p>Candidates</p>
                </div>
                <div class="icon-box green"><i class="fas fa-certificate"></i></div>
            </div>
            <div class="stat-card fail" onclick="filterByStatus('failure')" style="cursor: pointer;">
                <div class="stat-info">
                    <h3><?= h(isset($failures) ? $failures : 0) ?></h3>
                    <p>With Failures</p>
                </div>
                <div class="icon-box red"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>

        <div class="content-grid">
            <div class="table-card">
                <div class="card-header">
                    <span><i class="fas fa-layer-group"></i> Enrollment Distribution</span>
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start;">
                    <div class="table-container" style="flex: 1; min-width: 300px;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Count</th>
                                    <th width="35%">Ratio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rainbow = array('#FF0000', '#FF7F00', '#FFD700', '#22c55e', '#3b82f6', '#4B0082', '#9400D3'); ?>
                                <?php if (truthy($course_stats)): ?>
                                    <?php $__n1 = 0; $__ix = -1; foreach ($course_stats as $course => $count): $__ix++; $__n1++; ?>
                                    <tr>
                                        <td style="font-weight:600;"><?= h(pv($course)) ?></td>
                                        <td><?= h(pv($count)) ?></td>
                                        <td>
                                            <?php $perc = ($total && $total > 0) ? (int)($count / $total * 100) : 0; ?>
                                            <span style="font-size: 10px; color: #64748b;"><?= $perc ?>%</span>
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
                            <?php $__n2 = 0; foreach ($students as $s): $__n2++; ?>
                            <tr class="student-row" data-status="<?= h(pv(isset($s['report_status']) ? $s['report_status'] : null)) ?>">
                                <td>
                                    <div style="font-weight: bold;"><?= h(pv(isset($s['full_name']) ? $s['full_name'] : null)) ?></div>
                                    <div style="font-size: 10px; color: #64748b;">ID: <?= h(pv(isset($s['id']) ? $s['id'] : null)) ?></div>
                                </td>
                                <td>
                                    <?php if ($s['report_status'] == 'failure'): ?>
                                        <span style="color: #ef4444;"><i class="fas fa-times-circle"></i> Failed</span>
                                    <?php elseif ($s['report_status'] == 'candidate'): ?>
                                        <span style="color: #22c55e;"><i class="fas fa-check-circle"></i> Graduating Candidate</span>
                                    <?php else: ?>
                                        <span style="color: #f59e0b;"><i class="fas fa-clock"></i> Incomplete / Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="no-print">
                                    <a href="<?= url('/student/' . rawurlencode($s['id'])) ?>" style="color:#3b82f6; text-decoration:none; font-weight:bold;">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        
            <div class="card-header">
                <span><i class="fas fa-info-circle"></i> Student Classification Breakdown</span>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding: 20px 0;">
                <?php $new_e = 0; $trans = 0; $cont = 0; foreach ($students as $__s) { $__t = isset($__s['type']) ? $__s['type'] : null; if ($__t === 'New Enrollee') $new_e++; elseif ($__t === 'Transferee') $trans++; elseif ($__t === 'Continuing') $cont++; } ?>
                
                <div style="padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <p style="margin: 0; color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: 600;">New Enrollees</p>
                    <h2 style="margin: 10px 0 0 0; color: #1e293b; font-size: 36px;"><?= h(pv($new_e)) ?></h2>
                </div>
                
                <div style="padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <p style="margin: 0; color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: 600;">Transferees</p>
                    <h2 style="margin: 10px 0 0 0; color: #1e293b; font-size: 36px;"><?= h(pv($trans)) ?></h2>
                </div>
                
                <div style="padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <p style="margin: 0; color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: 600;">Continuing</p>
                    <h2 style="margin: 10px 0 0 0; color: #1e293b; font-size: 36px;"><?= h(pv($cont)) ?></h2>
                </div>
            </div>

            <div style="margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 20px; text-align: center;">
                <img src="<?= url('/static/pu_footer1.png') ?>" alt="PU Footer" class="footer-logo-img" onerror="this.style.display='none'">
                <p style="font-size: 11px; color: #94a3b8; margin-top: 10px;">This is a computer-generated report from the Graduate School Portal. Generated on: 2026-02-11</p>
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
                            backgroundColor: ['#FF0000', '#FF7F00', '#FFD700', '#22c55e', '#3b82f6', '#4B0082', '#9400D3'],
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
</body>
</html>