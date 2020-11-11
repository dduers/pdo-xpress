<?php
include '../src/dduers/pdoxpress/PDOXpress.php';
include '../config/config.php';

$pdoMySql = new \Dduers\PDOXpress\PDOXpress(DB_CONN, DB_USER, DB_PASS);

if (isset($_POST['Create'])) {
    unset($_POST['Create']);
    $pdoMySql->insert('pdo_test', $_POST);
    //exit($pdoMySql->lastInsertId());
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
