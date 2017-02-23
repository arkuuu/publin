<?php

namespace publin\install;

use PDO;
use PDOException;
use publin\config\Config;
use publin\src\Auth;
use publin\src\MailHandler;

require_once '../vendor/autoload.php';

Config::setup();

$required_php_version = '5.3.14';
$required_extensions = array('mbstring', 'fileinfo');
$upload_file_path = Config::FILE_PATH;
$required_mysql_version = '5.5.38';
$sql_host = Config::SQL_HOST;
$sql_db = Config::SQL_DATABASE;
$sql_user = Config::SQL_USER;
$admin_mail = Config::ADMIN_MAIL;
$sql_dump_file = __DIR__.'/database.sql';

function db_connect()
{
    $dsn = 'mysql:host='.Config::SQL_HOST.';dbname='.Config::SQL_DATABASE.';charset=UTF8';
    $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

    try {
        $pdo = new PDO($dsn, Config::SQL_USER, Config::SQL_PASSWORD, $options);
        if (version_compare(phpversion(), '5.3.6', '<')) {
            $pdo->exec('SET NAMES UTF8');
        }

        return $pdo;
    } catch (PDOException $e) {
        return false;
    }
}

function db_import($sql_dump_file)
{

    $pdo = db_connect();

    if (!$pdo) {
        return false;
    }

    $sql = file_get_contents($sql_dump_file);
    try {
        $pdo->beginTransaction();
        $pdo->exec($sql);
        // ISI study fields
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Not Categorized');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Agricultural Sciences');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Biology & Biochemistry');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Chemistry');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Clinical Medicine');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Computer Science');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Economics & Business');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Engineering');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Environment/Ecology');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Geosciences');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Immunology');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Materials Science');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Mathematics');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Microbiology');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Molecular Biology & Genetics');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Neuroscience & Behavior');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Pharmacology & Toxicology');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Physics');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Plant & Animal Science');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Psychiatry/Psychology');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Social Sciences, general');");
        $pdo->exec("INSERT INTO study_fields (name) VALUES ('Space Science');");
        // Other
        $pdo->exec("INSERT INTO types VALUES (1,'article',NULL),(2,'book',NULL),(3,'inproceedings',NULL),(4,'incollection',NULL),(5,'inbook',NULL),(6,'techreport',NULL),(7,'mastersthesis',NULL),(8,'phdthesis',NULL),(9,'unpublished',NULL),(10,'misc',NULL);");
        $pdo->exec("INSERT INTO permissions VALUES (1,'access_hidden_files'),(2,'access_restricted_files'),(3,'author_delete'),(4,'author_edit'),(5,'keyword_delete'),(6,'keyword_edit'),(7,'manage'),(8,'publication_delete'),(9,'publication_edit'),(10,'publication_submit');");
        $pdo->exec("INSERT INTO roles VALUES (1,'Admin'),(2,'Editor'),(3,'Member'),(4,'Guest');");

        $pdo->exec("INSERT INTO roles_permissions VALUES (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,1,8),(9,1,9),(10,1,10);");
        $pdo->commit();

        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo '<p class="error">'.$e->getMessage().'</p>';

        return false;
    }
}

if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

