<?php

try{
    $conn = 'sqlite:jeanmi-construction.db';
    $pdo = new PDO($conn);
}
catch(PDOException $e) {
    $msg = 'Error while accessing database. Please warn challenge organisation.';
    die($e->getMessage());
    exit();
}