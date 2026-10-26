<?php
/**
 * Graduate School Portal - Panpacific University
 * PHP port of university_system.py (Flask) for InfinityFree / any Apache + PHP host.
 * All routes, validation rules and calculations follow the original Flask app.
 */

ini_set('serialize_precision', '-1');
error_reporting(E_ALL);
define('DEBUG', false);              // set to true temporarily to see PHP errors on screen
ini_set('display_errors', DEBUG ? '1' : '0');
date_default_timezone_set('Asia/Manila');
ob_start();

// --- DATABASE SETUP ---------------------------------------------------------
define('DATA_DIR',   __DIR__ . '/data');
define('DATA_FILE',  DATA_DIR . '/students_db.json');
define('BACKUP_DIR', DATA_DIR . '/.backups');
define('USERS_FILE', DATA_DIR . '/users_db.json');
define('AUDIT_LOG',  DATA_DIR . '/audit_log.json');

if (!is_dir(DATA_DIR))   { @mkdir(DATA_DIR, 0755, true); }
if (!is_dir(BACKUP_DIR)) { @mkdir(BACKUP_DIR, 0755, true); }
// Make sure the JSON "database" always exists (no MySQL needed - data lives in data/*.json)
if (!file_exists(DATA_FILE)) { @file_put_contents(DATA_FILE, "[]\n", LOCK_EX); }
if (!file_exists(DATA_DIR . '/.htaccess')) {
    @file_put_contents(DATA_DIR . '/.htaccess',
        "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nOrder allow,deny\nDeny from all\n</IfModule>\n");
}

require __DIR__ . '/curriculum.php';   // defines ALL_CURRICULUMS and PREREQUISITES

// --- SESSION ----------------------------------------------------------------
if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
    session_set_cookie_params(array('lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax'));
}
session_start();

