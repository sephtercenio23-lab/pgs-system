<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Evaluation | <?= h(pv(isset($student['full_name']) ? $student['full_name'] : null)) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= url('/static/app.css') ?>" rel="stylesheet">
    <style>
        /* Screen helpers kept page-local */
        /* PRINT STYLES - Clean Professional Layout */
        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }
            
            body {
                background: white;
                font-size: 10pt;
                line-height: 1.3;
            }
            
            /* Hide navigation and buttons */
            .navbar,
            .profile-panel,
            .btn,
            .modal,
            .prereq-section,
            .warning-box,
            .info-box,
            button,
            .no-print {
                display: none !important;
            }
            
            /* Main content full width */
            .main-wrapper {
                display: block;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
            
            .content {
                width: 100%;
            }
            
            /* Print header */
            .print-header {
                display: block !important;
                text-align: center;
                border-bottom: 3px double #000;
                padding-bottom: 15px;
                margin-bottom: 20px;
            }
            
            .print-header .logo-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
            }
            
            .print-header img {
                height: 70px;
                width: auto;
            }
            
            .print-header h1 {
                font-size: 16pt;
                margin: 0;
                color: #000;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            
            .print-header h2 {
                font-size: 12pt;
                margin: 5px 0;
                color: #333;
                font-weight: normal;
            }
            
            .print-header .doc-title {
                font-size: 14pt;
                font-weight: bold;
                margin-top: 10px;
                padding: 8px;
                background: #f0f0f0;
                border: 1px solid #ccc;
            }
            
            /* Student info section */
            .print-student-info {
                width: 100%;
                margin-bottom: 20px;
                padding: 15px;
                border: 2px solid #000;
                background: #fafafa;
            }
            
            .print-student-info table {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
            }
            
            .print-student-info td {
                padding: 8px;
                border: none;
                vertical-align: top;
            }
            
            .print-student-info .label {
                font-weight: bold;
                width: 30%;
            }
            
            .print-student-info .value {
                border-bottom: 1px solid #666;
                width: 70%;
            }
            
            /* Summary box */
            .print-summary {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                gap: 10px;
                margin-bottom: 20px;
                padding: 15px;
                border: 2px solid #000;
                background: #f5f5f5;
            }
            
            .print-summary .summary-item {
                text-align: center;
                padding: 10px;
                border-right: 1px solid #ccc;
            }
            
            .print-summary .summary-item:last-child {
                border-right: none;
            }
            
            .print-summary .summary-label {
                font-size: 8pt;
                text-transform: uppercase;
                color: #555;
                margin-bottom: 5px;
            }
            
            .print-summary .summary-value {
                font-size: 12pt;
                font-weight: bold;
                color: #000;
            }
            
            /* Tables */
            .card {
                box-shadow: none;
                border: none;
                padding: 0;
                margin-bottom: 15px;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
                font-size: 9pt;
            }
            
            th {
                background: #e0e0e0 !important;
                color: #000 !important;
                padding: 6px;
                border: 1px solid #999;
                font-size: 8pt;
            }
            
            td {
                padding: 5px 6px;
                border: 1px solid #999;
                vertical-align: top;
            }
            
            tr:nth-child(even) {
                background: #f9f9f9;
            }
            
            /* Section headers */
            h3 {
                font-size: 11pt;
                color: #000;
                border-bottom: 2px solid #000;
                padding-bottom: 5px;
                margin-top: 20px;
                margin-bottom: 10px;
            }
            
            /* Checklist print */
            .print-checklist {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 15px;
                margin-bottom: 20px;
            }
            
            .print-checklist-group {
                border: 1px solid #999;
                padding: 10px;
            }
            
            .print-checklist-group h4 {
                font-size: 9pt;
                margin: 0 0 10px 0;
                padding-bottom: 5px;
                border-bottom: 1px solid #999;
                text-transform: uppercase;
            }
            
            .print-checklist-item {
                font-size: 8pt;
                margin-bottom: 5px;
                display: flex;
                align-items: center;
                gap: 5px;
            }
            
            .checkbox {
                width: 10px;
                height: 10px;
                border: 1px solid #000;
                display: inline-block;
                margin-right: 5px;
            }
            
            .checkbox.checked {
                background: #000;
            }
            
            /* Signatures */
            .print-signatures {
                margin-top: 40px;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 50px;
                page-break-inside: avoid;
            }
            
            .signature-line {
                border-top: 1px solid #000;
                margin-top: 50px;
                padding-top: 5px;
                text-align: center;
                font-size: 9pt;
            }
            
            /* Footer */
            .print-footer {
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #999;
                font-size: 7pt;
                color: #666;
                text-align: center;
            }
            
            /* Status badges for print */
            .badge {
                border: 1px solid #000;
                padding: 2px 5px;
                font-size: 7pt;
            }
            
            .badge-passed {
                background: #fff !important;
                color: #000 !important;
            }
            
            .badge-failed {
                background: #fff !important;
                color: #000 !important;
                border-style: dashed;
            }
        }
        
        /* Screen-only elements */
        .print-only {
            display: none;
        }

        @media (max-width: 1024px) {
            .main-wrapper { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="page-profile">

    <!-- PRINT HEADER - Only visible when printing -->
    <div class="print-only print-header">
        <div class="logo-row">
            <img src="<?= url('/static/school_logo.png') ?>" alt="School Logo" onerror="this.style.display='none'">
            <div>
                <h1>Panpacific University</h1>
                <h2>Graduate School</h2>
            </div>
            <img src="<?= url('/static/pgs_logo.png') ?>" alt="PGS Logo" onerror="this.style.display='none'">
        </div>
        <div class="doc-title">OFFICIAL ACADEMIC EVALUATION</div>
    </div>

    <nav class="navbar no-print">
        <a href="<?= url('/') ?>"><i class="fas fa-chevron-left"></i> Dashboard</a>
        <div class="nav-title"><i class="fas fa-graduation-cap"></i> STUDENT EVALUATION</div>
        <div style="width: 100px; text-align: right;">
            <i class="fas fa-circle-user fa-lg"></i>
        </div>
    </nav>

    <div class="main-wrapper">
        <div class="profile-panel no-print">
            <div class="card" style="position: sticky; top: 100px;">
                <div class="assessment-header">
                    <div class="profile-img">
                        <i class="fas fa-user fa-3x"></i>
                    </div>
                    <h2 style="margin: 0.5rem 0 0.25rem;"><?= h(pv(isset($student['full_name']) ? $student['full_name'] : null)) ?></h2>
                    <p style="color: #64748b; margin: 0; font-size: 0.9rem;">ID: <strong><?= h(pv(isset($student['id']) ? $student['id'] : null)) ?></strong></p>
                    <div style="margin-top: 1rem;">
                        <span class="badge <?php if ($academic_status == 'Probation'): ?>badge-failed<?php else: ?>badge-passed<?php endif; ?>">
                            <?= h(pv($academic_status)) ?>
                        </span>
                    </div>
                    <?php if (truthy(isset($student['prereq_status']) ? $student['prereq_status'] : null)): ?>
                    <div style="margin-top: 0.5rem;">
                        <span class="badge badge-warning">
                            <i class="fas fa-exclamation-triangle"></i> Pre-req Required
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- PROMINENT UNITS DISPLAY -->
                <div class="units-display">
                    <h4><i class="fas fa-graduation-cap"></i> Total Units Earned</h4>
                    <div class="units-number"><?= h(pv(isset($totals['Total']) ? $totals['Total'] : null)) ?></div>
                    <div class="units-label">of <?= h(isset($totals['Required']) ? $totals['Required'] : 'Program') ?> Required Units</div>
                </div>

                <div class="stats">
                    <div class="stat-row"><span>Program</span> <span class="stat-val"><?= h(pv(isset($student['course']) ? $student['course'] : null)) ?></span></div>
                    <div class="stat-row"><span>Classification</span> <span class="stat-val"><?= h(pv(isset($student['type']) ? $student['type'] : null)) ?></span></div>
                </div>

                <div style="margin-top: 1.5rem; display: grid; gap: 0.75rem;">
                    <button class="btn btn-blue" onclick="openProfileModal()"><i class="fas fa-user-pen"></i> Edit Profile</button>
                    <button class="btn btn-outline" onclick="openCreditModal()"><i class="fas fa-right-left"></i> Credit Subject</button>
                    <button class="btn btn-primary" onclick="generateReport()"><i class="fas fa-print"></i> Generate Report</button>
                </div>

                <div style="margin-top: 2rem; background: #f8fafc; padding: 1.25rem; border-radius: 0.75rem;">
                    <h4 style="margin: 0 0 1rem; font-size: 0.75rem; text-transform: uppercase; color: #64748b;">Curriculum Progress</h4>
                    <div class="stat-row"><span>Core</span> <span class="stat-val"><?= h(pv(isset($totals['Core']) ? $totals['Core'] : null)) ?> units</span></div>
                    <div class="stat-row"><span>Major</span> <span class="stat-val"><?= h(pv(isset($totals['Major']) ? $totals['Major'] : null)) ?> units</span></div>
                    <div class="stat-row"><span>Pre-requisite</span> <span class="stat-val"><?= h(pv($totals['Pre-requisite'])) ?> units</span></div>
                    <div class="stat-row" style="border-bottom: 2px solid #e2e8f0; margin-bottom: 0.5rem;"><span>Other</span> <span class="stat-val"><?= $totals['Cognates'] + $totals['Institutional'] ?> units</span></div>
                    <div class="stat-row" style="border: none;">
                        <span style="font-weight: 700;">TOTAL UNITS</span>
                        <span class="stat-val" style="font-size: 1.25rem; color: var(--accent);"><?= h(pv(isset($totals['Total']) ? $totals['Total'] : null)) ?></span>
                    </div>
                    <?php if (truthy($gwa)): ?>
                    <div class="stat-row" style="border-top: 1px dashed #cbd5e1; margin-top: 0.5rem; padding-top: 1rem;">
                        <span style="font-weight: 700;">GWA</span>
                        <span class="stat-val" style="color: var(--success); font-size: 1.25rem;"><?= number_format((float)$gwa, 2, '.', '') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="content">
            <!-- PRINT STUDENT INFO - Only visible when printing -->
            <div class="print-only">
                <table class="print-student-info">
                    <tr>
                        <td class="label">Student Name:</td>
                        <td class="value"><?= h(pv(isset($student['full_name']) ? $student['full_name'] : null)) ?></td>
                        <td class="label">ID Number:</td>
                        <td class="value"><?= h(pv(isset($student['id']) ? $student['id'] : null)) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Program:</td>
                        <td class="value"><?= h(pv(isset($student['course']) ? $student['course'] : null)) ?></td>
                        <td class="label">Classification:</td>
                        <td class="value"><?= h(pv(isset($student['type']) ? $student['type'] : null)) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Academic Status:</td>
                        <td class="value"><?= h(pv($academic_status)) ?></td>
                        <td class="label">Date Printed:</td>
                        <td class="value" id="printDate"></td>
                    </tr>
                </table>

                <!-- PRINT SUMMARY -->
                <div class="print-summary">
                    <div class="summary-item">
                        <div class="summary-label">Core Units</div>
                        <div class="summary-value"><?= h(pv(isset($totals['Core']) ? $totals['Core'] : null)) ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Major Units</div>
                        <div class="summary-value"><?= h(pv(isset($totals['Major']) ? $totals['Major'] : null)) ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Pre-req Units</div>
                        <div class="summary-value"><?= h(pv($totals['Pre-requisite'])) ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Total Units</div>
                        <div class="summary-value"><?= h(pv(isset($totals['Total']) ? $totals['Total'] : null)) ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">GWA</div>
                        <div class="summary-value"><?= truthy($gwa) ? number_format((float)$gwa, 2, '.', '') : 'N/A' ?></div>
                    </div>
                </div>
            </div>

            <!-- Prerequisites Warning Section -->
            <?php if (truthy($prereq_warnings)): ?>
            <div class="warning-box no-print">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Prerequisites Not Met:</strong> The following subjects have unmet prerequisites:
                <ul style="margin: 0.5rem 0 0 1.5rem;">
                    <?php $__n1 = 0; foreach ($prereq_warnings as $course => $missing): $__n1++; ?>
                    <li><strong><?= h(pv($course)) ?></strong> requires: <?= h(implode(', ', $missing)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div class="card no-print">
                <h3 style="margin-top: 0;"><i class="fas fa-list-check" style="color: var(--success);"></i> Graduation & Enrollment Checklist</h3>
                
                <form id="checklistForm" action="<?= url('/student/' . rawurlencode($student['id']) . '/requirements') ?>" method="POST">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                        <div class="req-group">
                            <h4>Admission</h4>
                            <?php foreach (array(array('Letter of Intent', 'adm_intent'), array('Referral Letter', 'adm_referral'), array('OTR', 'adm_otr'), array('Birth / Marriage Certificate', 'adm_birth_marriage'), array('Transfer Credentials', 'adm_transfer'), array('Good Moral Certificate', 'adm_moral')) as list($label, $key)): ?>
                            <label class="req-label">
                                <input type="checkbox" name="<?= h(pv($key)) ?>" value="true" 
                                <?php if (req_checked($student, $key)): ?>checked<?php endif; ?>>
                                <?= h(pv($label)) ?>
                            </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="req-group">
                            <h4>Research</h4>
                            <?php foreach (array(array('Paper Presentation', 'res_present'), array('Publication', 'res_pub')) as list($label, $key)): ?>
                            <label class="req-label">
                                <input type="checkbox" name="<?= h(pv($key)) ?>" value="true" 
                                <?php if (req_checked($student, $key)): ?>checked<?php endif; ?>>
                                <?= h(pv($label)) ?>
                            </label>
                            <?php endforeach; ?>
                            <div style="margin-top: 1rem;">
                                <label style="font-size: 0.8rem; font-weight: 700;">Notes:</label>
                                <textarea name="res_notes" class="form-control" rows="3" placeholder="Research notes..."><?= h(pv(isset($student['req_checklist']['res_notes']) ? $student['req_checklist']['res_notes'] : '')) ?></textarea>
                            </div>
                        </div>

                        <div class="req-group">
                            <h4>Scholarship</h4>
                            <?php foreach (array(array('SIKAP Grantee', 'sch_sikap'), array('PanpacificU Scholar', 'sch_pan'), array('BuCor 50% Tuition Fee Discount', 'sch_bucor'), array('Alumni 20% Discount', 'sch_alumni'), array('Study Now, Pay Later', 'sch_snpl')) as list($label, $key)): ?>
                            <label class="req-label">
                                <input type="checkbox" name="<?= h(pv($key)) ?>" value="true" 
                                <?php if (req_checked($student, $key)): ?>checked<?php endif; ?>>
                                <?= h(pv($label)) ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem; width: 100%; padding: 1rem; background: var(--success);">
                        <i class="fas fa-save"></i> Save Checklist
                    </button>
                </form>
            </div>

            <!-- PRINT CHECKLIST - Only visible when printing -->
            <div class="print-only">
                <h3><i class="fas fa-list-check"></i> Graduation & Enrollment Checklist</h3>
                <div class="print-checklist">
                    <div class="print-checklist-group">
                        <h4>Admission Requirements</h4>
                        <?php foreach (array(array('Letter of Intent', 'adm_intent'), array('Referral Letter', 'adm_referral'), array('OTR', 'adm_otr'), array('Birth/Marriage Cert', 'adm_birth_marriage'), array('Transfer Credentials', 'adm_transfer'), array('Good Moral Cert', 'adm_moral')) as list($label, $key)): ?>
                        <div class="print-checklist-item">
                            <span class="checkbox <?php if (req_checked($student, $key)): ?>checked<?php endif; ?>"></span>
                            <?= h(pv($label)) ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="print-checklist-group">
                        <h4>Research Requirements</h4>
                        <?php foreach (array(array('Paper Presentation', 'res_present'), array('Publication', 'res_pub')) as list($label, $key)): ?>
                        <div class="print-checklist-item">
                            <span class="checkbox <?php if (req_checked($student, $key)): ?>checked<?php endif; ?>"></span>
                            <?= h(pv($label)) ?>
                        </div>
                        <?php endforeach; ?>
                        <?php if (truthy(isset($student['req_checklist']['res_notes']) ? $student['req_checklist']['res_notes'] : null)): ?>
                        <div style="margin-top: 10px; font-size: 8pt; border-top: 1px solid #ccc; padding-top: 5px;">
                            <strong>Notes:</strong> <?= h(pv(isset($student['req_checklist']['res_notes']) ? $student['req_checklist']['res_notes'] : '')) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="print-checklist-group">
                        <h4>Scholarship</h4>
                        <?php foreach (array(array('SIKAP Grantee', 'sch_sikap'), array('PanpacificU Scholar', 'sch_pan'), array('BuCor 50% Discount', 'sch_bucor'), array('Alumni 20% Discount', 'sch_alumni'), array('SNPL', 'sch_snpl')) as list($label, $key)): ?>
                        <div class="print-checklist-item">
                            <span class="checkbox <?php if (req_checked($student, $key)): ?>checked<?php endif; ?>"></span>
                            <?= h(pv($label)) ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0;">
                    <i class="fas fa-book-open" style="color: var(--accent);"></i> 
                    Academic Subject Records
                </h3>
                
                <?php $__n2 = 0; foreach ($organized_subjects as $term => $subjects): $__n2++; ?>
                <div style="margin-bottom: 1.5rem;">
                    <div class="term-header no-print">
                        <span><?= h(pv($term)) ?></span>
                        <?php if (truthy(isset($term_gwas[$term]) ? $term_gwas[$term] : null)): ?>
                        <span class="term-gwa">Term GWA: <?= number_format((float)$term_gwas[$term], 2, '.', '') ?></span>
                        <?php endif; ?>
                    </div>
                    <!-- Print version of term header -->
                    <div class="print-only" style="font-weight: bold; border-bottom: 1px solid #000; padding: 5px 0; margin-top: 15px; background: #e0e0e0; padding: 8px;">
                        <?= h(pv($term)) ?>
                    </div>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th width="12%">Code</th>
                                    <th width="43%">Subject Description</th>
                                    <th width="10%">Units</th>
                                    <th width="10%">Grade</th>
                                    <th width="15%">Status</th>
                                    <th width="10%" class="no-print">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__n3 = 0; foreach ($subjects as $sub): $__n3++; ?>
                                <?php $record = isset($grade_map[$sub['code']]) ? $grade_map[$sub['code']] : null; ?>
                                <tr>
                                    <td style="font-weight: 700; color: var(--accent);"><?= h(pv(isset($sub['code']) ? $sub['code'] : null)) ?></td>
                                    <td>
                                        <div style="font-weight: 500;"><?= h(pv(isset($sub['title']) ? $sub['title'] : null)) ?></div>
                                        <?php if (isset($prereq_warnings[$sub['code']])): ?>
                                        <div style="font-size: 0.75rem; color: var(--warning); margin-top: 4px;" class="no-print">
                                            <i class="fas fa-exclamation-circle"></i> Prerequisites not met
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= h(pv(isset($sub['units']) ? $sub['units'] : null)) ?></td>
                                    <td><span style="font-family: monospace; font-weight: 700;"><?= $record ? h(pv($record['grade'])) : '-' ?></span></td>
                                    <td>
                                        <?php if ($record): ?>
                                            <?php if ($record['status'] == 'CREDITED'): ?>
                                                <span class="badge badge-credited">CREDITED</span>
                                            <?php elseif ($record['status'] == 'Passed'): ?>
                                                <span class="badge badge-passed">Passed</span>
                                            <?php elseif ($record['status'] == 'Failed'): ?>
                                                <span class="badge badge-failed">Failed</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning"><?= h(pv(isset($record['status']) ? $record['status'] : null)) ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: #cbd5e1;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="no-print">
                                        <button class="btn btn-outline" style="padding: 0.4rem;" onclick="openGradeModal(<?= h(json_encode($sub['code'])) ?>, <?= h(json_encode($record ? pv($record['grade']) : '')) ?>)">
                                            <i class="fas fa-pen-to-square"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Manual/Prerequisite Subjects -->
            <?php if (truthy($manual_subjects)): ?>
            <div class="card">
                <h3><i class="fas fa-layer-group" style="color: var(--warning);"></i> Pre-requisite / Bridging Subjects</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Title</th>
                            <th>Units</th>
                            <th>Grade</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th class="no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__n4 = 0; foreach ($manual_subjects as $sub): $__n4++; ?>
                        <tr>
                            <td><strong><?= h(pv(isset($sub['course_code']) ? $sub['course_code'] : null)) ?></strong></td>
                            <td><?= h(pv(isset($sub['course_title']) ? $sub['course_title'] : null)) ?></td>
                            <td><?= h(pv(isset($sub['units']) ? $sub['units'] : null)) ?></td>
                            <td><?= h(pv(isset($sub['grade']) ? $sub['grade'] : null)) ?></td>
                            <td>
                                <?php if ($sub['status'] == 'Passed'): ?>
                                    <span class="badge badge-passed">Passed</span>
                                <?php else: ?>
                                    <span class="badge badge-failed">Failed</span>
                                <?php endif; ?>
                            </td>
                            <td><?= h(pv(isset($sub['category']) ? $sub['category'] : null)) ?></td>
                            <td class="no-print">
                                <button type="button" class="btn btn-blue" style="padding: 6px 12px; font-size: 12px;" 
                                        onclick="editSubject(<?= h(json_encode(pv($sub['course_code']))) ?>, <?= h(json_encode(pv($sub['course_title']))) ?>, <?= h(pv($sub['units'])) ?>, <?= h(pv($sub['grade'])) ?>, <?= h(json_encode(pv($sub['category']))) ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" action="<?= url('/delete_manual_subject/' . rawurlencode($student['id']) . '/' . rawurlencode($sub['course_code'])) ?>" 
                                      style="display: inline;" onsubmit="return confirm('Delete this subject?');">
                                    <button type="submit" class="btn btn-del" style="padding: 6px 12px; font-size: 12px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Add Manual Subject Form -->
            <?php if (truthy(isset($student['prereq_status']) ? $student['prereq_status'] : null) || (isset($student['type']) ? $student['type'] : null) === 'Transferee'): ?>
            <div class="card prereq-section no-print">
                <div class="prereq-header">
                    <i class="fas fa-plus-circle"></i>
                    <h3>Add Manual / Pre-requisite Subject</h3>
                </div>
                <div style="padding: 20px;">
                    <form id="subjectForm" action="<?= url('/add_manual_subject/' . rawurlencode($student['id'])) ?>" method="POST"
                          class="form-block">
                        <div style="display: grid; grid-template-columns: 1fr 2fr 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Subject Code</label>
                                <input type="text" name="subject_code" id="inputCode" placeholder="PRE-101" required 
                                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Subject Title</label>
                                <input type="text" name="subject_title" id="inputTitle" placeholder="Bridging Math" required 
                                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Units</label>
                                <input type="number" name="units" id="inputUnits" step="0.1" placeholder="3.0" required 
                                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Grade</label>
                                <input type="number" name="grade" id="inputGrade" step="0.01" placeholder="85.0" required 
                                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Category</label>
                                <select name="type" id="inputCategory" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                                    <option value="Pre-requisite">Pre-requisite</option>
                                    <option value="Bridging">Bridging</option>
                                    <option value="Enrichment">Enrichment</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-blue" style="padding: 10px 20px; height: 42px;">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <!-- PRINT SIGNATURES - Only visible when printing -->
            <div class="print-only print-signatures">
                <div class="signature-line">
                    <strong>Student Signature</strong><br>
                    <span style="font-size: 8pt;"><?= h(pv(isset($student['full_name']) ? $student['full_name'] : null)) ?></span><br>
                    Date: _______________
                </div>
                <div class="signature-line">
                    <strong>Registrar/Evaluator</strong><br>
                    <span style="font-size: 8pt;">Office of the Registrar</span><br>
                    Date: _______________
                </div>
            </div>

            <!-- PRINT FOOTER - Only visible when printing -->
            <div class="print-only print-footer">
                <p><strong>This is an official document from Panpacific University Graduate School.</strong></p>
                <p>Any alteration renders this document invalid. Keep this copy for your records.</p>
                <p>Document ID: <?= h(pv(isset($student['id']) ? $student['id'] : null)) ?>-<?= h(pv(isset($totals['Total']) ? $totals['Total'] : null)) ?> | Printed on: <span id="footerDate"></span></p>
            </div>

        </div>
    </div>

    <!-- Modals -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeProfileModal()">&times;</span>
            <h3 style="margin-top: 0; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Update Profile</h3>
            <form action="<?= url('/student/' . rawurlencode($student['id'])) ?>" method="POST">
                <input type="hidden" name="action" value="edit_profile">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" class="form-control" value="<?= h(pv(isset($student['id']) ? $student['id'] : null)) ?>" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group"><label>First Name</label><input type="text" name="fname" class="form-control" value="<?= h(pv(isset($student['fname']) ? $student['fname'] : null)) ?>" required></div>
                    <div class="form-group"><label>Last Name</label><input type="text" name="lname" class="form-control" value="<?= h(pv(isset($student['lname']) ? $student['lname'] : null)) ?>" required></div>
                </div>
                <div class="form-group">
                    <label>Course</label>
                    <select name="course" class="form-control">
                        <?php $__n5 = 0; foreach (array("MBA", "MPA", "MAED-EM", "MAED-SPED", "MSC-CRIM", "PHD-PA", "PHD-DEM") as $c): $__n5++; ?>
                        <option value="<?= h(pv($c)) ?>" <?php if ((isset($student['course']) ? $student['course'] : null) === $c): ?>selected<?php endif; ?>><?= h(pv($c)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Student Status</label>
                    <select name="type" class="form-control">
                        <option value="New Enrollee" <?php if ((isset($student['type']) ? $student['type'] : null) === 'New Enrollee'): ?>selected<?php endif; ?>>New Enrollee</option>
                        <option value="Transferee" <?php if ((isset($student['type']) ? $student['type'] : null) === 'Transferee'): ?>selected<?php endif; ?>>Transferee</option>
                        <option value="Continuing" <?php if ((isset($student['type']) ? $student['type'] : null) === 'Continuing'): ?>selected<?php endif; ?>>Continuing</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="prereq_met" value="true" <?php if (truthy(isset($student['prereq_status']) ? $student['prereq_status'] : null)): ?>checked<?php endif; ?>>
                        Requires Pre-requisites/Bridging
                    </label>
                </div>
                <button type="submit" class="btn btn-blue" style="width: 100%; padding: 0.8rem;">Update Information</button>
            </form>
        </div>
    </div>

    <div id="gradeModal" class="modal">
        <div class="modal-content" style="max-width: 350px;">
            <span class="close-btn" onclick="closeGradeModal()">&times;</span>
            <h3 id="gTitle" style="margin-top: 0;">Grade Entry</h3>
            <form action="<?= url('/student/' . rawurlencode($student['id'])) ?>" method="POST">
                <input type="hidden" name="action" value="update_grade">
                <input type="hidden" name="course_code" id="gCode">
                <div class="form-group">
                    <label>Numerical Grade</label>
                    <input type="number" step="0.01" name="grade" id="gInput" class="form-control" placeholder="e.g. 88.50" required>
                </div>
                <button type="submit" class="btn btn-blue" style="width: 100%;">Post Grade</button>
            </form>
        </div>
    </div>

    <div id="creditModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeCreditModal()">&times;</span>
            <h3 style="margin-top: 0;">External Credit Registration</h3>
            <form action="<?= url('/student/' . rawurlencode($student['id']) . '/credit') ?>" method="POST">
                <div class="form-group">
                    <label>Original Subject Code</label>
                    <input type="text" name="prev_code" class="form-control" placeholder="e.g. MATH 101" required>
                </div>
                <div class="form-group">
                    <label>Original Subject Description</label>
                    <input type="text" name="prev_title" class="form-control" placeholder="e.g. College Algebra" required>
                </div>
                <div class="form-group">
                    <label>Grade Obtained</label>
                    <input type="number" step="0.01" name="grade" class="form-control" placeholder="e.g. 92.00" required>
                </div>
                <div class="form-group">
                    <label>Internal Equivalent</label>
                    <select name="course_code" id="eqSelect" class="form-control" required>
                        <option value="">-- Select Equivalent Subject --</option>
                        <?php $__n6 = 0; foreach ($organized_subjects as $term => $subs): $__n6++; ?>
                        <optgroup label="<?= h(pv($term)) ?>">
                            <?php $__n7 = 0; foreach ($subs as $s): $__n7++; ?><option value="<?= h(pv(isset($s['code']) ? $s['code'] : null)) ?>" data-title="<?= h(pv(isset($s['title']) ? $s['title'] : null)) ?>" data-units="<?= h(pv(isset($s['units']) ? $s['units'] : null)) ?>" data-type="<?= h(pv(isset($s['type']) ? $s['type'] : null)) ?>"><?= h(pv(isset($s['code']) ? $s['code'] : null)) ?> - <?= h(pv(isset($s['title']) ? $s['title'] : null)) ?></option><?php endforeach; ?>
                        </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="hidden" name="course_title" id="eqTitle">
                <input type="hidden" name="units" id="eqUnits">
                <input type="hidden" name="category" id="eqType">
                <button type="submit" class="btn btn-primary" style="width: 100%; background: var(--success);">Apply Credit</button>
            </form>
        </div>
    </div>

    <script>
        function openProfileModal() { document.getElementById('profileModal').style.display = 'flex'; }
        function closeProfileModal() { document.getElementById('profileModal').style.display = 'none'; }
        function openCreditModal() { document.getElementById('creditModal').style.display = 'flex'; }
        function closeCreditModal() { document.getElementById('creditModal').style.display = 'none'; }
        function openGradeModal(code, grade) {
            document.getElementById('gCode').value = code;
            document.getElementById('gTitle').innerText = "Grade for " + code;
            document.getElementById('gInput').value = grade;
            document.getElementById('gradeModal').style.display = 'flex';
        }
        function closeGradeModal() { document.getElementById('gradeModal').style.display = 'none'; }

        function generateReport() {
            // Set dates
            const now = new Date();
            const dateStr = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('printDate').textContent = dateStr;
            document.getElementById('footerDate').textContent = dateStr;
            
            // Print
            window.print();
        }

        const checklistForm = document.getElementById('checklistForm');
        if(checklistForm) {
            checklistForm.addEventListener('submit', function(e) {
                const checkboxes = this.querySelectorAll('input[type="checkbox"]');
                checkboxes.forEach(cb => {
                    if(!cb.checked) {
                        let hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = cb.name;
                        hidden.value = 'false';
                        this.appendChild(hidden);
                    }
                });
            });
        }

        const eqSelect = document.getElementById('eqSelect');
        if(eqSelect) {
            eqSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if(opt && opt.value) {
                    document.getElementById('eqTitle').value = opt.dataset.title || "";
                    document.getElementById('eqUnits').value = opt.dataset.units || "";
                    document.getElementById('eqType').value = opt.dataset.type || "";
                }
            });
        }

        window.onclick = function(e) { 
            if(e.target.classList.contains('modal')) e.target.style.display = 'none'; 
        }

        function editSubject(code, title, units, grade, category) {
            document.getElementById('inputCode').value = code;
            document.getElementById('inputTitle').value = title;
            document.getElementById('inputUnits').value = units;
            document.getElementById('inputGrade').value = grade;
            document.getElementById('inputCategory').value = category;
            document.getElementById('inputCode').focus();
        }
    </script>
    <script src="<?= url('/static/app.js') ?>"></script>
</body>
</html>