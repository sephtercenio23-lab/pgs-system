<?php if (!isset($active)) { $active = ''; } ?>
<aside class="sidebar" id="appSidebar">
    <div class="brand">
        <img src="<?= url('/static/pgs_logo.png') ?>" alt="PGS Logo" onerror="this.style.display='none'">
        <div class="brand-text">
            <strong>PGS Portal</strong>
            <span>Graduate School &middot; Panpacific University</span>
        </div>
    </div>

    <nav class="side-nav">
        <a href="<?= url('/') ?>" class="menu-item<?= $active === 'students' ? ' active' : '' ?>"><i class="fas fa-users"></i> Students</a>
        <a href="<?= url('/reports') ?>" class="menu-item<?= $active === 'reports' ? ' active' : '' ?>"><i class="fas fa-chart-pie"></i> Reports</a>
        <a href="<?= url('/settings') ?>" class="menu-item<?= $active === 'settings' ? ' active' : '' ?>"><i class="fas fa-cog"></i> Settings</a>
        <?php if ((isset($_SESSION['role']) ? $_SESSION['role'] : null) === 'admin'): ?>
        <a href="<?= url('/users') ?>" class="menu-item<?= $active === 'users' ? ' active' : '' ?>"><i class="fas fa-users-cog"></i> Users</a>
        <a href="<?= url('/audit-log') ?>" class="menu-item<?= $active === 'audit' ? ' active' : '' ?>"><i class="fas fa-history"></i> Audit Log</a>
        <?php endif; ?>
    </nav>

    <div class="user-section">
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <div>
                <strong><?= h(isset($_SESSION['username']) ? $_SESSION['username'] : '') ?></strong>
                <span class="role"><?= h(isset($_SESSION['role']) ? $_SESSION['role'] : 'User') ?></span>
            </div>
        </div>
        <a href="<?= url('/logout') ?>" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="App.closeSidebar()"></div>