// --- BASE URL (works in site root or in a sub-folder) ----------------------
$__script = str_replace('\\', '/', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/index.php');
define('BASE_URL', rtrim(str_replace('\\', '/', dirname($__script)), '/'));

function url($p = '/') { return BASE_URL . $p; }

// --- SMALL HELPERS ----------------------------------------------------------
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

/** Render a value the way Python/Jinja would print it (floats keep ".0"). */
function pv($v) {
    if ($v === null) return '';
    if (is_bool($v)) return $v ? 'True' : 'False';
    if (is_float($v)) return json_encode($v, JSON_PRESERVE_ZERO_FRACTION);
    if (is_array($v)) return json_encode($v);
    return (string)$v;
}

/** Python-style truthiness. */
function truthy($v) {
    if ($v === null || $v === false) return false;
    if (is_int($v) || is_float($v)) return $v != 0;
    if (is_string($v)) return $v !== '';
    if (is_array($v)) return count($v) > 0;
    return true;
}

function post($k, $default = null) {
    return (isset($_POST[$k]) && is_string($_POST[$k])) ? $_POST[$k] : $default;
}

function py_title($s) {
    $r = preg_replace_callback('/\p{L}+/u', function ($m) {
        $w = $m[0];
        if (function_exists('mb_strtoupper')) {
            return mb_strtoupper(mb_substr($w, 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($w, 1, null, 'UTF-8'), 'UTF-8');
        }
        return ucfirst(strtolower($w));
    }, $s);
    return $r === null ? $s : $r;
}

function lower($s) { return function_exists('mb_strtolower') ? mb_strtolower((string)$s, 'UTF-8') : strtolower((string)$s); }

function iso_now() { $d = new DateTime('now'); return $d->format('Y-m-d\TH:i:s.u'); }

function uuid4() {
    $d = random_bytes(16);
    $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
    $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
}

function is_list_array($a) {
    if (!is_array($a)) return false;
    $i = 0;
    foreach ($a as $k => $_) { if ($k !== $i++) return false; }
    return true;
}

function redirect_to($path) {
    while (ob_get_level() > 0) { ob_end_clean(); }
    header('Location: ' . url($path));
    exit;
}

function render($__tpl, $__vars = array()) {
    extract($__vars);
    include __DIR__ . '/templates/' . $__tpl . '.php';
}

function error_page($code, $message) {
    http_response_code($code);
    render('error', array('error_code' => $code, 'error_message' => $message));
    exit;
}

// --- FLASH MESSAGES ---------------------------------------------------------
function flash($message, $category = 'message') { $_SESSION['_flashes'][] = array($category, $message); }
function get_flashed_messages() {
    $f = isset($_SESSION['_flashes']) ? $_SESSION['_flashes'] : array();
    unset($_SESSION['_flashes']);
    return $f;
}

// --- JSON FILE HELPERS ------------------------------------------------------
function json_pretty($data) {
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
}
function write_json($path, $data) { file_put_contents($path, json_pretty($data), LOCK_EX); }

// --- AUTHENTICATION SYSTEM --------------------------------------------------
function load_users() {
    if (!file_exists(USERS_FILE)) {
        $default_users = array(array(
            'id' => uuid4(),
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'name' => 'System Administrator',
            'created_at' => iso_now(),
        ));
        write_json(USERS_FILE, $default_users);
        return $default_users;
    }
    $d = json_decode((string)@file_get_contents(USERS_FILE), true);
    return is_array($d) ? array_values($d) : array();
}

function save_users($users) { write_json(USERS_FILE, array_values($users)); }

function find_user_by($field, $value, $users) {
    foreach ($users as $u) { if (isset($u[$field]) && $u[$field] === $value) return $u; }
    return null;
}

function login_required() {
    if (!isset($_SESSION['user_id'])) {
        flash('Please log in to access this page.', 'warning');
        redirect_to('/login');
    }
}

function admin_required() {
    if (!isset($_SESSION['user_id'])) {
        flash('Please log in to access this page.', 'warning');
        redirect_to('/login');
    }
    $user = find_user_by('id', $_SESSION['user_id'], load_users());
    if (!$user || (isset($user['role']) ? $user['role'] : null) !== 'admin') {
        flash('Admin access required.', 'danger');
        redirect_to('/');
    }
}

// --- AUDIT LOGGING ----------------------------------------------------------
function log_action($action, $details, $student_id = null) {
    $entry = array(
        'id' => uuid4(),
        'timestamp' => iso_now(),
        'user' => isset($_SESSION['username']) ? $_SESSION['username'] : 'anonymous',
        'action' => $action,
        'details' => $details,
        'student_id' => $student_id,
        'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
    );
    $logs = array();
    if (file_exists(AUDIT_LOG)) {
        $d = json_decode((string)@file_get_contents(AUDIT_LOG), true);
        if (is_array($d)) $logs = array_values($d);
    }
    $logs[] = $entry;
    write_json(AUDIT_LOG, array_slice($logs, -1000));   // Keep last 1000 entries
}

// --- DATABASE HELPERS -------------------------------------------------------
function load_data() {
    if (!file_exists(DATA_FILE)) return array();
    $raw = @file_get_contents(DATA_FILE);
    $data = json_decode((string)$raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        flash('Error loading database: ' . json_last_error_msg(), 'danger');
        return array();
    }
    return is_list_array($data) ? $data : array();
}

function save_data($data) {
    $ts = date('Ymd_His');
    if (file_exists(DATA_FILE)) {
        @copy(DATA_FILE, BACKUP_DIR . "/backup_{$ts}.json");
    }
    write_json(DATA_FILE, array_values($data));
}

function find_student_index($students, $sid) {
    foreach ($students as $i => $s) {
        if (isset($s['id']) && (string)$s['id'] === (string)$sid) return $i;
    }
    return null;
}

function validate_student_id($student_id) {
    if (!$student_id) return array(false, 'Student ID is required');
    if (strlen($student_id) < 4 || strlen($student_id) > 20) return array(false, 'Student ID must be between 4 and 20 characters');
    if (!preg_match('/^[A-Za-z0-9\-_]+$/', $student_id)) return array(false, 'Student ID can only contain letters, numbers, hyphens, and underscores');
    return array(true, 'Valid');
}

function validate_grade($grade) {
    if (!is_string($grade) && !is_numeric($grade)) return array(false, 'Invalid grade format');
    if (!is_numeric(trim((string)$grade))) return array(false, 'Invalid grade format');
    $g = (float)$grade;
    if ($g < 0 || $g > 100) return array(false, 'Grade must be between 0 and 100');
    return array(true, 'Valid');
}

function is_num($v) { return is_int($v) || is_float($v); }

function curriculum_for($course) {
    $c = ALL_CURRICULUMS;
    return isset($c[$course]) ? $c[$course] : $c['MBA'];
}

/** Check if student has completed prerequisites for a course. */
function check_prerequisites($student, $course_code) {
    $pre = PREREQUISITES;
    $required = isset($pre[$course_code]) ? $pre[$course_code] : array();
    if (!$required) return array(true, array());

    // Prerequisites are alternatives per program (e.g. THW I lists the research-methods
    // course of every program), so only the ones that exist in this student's own
    // curriculum are required.
    $program_codes = array();
    foreach (curriculum_for(isset($student['course']) ? $student['course'] : 'MBA') as $sub) { $program_codes[] = $sub['code']; }
    $filtered = array();
    foreach ($required as $r) { if (in_array($r, $program_codes, true)) $filtered[] = $r; }
    if (!$filtered) return array(true, array());
    $required = $filtered;

    $passed = array();
    $grades = isset($student['grades']) && is_array($student['grades']) ? $student['grades'] : array();
    foreach ($grades as $g) {
        $st = isset($g['status']) ? $g['status'] : null;
        $gr = isset($g['grade']) ? $g['grade'] : null;
        if (in_array($st, array('Passed', 'CREDITED'), true)
            || (is_num($gr) && $gr >= 75)
            || (is_num($gr) && $gr >= 1.0 && $gr <= 3.0)) {
            $passed[] = $g['course_code'];
        }
    }
    $missing = array();
    foreach ($required as $r) { if (!in_array($r, $passed, true)) $missing[] = $r; }
    return array(count($missing) === 0, $missing);
}

// --- CALCULATION FUNCTIONS --------------------------------------------------
function calculate_assessment($student) {
    $grades = (isset($student['grades']) && $student['grades']) ? $student['grades'] : array();
    $totals = array(
        'Core' => 0, 'Major' => 0, 'Cognates' => 0,
        'Institutional' => 0, 'Comprehensive' => 0, 'Research' => 0,
        'Thesis' => 0, 'Dissertation' => 0, 'Capstone' => 0, 'Pre-requisite' => 0,
        'Bridging' => 0, 'Enrichment' => 0, 'Total' => 0,
        'GWA' => 0.0,
    );
    $weighted_sum = 0.0;
    $units_for_gwa = 0.0;

    foreach ($grades as $g) {
        if (!is_array($g)) continue;
        $u = isset($g['units']) ? $g['units'] : 0;
        $gr = isset($g['grade']) ? $g['grade'] : 0;
        if (!is_numeric($u) || !is_numeric($gr)) continue;
        $units = (float)$u;
        $grade = (float)$gr;
        $status = isset($g['status']) ? $g['status'] : '';

        if (in_array($status, array('Passed', 'CREDITED'), true)) {
            $cat = isset($g['category']) ? $g['category'] : 'Others';
            if (!is_string($cat)) continue;
            if (array_key_exists($cat, $totals)) {
                $totals[$cat] += (int)$units;
            } elseif (strpos($cat, 'Pre') !== false || strpos($cat, 'Bridging') !== false) {
                $totals['Pre-requisite'] += (int)$units;
            }
            $totals['Total'] += (int)$units;
        }
        if ($grade > 0) {
            $weighted_sum += ($grade * $units);
            $units_for_gwa += $units;
        }
    }
    if ($units_for_gwa > 0) {
        $totals['GWA'] = round($weighted_sum / $units_for_gwa, 2);
    }

    $has_failed = false;
    foreach ($grades as $g) {
        $st = isset($g['status']) ? $g['status'] : null;
        $gr = isset($g['grade']) ? $g['grade'] : null;
        if ($st === 'Failed' || (is_num($gr) && $gr < 75 && $gr > 3.0)) { $has_failed = true; break; }
    }
    $totals['AcademicStatus'] = $has_failed ? 'Probation' : 'Good Standing';
    $totals['HasFailures'] = $has_failed;
    return $totals;
}

/** Calculate GWA for a specific term. */
function get_term_gwa($student, $term) {
    $rows = array();
    $grades = isset($student['grades']) ? $student['grades'] : array();
    foreach ($grades as $g) {
        if ((isset($g['term']) ? $g['term'] : null) === $term
            && isset($g['grade']) && is_numeric($g['grade']) && (float)$g['grade'] > 0) {
            $rows[] = $g;
        }
    }
    if (!$rows) return null;
    $weighted = 0.0; $units = 0.0;
    foreach ($rows as $g) { $weighted += (float)$g['grade'] * (float)$g['units']; $units += (float)$g['units']; }
    return $units > 0 ? round($weighted / $units, 2) : null;
}

/** Template helper: is a checklist item ticked? */
function req_checked($student, $key) {
    $v = (isset($student['req_checklist']) && is_array($student['req_checklist']) && array_key_exists($key, $student['req_checklist']))
        ? $student['req_checklist'][$key] : null;
    return strtolower(pv($v)) === 'true';
}

// --- ROUTES -----------------------------------------------------------------

function route_login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim(post('username', ''));
        $password = post('password', '');
        $users = load_users();
        $user = find_user_by('username', $username, $users);

        if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = isset($user['role']) ? $user['role'] : 'user';
            log_action('LOGIN', "User {$username} logged in");
            $display = array_key_exists('name', $user) ? $user['name'] : $username;
            flash('Welcome, ' . $display . '!', 'success');
            redirect_to('/');
        } else {
            flash('Invalid username or password.', 'danger');
            log_action('LOGIN_FAILED', "Failed login attempt for {$username}");
        }
    }
    render('login');
}