?>
<html>
<head>
    <title>Installation</title>
    <style type="text/css">
        .ok {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>


<?php

if ($page == 1) {
    $error = false;
    ?>
    <h1>Publin installation - Step 1</h1>
    <p>This script will do a fresh install of Publin on your server.<br/><b>Do not use this to update an existing
            installation, all data will be lost!</b></p>
    <p>First, let's see if your server matches the requirements:</p>
    <ul>
        <li>PHP Version:
            <?php if (version_compare(phpversion(), $required_php_version, '>=')) {
                echo '<span class="ok">'.phpversion().'</span>';
            } else {
                $error = true;
                echo '<span class="error">'.phpversion().'</span>';
            } ?>
        </li>
        <li>PHP Extensions:
            <ul>
                <?php foreach ($required_extensions as $extension) {
                    echo '<li><i>'.$extension.'</i>: ';
                    if (extension_loaded($extension)) {
                        echo '<span class="ok">ok</span></li>';
                    } else {
                        $error = true;
                        echo '<span class="error">not found</span></li>';
                    }
                } ?>
            </ul>
        </li>
        <li>File upload path:
            <?php if (file_exists($upload_file_path)) {
                if (is_writable($upload_file_path)) {
                    echo '<span class="ok">ok</span>';
                } else {
                    $error = true;
                    echo '<span class="error">not writable</span>';
                }
            } else {
                $error = true;
                echo '<span class="error">path not found</span>';
            } ?>
        </li>
        <li>Database connection:
            <?php if (db_connect()) {
                echo '<span class="ok">'.$sql_user.'@'.$sql_host.' : '.$sql_db.'</span>';
            } else {
                $error = true;
                echo '<span class="error">could not connect, check config</span>';
            }
            ?>
        </li>
        <li>MySQL Version:
            <?php
            if (!$pdo = db_connect()) {
                $error = true;
                echo '<span class="error">could not connect, check config</span>';
            } else {
                $version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
                if (version_compare($version, $required_mysql_version, '>=')) {
                    echo '<span class="ok">'.$version.'</span>';
                } else {
                    $error = true;
                    echo '<span class="error">'.$version.'</span>';
                }
            }
            ?>
        </li>
    </ul>
    <?php if ($error) {
        echo '<p>Oops, there are some errors! <a href="">Retry</a></p>';
    } else {
        echo '<p>Everything ok! If you continue, tables will be created in the database <b>'.$sql_db.'</b>.<br/>
         <b>Existing tables in this database will be dropped!</b> <a href="?page=2">Continue</a></p>';
    }
} else if ($page == 2) {
    $error = false;
    ?>
    <h1>Publin installation - Step 2</h1>
    <p>The installer now tries to create the database...</p>

    <?php if (!file_exists($sql_dump_file)) {
        $error = true;
        echo '<p class="error">Database dump file not found in '.$sql_dump_file.'</p>';
    } else if (db_import($sql_dump_file)) {
        echo '<p class="ok">Database successfully created</p>';
    } else {
        $error = true;
        echo '<p class="error">Database could not be created</p>';
    }
    if ($error) {
        echo '<p>Oops, this should not happen! <a href="">Retry</a></p>';
    } else {
        echo '<p>Everything ok! <a href="?page=3">Continue</a></p>';
    }
} else if ($page == 3) {
    $error = false;
    ?>
    <h1>Publin installation - Step 3</h1>
    <p>Now please enter your first user. This user will initially be an administrator.<br/>
        A temporary password will be sent to the email address.<br/>
        <b>If you do not get this mail, check your server's mail configuration.</b></p>
    <form action="./?page=3" method="post">
        <label for="username">Name:</label>
        <input type="text" name="username" id="username"><br/>
        <label for="email">Email:</label>
        <input type="text" name="email" id="email" value="<?php echo $admin_mail ?>"><br/><br/>
        <input type="submit" value="Continue">
    </form>
    <?php
    if (isset($_POST['username']) && isset($_POST['email'])) {
        if (empty($_POST['username']) || empty($_POST['email'])) {
            echo '<p class="error">Invalid input!</p>';
        } else {
            $name = $_POST['username'];
            $mail = $_POST['email'];
            $password = Auth::generatePassword();
            $hash = Auth::hashPassword($password);

            try {
                $pdo = db_connect();
                $stmt = $pdo->prepare('INSERT INTO users (name, mail, password) VALUES (?, ?, ?);');
                $stmt->execute(array($name, $mail, $hash));
                $user_id = $pdo->lastInsertId();
                $pdo->exec('INSERT INTO users_roles (user_id, role_id) VALUES ('.$user_id.', 1);');
            } catch (PDOException $e) {
                $error = true;
                echo '<p class="error">'.$e->getMessage().'</p>';
            }

            if ($error) {
                echo '<p>Oops, this should not happen!</p>';
            } else {

                $subject = 'An account at '.Config::ROOT_URL.' was created for you!';
                $message = 'Greetings, the installation script created an account for you at '.Config::ROOT_URL.'.'."\n\n";
                $message .= 'Username: '.$name."\n";
                $message .= 'Mail: '.$mail."\n";
                $message .= 'Temporary password: '.$password."\n";

                if (MailHandler::sendMail($mail, $subject, $message)) {
                    echo '<p class="ok">Email was send! The installation is now complete.</p>';
                    echo '<h2>Delete this install directory!</h2><p>If you do not do this, everybody can screw up your installation.</p>';
                } else {
                    echo '<p class="error">The email could not be sent!</p>';
                }
            }
        }
    }
}
?>
</body>
</html>
