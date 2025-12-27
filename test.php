<?php
include 'db.php';
if ($conn) {
    echo "<h1>PHP and Database Connected Successfully!</h1>";
} else {
    echo "<h1>Database Connection Failed.</h1>";
}
phpinfo();
?>