function route_logout() {
    if (isset($_SESSION['username'])) {
        log_action('LOGOUT', "User {$_SESSION['username']} logged out");
    }
    $_SESSION = array();
    flash('You have been logged out.', 'info');
    redirect_to('/login');
}

function route_index() {
    login_required();
    try {
        $students = load_data();
        $decorated = array();
        foreach ($students as $i => $s) {
            $k = truthy(isset($s['lname']) ? $s['lname'] : null) ? $s['lname']
               : (truthy(isset($s['full_name']) ? $s['full_name'] : null) ? $s['full_name'] : '');
            $decorated[] = array(lower((string)$k), $i, $s);
        }
        usort($decorated, function ($a, $b) {
            $c = strcmp($a[0], $b[0]);
            return $c !== 0 ? $c : ($a[1] - $b[1]);
        });
        $students = array();
        foreach ($decorated as $d) { $students[] = $d[2]; }

        // Pagination
        $page = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_VALIDATE_INT) : 1;
        if ($page === false || $page === null || $page < 1) $page = 1;
        $per_page = 20;
        $total = count($students);
        $paginated = array_slice($students, ($page - 1) * $per_page, $per_page);

        $attention_required = array();
        foreach ($students as $s) {
            $failed = false;
            $gs = isset($s['grades']) && is_array($s['grades']) ? $s['grades'] : array();
            foreach ($gs as $g) { if ((isset($g['status']) ? $g['status'] : null) === 'Failed') { $failed = true; break; } }
            if ($failed) $attention_required[] = $s;
        }

        // Search
        $search_query = lower(trim(isset($_GET['q']) && is_string($_GET['q']) ? $_GET['q'] : ''));
        if ($search_query !== '') {
            $paginated = array();
            foreach ($students as $s) {
                $name = lower(isset($s['full_name']) ? $s['full_name'] : '');
                $id = lower(isset($s['id']) ? $s['id'] : '');
                if (strpos($name, $search_query) !== false || strpos($id, $search_query) !== false) $paginated[] = $s;
            }
        }

        render('index', array(
            'students' => $paginated,
            'attention_required' => $attention_required,
            'page' => $page,
            'total_pages' => max(1, (int)floor(($total + $per_page - 1) / $per_page)),
            'total_students' => $total,
            'search_query' => $search_query,
        ));
    } catch (Exception $e) {
        error_log('Error in index route: ' . $e->getMessage());
        http_response_code(500);
        echo 'Error: ' . h($e->getMessage());
    }
}

