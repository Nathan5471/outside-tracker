<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

echo "Terra is awesome!";
echo "<br>";
echo "PHP is easy so far!";
?>