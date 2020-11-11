<?php
include '../src/dduers/pdomysql/PDOMySql.php';
include '../config/config.php';

$pdoMySql = new \Dduers\PDOMySql\PDOMySql(DB_CONN, DB_USER, DB_PASS/*, [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_EMULATE_PREPARES => false, 
]*/);

if (isset($_POST['Create'])) {
    unset($_POST['Create']);
    $pdoMySql->insert('pdo_test', $_POST);
    header('Location: ./');
    exit();
}

if (isset($_POST['Update'])) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        unset($_POST['Update']);
        $pdoMySql->update('pdo_test', $_POST, $_GET['id']);
    }
    header('Location: ./');
    exit();
}

if (isset($_POST['Delete'])) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        unset($_POST['Delete']);
        $pdoMySql->delete('pdo_test', $_GET['id']);
    }
    header('Location: ./');
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $pdoMySql->select('pdo_test', ['id' => $_GET['id']]);
    $_POST = $pdoMySql->fetch();
}

include 'html/template.html.php';