function route_profile($sid) {
    login_required();
    $students = load_data();
    $idx = find_student_index($students, $sid);
    if ($idx === null) {
        flash('Student not found.', 'danger');
        redirect_to('/');
    }
    $student =& $students[$idx];
    $self = '/student/' . rawurlencode($sid);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = post('action');

        if ($action === 'edit_profile') {
            $fname = py_title(trim(post('fname', '')));
            $lname = py_title(trim(post('lname', '')));
            $course = strtoupper(post('course', ''));

            if ($fname === '' || $lname === '') {
                flash('First and last name are required.', 'danger');
                redirect_to($self);
            }
            $student['lname'] = $lname;
            $student['fname'] = $fname;
            $student['full_name'] = "{$lname}, {$fname}";
            $student['course'] = $course;
            $student['type'] = post('type');
            // IMPORTANT: Update prereq_status based on checkbox
            $student['prereq_status'] = truthy(post('prereq_met')) ? true : false;
            $student['updated_at'] = iso_now();
            $student['updated_by'] = isset($_SESSION['username']) ? $_SESSION['username'] : null;

            save_data($students);
            log_action('UPDATE_PROFILE', "Updated profile for {$student['full_name']}", $sid);
            flash('Profile updated successfully.', 'success');
            redirect_to($self);

        } elseif ($action === 'update_grade') {
            $code = post('course_code');
            $raw_grade = post('grade');

            list($valid, $msg) = validate_grade($raw_grade);
            if (!$valid) {
                flash("Invalid grade: {$msg}", 'danger');
                redirect_to($self);
            }
            $grade_val = truthy($raw_grade) ? (float)$raw_grade : 0.0;

            // Determine status
            if ($grade_val >= 75 || ($grade_val >= 1.0 && $grade_val <= 3.0)) {
                $status = 'Passed';
            } elseif ($grade_val > 0) {
                $status = 'Failed';
            } else {
                $status = 'Unfinished';
            }

            // Check prerequisites
            $course_subjects = curriculum_for(isset($student['course']) ? $student['course'] : 'MBA');
            $curr_sub = null;
            foreach ($course_subjects as $s) { if ($s['code'] === $code) { $curr_sub = $s; break; } }

            if ($curr_sub && in_array($curr_sub['type'], array('Core', 'Major', 'Thesis', 'Dissertation', 'Capstone'), true)) {
                list($prereq_met, $missing) = check_prerequisites($student, $code);
                if (!$prereq_met) {
                    flash("Warning: Prerequisites not met for {$code}. Missing: " . implode(', ', $missing), 'warning');
                }
            }

            $found = false;
            if (isset($student['grades']) && is_array($student['grades'])) {
                foreach ($student['grades'] as &$g) {
                    if (isset($g['course_code']) && $g['course_code'] === $code) {
                        $g['grade'] = $grade_val;
                        $g['status'] = $status;
                        $g['updated_at'] = iso_now();
                        $g['updated_by'] = isset($_SESSION['username']) ? $_SESSION['username'] : null;
                        $found = true;
                        break;
                    }
                }
                unset($g);
            }
            if (!$found && $curr_sub) {
                if (!isset($student['grades']) || !is_array($student['grades'])) $student['grades'] = array();
                $student['grades'][] = array(
                    'course_code' => $code,
                    'course_title' => $curr_sub['title'],
                    'units' => $curr_sub['units'],
                    'category' => $curr_sub['type'],
                    'term' => $curr_sub['term'],
                    'grade' => $grade_val,
                    'status' => $status,
                    'created_at' => iso_now(),
                    'created_by' => isset($_SESSION['username']) ? $_SESSION['username'] : null,
                );
            }

            save_data($students);
            log_action('UPDATE_GRADE', "Updated grade for {$code}: " . pv($grade_val) . " ({$status})", $sid);
            flash("Grade for {$code} updated successfully.", 'success');
            redirect_to($self);
        }
    }

    $totals = calculate_assessment($student);
    $course_subjects = curriculum_for(isset($student['course']) ? $student['course'] : 'MBA');

    // Organize subjects by term
    $grade_map = array();
    $student_grades = isset($student['grades']) && is_array($student['grades']) ? $student['grades'] : array();
    foreach ($student_grades as $g) { $grade_map[$g['course_code']] = $g; }
    $organized_subjects = array();
    foreach ($course_subjects as $sub) { $organized_subjects[$sub['term']][] = $sub; }

    // Calculate term GWAs
    $term_gwas = array();
    foreach (array_keys($organized_subjects) as $term) { $term_gwas[$term] = get_term_gwa($student, $term); }

    // Get manual/prerequisite subjects - ONLY these categories
    $manual_subjects = array();
    foreach ($student_grades as $g) {
        if (in_array(isset($g['category']) ? $g['category'] : null, array('Pre-requisite', 'Bridging', 'Enrichment'), true)) $manual_subjects[] = $g;
    }

    // Check prerequisites for all subjects not yet passed
    $prereq_warnings = array();
    foreach ($organized_subjects as $term => $subjects) {
        foreach ($subjects as $sub) {
            $existing = isset($grade_map[$sub['code']]) ? $grade_map[$sub['code']] : null;
            if (!$existing || !in_array(isset($existing['status']) ? $existing['status'] : null, array('Passed', 'CREDITED'), true)) {
                list($met, $missing) = check_prerequisites($student, $sub['code']);
                if (!$met) $prereq_warnings[$sub['code']] = $missing;
            }
        }
    }

    $student_copy = $student;
    unset($student);
    render('profile', array(
        'student' => $student_copy,
        'totals' => $totals,
        'organized_subjects' => $organized_subjects,
        'grade_map' => $grade_map,
        'term_gwas' => $term_gwas,
        'manual_subjects' => $manual_subjects,
        'prereq_warnings' => $prereq_warnings,
        'academic_status' => isset($totals['AcademicStatus']) ? $totals['AcademicStatus'] : 'Good Standing',
        'gwa' => isset($totals['GWA']) ? $totals['GWA'] : 0,
    ));
}

