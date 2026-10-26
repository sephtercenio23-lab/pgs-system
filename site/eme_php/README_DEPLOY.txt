DEPLOY TO INFINITYFREE
======================

1. In the InfinityFree control panel open File Manager (or use FTP) and go to /htdocs.
2. Upload everything inside this folder into /htdocs (index.php, curriculum.php, .htaccess,
   static/, templates/, data/). Make sure the hidden .htaccess files are uploaded too.
   - Tip: upload the eme_php.zip and use the built-in "Extract" option - this is the most
     reliable way to keep the hidden .htaccess files and the data/ folder.
3. In the InfinityFree control panel open "PHP Config" and select PHP 7.4 or newer (8.x).
4. Open your site URL. Log in with:  admin / admin123   (or)   iPraise / admin123
5. Go to Users, create new accounts with real passwords, and delete the old ones.

HOW THE DATABASE WORKS (important)
----------------------------------
This system does NOT use MySQL / phpMyAdmin. That is intentional - nothing needs to
be created in the control panel. All records are stored in plain JSON files:

    data/students_db.json   -> student records ("the database")
    data/users_db.json      -> user accounts (passwords are hashed)
    data/audit_log.json     -> audit trail
    data/.backups/          -> automatic backups made on every save

If data/ is missing or empty, the system automatically creates a fresh empty database
and the default admin account on the first page load. Use Settings > Restore Data to
load a JSON backup (or upload your own data/students_db.json via File Manager).

TROUBLESHOOTING
---------------
* "500 Internal Server Error" right after uploading:
  Almost always caused by a bad .htaccess rule. The most common one on InfinityFree is
  the "Options" directive (e.g. Options -Indexes) which InfinityFree does not allow.
  This package's .htaccess is already fixed - make sure the uploaded .htaccess is the
  one from this package and that you did not add "Options ..." lines to it.
  Other directives NOT allowed on InfinityFree: php_value, php_flag.

* Blank page:
  Set DEBUG to true near the top of index.php, reload to see the error, then set it
  back to false when finished.

* Styles / logos missing:
  The static/ folder is missing - re-upload it.

* "No students" or empty dashboard even though you have data:
  The data/ folder (or data/students_db.json) was not uploaded. Upload it via File
  Manager, or use Settings > Restore Data with a JSON backup.

* Login loops or session problems:
  Make sure you are using PHP 7.4 or newer (PHP Config in the control panel).
