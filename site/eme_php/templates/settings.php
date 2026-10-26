<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings | Graduate School</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* UPDATE: Light Gray Theme consistent sa ibang pages */
        :root { 
            --primary: #e2e8f0; /* Light Gray Sidebar */
            --sidebar-text: #334155;
            --accent: #3b82f6; 
            --bg: #f1f5f9; 
            --menu-hover: #cbd5e1;
            --danger: #ef4444;
            --success: #10b981;
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
        
        /* LOGO CONTAINER */
        .sidebar-logo-container { 
            margin-bottom: 30px; 
            display: flex; 
            justify-content: center; 
            align-items: center;
            width: 200px; 
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
        .menu-item.active { 
            background: white; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            color: var(--sidebar-text);
        }
        
        /* MAIN CONTENT */
        .main { margin-left: 280px; padding: 40px; width: calc(100% - 280px); box-sizing: border-box; }
        .settings-container { max-width: 650px; }

        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .card h3 { margin-top: 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; color: #1e293b; display: flex; align-items: center; gap: 10px; }
        .card p { color: #64748b; font-size: 14px; margin-bottom: 20px; line-height: 1.5; }

        /* BUTTONS - Adjusted for consistency */
        .btn { padding: 12px 20px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
        
        .btn-download { background: #64748b; color: white; } /* Grayish button */
        .btn-download:hover { background: #475569; }
        
        .btn-upload { background: var(--success); color: white; }
        .btn-upload:hover { background: #059669; }
        
        .btn-reset { background: white; border: 1.5px solid var(--danger); color: var(--danger); }
        .btn-reset:hover { background: var(--danger); color: white; }
        
        input[type="file"] { 
            margin-bottom: 15px; 
            display: block; 
            font-size: 13px;
            color: #64748b;
            background: #f8fafc;
            padding: 10px;
            border-radius: 6px;
            width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 900px) {
            .main { margin-left: 0; width: 100%; padding: 20px; }
            .sidebar { display: none; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-logo-container">
            <img src="<?= url('/static/pgs_logo.png') ?>" class="sidebar-logo" alt="PGS Logo">
        </div>

        <a href="<?= url('/') ?>" class="menu-item"><i class="fas fa-users"></i> Students</a>
        <a href="<?= url('/reports') ?>" class="menu-item"><i class="fas fa-chart-pie"></i> Reports</a>
        <a href="<?= url('/settings') ?>" class="menu-item active"><i class="fas fa-cog"></i> Settings</a>
    </div>

    <div class="main">
        <div class="settings-container">
            <h1 style="margin-bottom: 30px; color: #1e293b;">System Settings</h1>

            <div class="card">
                <h3><i class="fas fa-download"></i> Backup Data</h3>
                <p>Download a copy of the student database in <b>JSON format</b>. It is recommended to back up your records regularly to prevent data loss.</p>
                <a href="<?= url('/settings/backup') ?>" class="btn btn-download"><i class="fas fa-file-download"></i> Download Backup</a>
            </div>

            <div class="card">
                <h3><i class="fas fa-upload"></i> Restore Data</h3>
                <p>Upload a previously saved JSON backup file. <b>Note:</b> This action will completely overwrite the current data in the system.</p>
                <form action="<?= url('/settings/restore') ?>" method="POST" enctype="multipart/form-data">
                    <input type="file" name="backup_file" required>
                    <button type="submit" class="btn btn-upload"><i class="fas fa-file-import"></i> Restore Database</button>
                </form>
            </div>

            <div class="card" style="border: 1px solid #fee2e2; background: #fffafb;">
                <h3 style="color:var(--danger);"><i class="fas fa-exclamation-triangle"></i> Danger Zone</h3>
                <p>This action will <b>delete all student records</b> and system data. Once wiped, the information cannot be recovered unless you have a backup.</p>
                <form action="<?= url('/settings/reset') ?>" method="POST" onsubmit="return confirm('SYSTEM RESET: Are you absolutely sure? This cannot be undone!')">
                    <button type="submit" class="btn btn-reset"><i class="fas fa-trash-alt"></i> Reset System Data</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>