function route_update_requirements($sid) {
    login_required();
    $students = load_data();
    $idx = find_student_index($students, $sid);
    if ($idx !== null) {
        $new_checklist = array();
        foreach ($_POST as $key => $value) {
            if (!is_string($value)) continue;
            $lv = strtolower($value);
            if ($lv === 'true' || $lv === 'on') $new_checklist[$key] = true;
            elseif ($lv === 'false') $new_checklist[$key] = false;
            else $new_checklist[$key] = $value;
        }
        $students[$idx]['req_checklist'] = $new_checklist;
        $students[$idx]['checklist_updated'] = iso_now();
        $students[$idx]['checklist_updated_by'] = isset($_SESSION['username']) ? $_SESSION['username'] : null;
        save_data($students);
        log_action('UPDATE_CHECKLIST', 'Updated requirements checklist', $sid);
        flash('Requirements checklist updated.', 'success');
    }
    redirect_to('/student/' . rawurlencode($sid));
}

function route_credit_subject($sid) {
    login_required();
    $students = load_data();
    $idx = find_student_index($students, $sid);
    if ($idx !== null) {
        $self = '/student/' . rawurlencode($sid);
        $course_code = post('course_code');
        $prev_code = post('prev_code');
        $prev_title = post('prev_title');

        // Validate inputs
        if (!truthy($course_code) || !truthy($prev_code)) {
            flash('Course code and previous course code are required.', 'danger');
            redirect_to($self);
        }

        $g_raw = post('grade', '0');
        $u_raw = post('units', '0');
        if (!is_numeric(trim($g_raw)) || !preg_match('/^\s*[+-]?\d+\s*$/', $u_raw)) {
            flash('Invalid grade or units format.', 'danger');
            redirect_to($self);
        }
        $grade = (float)$g_raw;
        $units = (int)$u_raw;

        // Remove existing grade for this course if present
        $kept = array();
        $existing = isset($students[$idx]['grades']) && is_array($students[$idx]['grades']) ? $students[$idx]['grades'] : array();
        foreach ($existing as $g) { if ($g['course_code'] !== $course_code) $kept[] = $g; }
        $students[$idx]['grades'] = $kept;

        $students[$idx]['grades'][] = array(
            'course_code' => $course_code,
            'course_title' => post('course_title'),
            'units' => $units,
            'category' => post('category'),
            'term' => 'Transferred',
            'grade' => $grade,
            'status' => 'CREDITED',
            'prev_code' => $prev_code,
            'prev_title' => $prev_title,
            'credited_at' => iso_now(),
            'credited_by' => isset($_SESSION['username']) ? $_SESSION['username'] : null,
        );

        save_data($students);
        log_action('CREDIT_SUBJECT', "Credited {$prev_code} as {$course_code}", $sid);
        flash("Subject {$course_code} credited successfully.", 'success');
    }
    redirect_to('/student/' . rawurlencode($sid));
}

function route_add_student() {
    login_required();
    $students = load_data();
    $action = post('action');

    if ($action === 'delete') {
        $target_id = post('target_id');
        $idx = find_student_index($students, $target_id);
        if ($idx !== null) {
            $name = isset($students[$idx]['full_name']) ? $students[$idx]['full_name'] : '';
            log_action('DELETE_STUDENT', "Deleted student {$name} ({$target_id})", $target_id);
            array_splice($students, $idx, 1);
            save_data($students);
            flash('Student record deleted.', 'success');
        }
        redirect_to('/');
    }

    // Add new student
    $student_id = trim(post('student_id', ''));
    $fname = trim(post('fname', ''));
    $lname = trim(post('lname', ''));

    // Validation
    list($valid, $msg) = validate_student_id($student_id);
    if (!$valid) {
        flash("Invalid Student ID: {$msg}", 'danger');
        redirect_to('/');
    }
    if ($fname === '' || $lname === '') {
        flash('First and last name are required.', 'danger');
        redirect_to('/');
    }
    // Check for duplicate ID
    if (find_student_index($students, $student_id) !== null) {
        flash("Student ID {$student_id} already exists.", 'danger');
        redirect_to('/');
    }

    $course = post('course');
    $curricula = ALL_CURRICULUMS;
    if (!is_string($course) || !isset($curricula[$course])) {
        flash('Invalid course selected.', 'danger');
        redirect_to('/');
    }

    $new_student = array(
        'id' => $student_id,
        'fname' => py_title($fname),
        'lname' => py_title($lname),
        'full_name' => py_title($lname) . ', ' . py_title($fname),
        'course' => strtoupper($course),
        'type' => post('student_type'),
        'grades' => array(),
        'req_checklist' => new stdClass(),
        'prereq_status' => truthy(post('prereq_met')) ? true : false,
        'created_at' => iso_now(),
        'created_by' => isset($_SESSION['username']) ? $_SESSION['username'] : null,
    );
    $students[] = $new_student;
    save_data($students);
    log_action('ADD_STUDENT', "Added new student {$new_student['full_name']}", $student_id);
    flash("Student {$new_student['full_name']} added successfully.", 'success');
    redirect_to('/');
}

