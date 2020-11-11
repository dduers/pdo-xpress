<?php
include '../src/dduers/pdoxpress/PDOXpress.php';
include '../config/config.php';

$PDOx = new \Dduers\PDOXpress\PDOXpress(DB_CONN, DB_USER, DB_PASS);

if (isset($_POST['Create'])) {
    unset($_POST['Create']);
    $PDOx->insert('pdo_test', $_POST, true);
    //exit($PDOx->lastInsertId());
    header('Location: ./');
    exit();
}

if (isset($_POST['Update'])) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        unset($_POST['Update']);
        $PDOx->update('pdo_test', $_POST, $_GET['id'], 'id', false);
    }
    header('Location: ./');
    exit();
}

if (isset($_POST['Delete'])) {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        unset($_POST['Delete']);
        $PDOx->delete('pdo_test', $_GET['id']);
    }
    header('Location: ./');
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $PDOx->select('pdo_test', ['id' => $_GET['id']]);
    $_POST = $PDOx->fetch(true);
}

include 'html/template.html.php';