function route_import_students() {
    login_required();
    if (!isset($_FILES['file'])) {
        flash('No file selected.', 'danger');
        redirect_to('/');
    }
    $file = $_FILES['file'];
    if (!isset($file['name']) || $file['name'] === '') {
        flash('No file selected.', 'danger');
        redirect_to('/');
    }
    // Validate file extension
    if (substr($file['name'], -4) !== '.csv') {
        flash('Only CSV files are allowed.', 'danger');
        redirect_to('/');
    }

    try {
        if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('upload failed (code ' . $file['error'] . ')');
        $content = file_get_contents($file['tmp_name']);
        if (function_exists('mb_check_encoding') && !mb_check_encoding($content, 'UTF-8')) {
            throw new Exception("'utf-8' codec can't decode file");
        }
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") $content = substr($content, 3);   // strip Excel BOM

        $fh = fopen('php://temp', 'r+');
        fwrite($fh, $content);
        rewind($fh);
        $header = null;
        $rows = array();
        $csv_ok = version_compare(PHP_VERSION, '7.4.0', '>=');
        while (($r = ($csv_ok ? fgetcsv($fh, 0, ',', '"', '') : fgetcsv($fh))) !== false) {
            if ($r === array(null) || $r === array()) continue;       // blank line
            if ($header === null) { $header = $r; continue; }
            $row = array();
            foreach ($header as $i => $col) { $row[$col] = array_key_exists($i, $r) ? $r[$i] : null; }
            $rows[] = $row;
        }
        fclose($fh);

        $students = load_data();
        $added_count = 0;
        $skipped_count = 0;

        foreach ($rows as $row) {
            if (!truthy(isset($row['id']) ? $row['id'] : null)) { $skipped_count++; continue; }
            $student_id = trim($row['id']);

            // Validate ID
            list($valid, $msg) = validate_student_id($student_id);
            if (!$valid) { $skipped_count++; continue; }
            if (find_student_index($students, $student_id) !== null) { $skipped_count++; continue; }

            $fname = trim(isset($row['fname']) ? $row['fname'] : '');
            $lname = trim(isset($row['lname']) ? $row['lname'] : '');
            if ($fname === '' || $lname === '') { $skipped_count++; continue; }

            $course = strtoupper(array_key_exists('course', $row) ? (string)$row['course'] : 'MBA');
            $curricula = ALL_CURRICULUMS;
            if (!isset($curricula[$course])) $course = 'MBA';   // Default to MBA if invalid

            $students[] = array(
                'id' => $student_id,
                'fname' => $fname,
                'lname' => $lname,
                'full_name' => truthy(isset($row['full_name']) ? $row['full_name'] : null) ? $row['full_name'] : "{$lname}, {$fname}",
                'course' => $course,
                'type' => array_key_exists('type', $row) ? (string)$row['type'] : 'New Enrollee',
                'grades' => array(),
                'req_checklist' => new stdClass(),
                'prereq_status' => false,
                'created_at' => iso_now(),
                'created_by' => isset($_SESSION['username']) ? $_SESSION['username'] : null,
            );
            $added_count++;
        }
        save_data($students);
        log_action('IMPORT_CSV', "Imported {$added_count} students, skipped {$skipped_count}");
        flash("Import complete. Added: {$added_count}, Skipped: {$skipped_count}", 'success');
    } catch (Exception $e) {
        flash('Error importing CSV: ' . $e->getMessage(), 'danger');
        log_action('IMPORT_CSV_FAILED', 'Error: ' . $e->getMessage());
    }
    redirect_to('/');
}

function route_reports() {
    login_required();
    $students = load_data();
    $fail_count = 0;
    $grad_candidates_count = 0;
    $course_stats = array();
    $type_stats = array('New Enrollee' => 0, 'Transferee' => 0, 'Continuing' => 0);
    $curricula = ALL_CURRICULUMS;

    foreach ($students as &$s) {
        $c = isset($s['course']) ? $s['course'] : 'Unknown';
        $course_stats[$c] = (isset($course_stats[$c]) ? $course_stats[$c] : 0) + 1;

        $t = isset($s['type']) ? $s['type'] : 'Unknown';
        if (is_string($t) && array_key_exists($t, $type_stats)) $type_stats[$t]++;

        $totals = calculate_assessment($s);
        $has_fail = isset($totals['HasFailures']) ? $totals['HasFailures'] : false;

        $curriculum = isset($curricula[isset($s['course']) ? $s['course'] : null]) ? $curricula[$s['course']] : array();
        $required_units = 0;
        foreach ($curriculum as $item) { $required_units += $item['units']; }

        $is_candidate = (!$has_fail
            && $totals['Total'] >= $required_units
            && $required_units > 0
            && (isset($totals['GWA']) ? $totals['GWA'] : 0) > 0);

        if ($has_fail) { $s['report_status'] = 'failure'; $fail_count++; }
        elseif ($is_candidate) { $s['report_status'] = 'candidate'; $grad_candidates_count++; }
        else { $s['report_status'] = 'pending'; }
    }
    unset($s);

    render('reports', array(
        'total' => count($students),
        'failures' => $fail_count,
        'candidates' => $grad_candidates_count,
        'course_stats' => $course_stats,
        'type_stats' => $type_stats,
        'students' => $students,
    ));
}

function route_export_csv() {
    login_required();
    $students = load_data();
    log_action('EXPORT_CSV', 'Exported ' . count($students) . ' student records');
    while (ob_get_level() > 0) { ob_end_clean(); }
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=university_student_report.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, array('Student ID', 'Full Name', 'Course', 'Type', 'Passed Units', 'GWA', 'Academic Status', 'Created At'));
    foreach ($students as $s) {
        $totals = calculate_assessment($s);
        fputcsv($out, array(
            isset($s['id']) ? $s['id'] : '',
            isset($s['full_name']) ? $s['full_name'] : '',
            isset($s['course']) ? $s['course'] : '',
            isset($s['type']) ? $s['type'] : '',
            $totals['Total'],
            pv($totals['GWA']),
            isset($totals['AcademicStatus']) ? $totals['AcademicStatus'] : 'Unknown',
            isset($s['created_at']) ? $s['created_at'] : 'N/A',
        ));
    }
    fclose($out);
    exit;
}

function route_settings() {
    login_required();
    render('settings');
}

function route_backup_db() {
    login_required();
    if (file_exists(DATA_FILE)) {
        log_action('BACKUP', 'Database backup downloaded');
        while (ob_get_level() > 0) { ob_end_clean(); }
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="backup_students_' . date('Y-m-d') . '.json"');
        header('Content-Length: ' . filesize(DATA_FILE));
        readfile(DATA_FILE);
        exit;
    }
    flash('No database found to backup.', 'danger');
    redirect_to('/settings');
}

function route_restore_db() {
    admin_required();
    $file = isset($_FILES['backup_file']) ? $_FILES['backup_file'] : null;
    if ($file && isset($file['name']) && $file['name'] !== '') {
        // Validate it's a JSON file
        if (substr($file['name'], -5) !== '.json') {
            flash('Only JSON backup files are allowed.', 'danger');
            redirect_to('/settings');
        }
        $content = ($file['error'] === UPLOAD_ERR_OK) ? file_get_contents($file['tmp_name']) : false;
        $data = ($content !== false) ? json_decode($content, true) : null;
        if ($content === false || json_last_error() !== JSON_ERROR_NONE) {
            flash('Invalid JSON file.', 'danger');
            redirect_to('/settings');
        }
        if (!is_list_array($data)) {
            flash('Invalid backup file format.', 'danger');
            redirect_to('/settings');
        }
        file_put_contents(DATA_FILE, $content, LOCK_EX);
        log_action('RESTORE', "Database restored from {$file['name']}");
        flash('Database restored successfully.', 'success');
        redirect_to('/');
    }
    flash('No file selected.', 'danger');
    redirect_to('/settings');
}

function route_reset_db() {
    admin_required();
    if (file_exists(DATA_FILE)) {
        // Create final backup before reset
        $ts = date('Ymd_His');
        @copy(DATA_FILE, BACKUP_DIR . "/pre_reset_backup_{$ts}.json");
        unlink(DATA_FILE);
        log_action('RESET', 'Database reset - all data wiped');
        flash('System has been reset. All data wiped.', 'warning');
    }
    redirect_to('/');
}

function route_add_manual_subject($student_id) {
    login_required();
    $students = load_data();
    $idx = find_student_index($students, $student_id);
    if ($idx === null) {
        flash('Student not found.', 'danger');
        redirect_to('/');
    }
    $self = '/student/' . rawurlencode($student_id);

    // Get form data
    $subject_code = strtoupper(trim(post('subject_code', '')));
    $subject_title = trim(post('subject_title', ''));
    $units = post('units', '0');
    $grade = post('grade', '0');
    $subj_type = post('type', 'Pre-requisite');

    // Validation
    if ($subject_code === '' || $subject_title === '') {
        flash('Subject code and title are required.', 'danger');
        redirect_to($self);
    }
    if (!is_numeric(trim($grade)) || !is_numeric(trim($units))) {
        flash('Invalid grade or units format.', 'danger');
        redirect_to($self);
    }
    $g_val = (float)$grade;
    $u_val = (float)$units;

    // If grade is 1.0-3.0 (GPA scale) OR 75-100 (percentage scale), it's passing
    $is_passing = ($g_val >= 1.0 && $g_val <= 3.0) || ($g_val >= 75);
    $status = $is_passing ? 'Passed' : 'Failed';

    // Ensure grades is a list
    if (!isset($students[$idx]['grades']) || !is_array($students[$idx]['grades'])) {
        $students[$idx]['grades'] = array();
    }

    // Check if subject already exists
    $existing_idx = null;
    foreach ($students[$idx]['grades'] as $i => $g) {
        if ($g['course_code'] === $subject_code) { $existing_idx = $i; break; }
    }

    if ($existing_idx !== null) {
        // Update existing - preserve created info
        $old_grade = isset($students[$idx]['grades'][$existing_idx]['grade']) ? $students[$idx]['grades'][$existing_idx]['grade'] : null;
        $students[$idx]['grades'][$existing_idx] = array_merge($students[$idx]['grades'][$existing_idx], array(
            'course_title' => $subject_title,
            'units' => $u_val,
            'category' => $subj_type,
            'term' => 'Pre-requisite / Bridging',
            'grade' => $g_val,
            'status' => $status,
            'updated_at' => iso_now(),
            'updated_by' => isset($_SESSION['username']) ? $_SESSION['username'] : null,
        ));
        log_action('UPDATE_MANUAL_SUBJECT', "Updated {$subject_code}: " . pv($old_grade) . ' -> ' . pv($g_val) . " ({$status})", $student_id);
        flash("Subject {$subject_code} updated to {$status}.", 'success');
    } else {
        // Add new
        $students[$idx]['grades'][] = array(
            'course_code' => $subject_code,
            'course_title' => $subject_title,
            'units' => $u_val,
            'category' => $subj_type,
            'term' => 'Pre-requisite / Bridging',
            'grade' => $g_val,
            'status' => $status,
            'created_at' => iso_now(),
            'created_by' => isset($_SESSION['username']) ? $_SESSION['username'] : null,
        );
        log_action('ADD_MANUAL_SUBJECT', "Added {$subject_code} ({$subj_type}) with grade " . pv($g_val) . " ({$status})", $student_id);
        flash("Subject {$subject_code} added with status: {$status}.", 'success');
    }

    // Set prereq_status if not already set
    if (!truthy(isset($students[$idx]['prereq_status']) ? $students[$idx]['prereq_status'] : null)) {
        $students[$idx]['prereq_status'] = true;
    }

    save_data($students);
    redirect_to($self);
}

function route_delete_manual_subject($student_id, $course_code) {
    login_required();
    $students = load_data();
    $idx = find_student_index($students, $student_id);
    if ($idx === null) {
        flash('Student not found.', 'danger');
        redirect_to('/');
    }
    $existing = isset($students[$idx]['grades']) && is_array($students[$idx]['grades']) ? $students[$idx]['grades'] : array();
    $original_count = count($existing);
    $kept = array();
    foreach ($existing as $g) { if ($g['course_code'] !== $course_code) $kept[] = $g; }
    $students[$idx]['grades'] = $kept;

    if (count($kept) < $original_count) {
        save_data($students);
        log_action('DELETE_MANUAL_SUBJECT', "Deleted {$course_code}", $student_id);
        flash("Subject {$course_code} deleted successfully.", 'success');
    } else {
        flash('Subject not found.', 'warning');
    }
    redirect_to('/student/' . rawurlencode($student_id));
}

function route_audit_log() {
    admin_required();
    $logs = array();
    if (file_exists(AUDIT_LOG)) {
        $d = json_decode((string)@file_get_contents(AUDIT_LOG), true);
        if (is_array($d)) $logs = array_values($d);
    }
    $logs = array_reverse($logs);   // Newest first
    render('audit_log', array('logs' => array_slice($logs, 0, 100)));   // Last 100 entries
}

function route_manage_users() {
    admin_required();
    render('users', array('users' => load_users()));
}

function route_add_user() {
    admin_required();
    $username = trim(post('username', ''));
    $password = post('password', '');
    $name = trim(post('name', ''));
    $role = post('role', 'user');

    if ($username === '' || $password === '') {
        flash('Username and password are required.', 'danger');
        redirect_to('/users');
    }
    $users = load_users();
    if (find_user_by('username', $username, $users)) {
        flash('Username already exists.', 'danger');
        redirect_to('/users');
    }
    $users[] = array(
        'id' => uuid4(),
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'name' => $name,
        'role' => $role,
        'created_at' => iso_now(),
    );
    save_users($users);
    log_action('ADD_USER', "Created user {$username} with role {$role}");
    flash("User {$username} created successfully.", 'success');
    redirect_to('/users');
}

function route_delete_user($user_id) {
    admin_required();
    $users = load_users();
    $user = find_user_by('id', $user_id, $users);
    if (!$user) {
        flash('User not found.', 'danger');
        redirect_to('/users');
    }
    if ($user['username'] === (isset($_SESSION['username']) ? $_SESSION['username'] : null)) {
        flash('Cannot delete your own account.', 'danger');
        redirect_to('/users');
    }
    $kept = array();
    foreach ($users as $u) { if ($u['id'] !== $user_id) $kept[] = $u; }
    save_users($kept);
    log_action('DELETE_USER', 'Deleted user ' . $user['username']);
    flash('User ' . $user['username'] . ' deleted.', 'success');
    redirect_to('/users');
}

// --- DISPATCH ---------------------------------------------------------------
$path = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/', PHP_URL_PATH);
if ($path === null || $path === false) $path = '/';
if (BASE_URL !== '' && strpos($path, BASE_URL) === 0) $path = substr($path, strlen(BASE_URL));
if (preg_match('#^/index\.php(/.*)?$#', $path, $__m)) $path = (isset($__m[1]) && $__m[1] !== '') ? $__m[1] : '/';
if ($path === '') $path = '/';
if ($path !== '/') $path = rtrim($path, '/');

$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
if ($method === 'HEAD') { $method = 'GET'; }

$G = array('GET'); $P = array('POST'); $GP = array('GET', 'POST');
$routes = array(
    array('#^/login$#',                              $GP, 'route_login'),
    array('#^/logout$#',                             $G,  'route_logout'),
    array('#^/$#',                                   $G,  'route_index'),
    array('#^/student/([^/]+)$#',                    $GP, 'route_profile'),
    array('#^/student/([^/]+)/requirements$#',       $P,  'route_update_requirements'),
    array('#^/student/([^/]+)/credit$#',             $P,  'route_credit_subject'),
    array('#^/add_student$#',                        $P,  'route_add_student'),
    array('#^/import_csv$#',                         $P,  'route_import_students'),
    array('#^/reports$#',                            $G,  'route_reports'),
    array('#^/export_csv$#',                         $G,  'route_export_csv'),
    array('#^/settings$#',                           $G,  'route_settings'),
    array('#^/settings/backup$#',                    $G,  'route_backup_db'),
    array('#^/settings/restore$#',                   $P,  'route_restore_db'),
    array('#^/settings/reset$#',                     $P,  'route_reset_db'),
    array('#^/add_manual_subject/([^/]+)$#',         $P,  'route_add_manual_subject'),
    array('#^/delete_manual_subject/([^/]+)/([^/]+)$#', $P, 'route_delete_manual_subject'),
    array('#^/audit-log$#',                          $G,  'route_audit_log'),
    array('#^/users$#',                              $G,  'route_manage_users'),
    array('#^/users/add$#',                          $P,  'route_add_user'),
    array('#^/users/delete/([^/]+)$#',               $P,  'route_delete_user'),
);

try {
    foreach ($routes as $r) {
        if (preg_match($r[0], $path, $m)) {
            if (!in_array($method, $r[1], true)) {
                error_page(405, 'Method not allowed');
            }
            array_shift($m);
            $args = array();
            foreach ($m as $part) { $args[] = rawurldecode($part); }
            call_user_func_array($r[2], $args);
            exit;
        }
    }
    error_page(404, 'Page not found');
} catch (Throwable $e) {
    error_log('Unhandled error: ' . $e->getMessage());
    while (ob_get_level() > 0) { ob_end_clean(); }
    http_response_code(500);
    render('error', array('error_code' => 500, 'error_message' => DEBUG ? $e->getMessage() : 'Internal server error'));
}
