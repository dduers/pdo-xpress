<?php
/**
 * library include
 */
include '../src/dduers/pdoxpress/PDOXpress.php';

/**
 * sample configuration
 */
define('DB_CONN', 'mysql:host=localhost;dbname=playground_pdo;charset=utf8mb4');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * create PDOXpress instance
 */
$PDOx = new \Dduers\PDOXpress\PDOXpress(DB_CONN, DB_USER, DB_PASS);

/**
 * create a record
 */
if (isset($_POST['Create'])) {

    // remove unused post values, that represent no table column name
    unset($_POST['Create']);

    // insert to table `pdo_test`
    $PDOx->insert('pdo_test', $_POST, true);

    // get the id of the inserted record
    $recordId = $PDOx->lastInsertId();

    // redirect, avoid resending another post on page refresh
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

/**
 * update a record
 */
if (isset($_POST['Update'])) {

    // do id parameter validation
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {

        // remove unused post values, that represent no table column name
        unset($_POST['Update']);

        // update record with id in table `pdo_test` 
        $PDOx->update('pdo_test', $_POST, $_GET['id'], 'id', false);
    }

    // redirect, avoid resending another post on page refresh
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

/**
 * delete a record
 */
if (isset($_POST['Delete'])) {

    // do id parameter validation
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {

        // remove unused post values, that represent no table column name
        unset($_POST['Delete']);

        // delete record with id in table `pdo_test` 
        $PDOx->delete('pdo_test', $_GET['id']);
    }

    // redirect, avoid resending another post on page refresh
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

/**
 * delete all record
 */
if (isset($_POST['DeleteAll'])) {

    // remove unused post values, that represent no table column name
    unset($_POST['Delete']);

    // delete record with id in table `pdo_test` 
    $PDOx->query("DELETE FROM `pdo_test`");

    // redirect, avoid resending another post on page refresh
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

/**
 * select record for edit / update
 */
if (isset($_GET['id']) && is_numeric($_GET['id'])) {

    // select the record from table `pdo_test` 
    $PDOx->select('pdo_test', ['id' => $_GET['id']]);

    // set to post var
    $_POST = $PDOx->fetch(true);
}

// render your page template ...
?>

<!DOCTYPE html>
<html>
    <head>
        <title>PDO Test Drive</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="container">
            <h1>PDO Form</h1>
            <form method="POST" action="<?= $_SERVER['PHP_SELF'].($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '') ?>">
                <label>Title</label>
                <input name="title" required="required" value="<?= $_POST['title'] ?? '' ?>"/>
                <label>Text</label>
                <textarea name="text" required="required"><?= $_POST['text'] ?? '' ?></textarea>
                <label>Number</label>
                <input name="number" type="number" required="required" value="<?= $_POST['number'] ?? '' ?>"/>
                <button name="<?= isset($_GET['id']) && $_GET['id'] ? 'Update' : 'Create' ?>" type="submit"><?= isset($_GET['id']) && $_GET['id'] ? 'Update' : 'Create' ?></button>
                <?php if (isset($_GET['id']) && is_numeric($_GET['id'])): ?>
                    <button name="Delete" type="submit">Delete</button>
                <?php endif; ?>
            </form>
            <br/>
            <form method="POST" action="<?= $_SERVER['PHP_SELF'].($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '') ?>">
                <button name="DeleteAll" type="submit">Delete All</button>
            </form>
            <table>
                <!--
                    EXAMPLE
                    use $PDOx->selectFetchAllObject 
                    to fetch all records at once as an array with objects with UPPERCASE property names
                    also, encode html special chars for display
                -->
                <?php 
                    foreach (
                        ($result = $PDOx->selectFetchAllObject('pdo_test', [], [], PDO::CASE_UPPER, true)) 
                        ? $result 
                        : [] 
                    as $row): 
                ?>
                    <tr>
                        <td><?= $row->ID ?></td>
                        <td><a href="?id=<?= $row->ID ?>"><?= $row->TITLE ?></a></td>
                        <td><?= $row->TEXT ?></td>
                        <td><?= $row->NUMBER ?></td>
                    </tr>
                <?php endforeach; ?>
                <!--
                    EXAMPLE
                    use $PDOx->select / $PDOx->fetch
                    to prepare the statement and fetch throu every record in a while loop
                    also, encode html special chars for display
                -->
                <?php 
                    $PDOx->select('pdo_test'); 
                    while ($row = $PDOx->fetch(true)): 
                ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><a href="?id=<?= $row['id'] ?>"><?= $row['title'] ?></a></td>
                        <td><?= $row['text'] ?></td>
                        <td><?= $row['number'] ?></td>
                    </tr>
                <?php endwhile; ?>

                <?php
                    /**
                     * Transactions
                     */
                    $PDOx->beginTransaction();

                    if (false === $PDOx->insert('pdo_test', [
                        'title' => 'auto_insert1',
                        'text' => 'auto_insert1',
                        'number' => 111,
                    ])) $PDOx->rollBack();

                    if (false === $PDOx->insert('pdo_test', [
                        'title' => 'auto_insert2',
                        'text' => 'auto_insert2',
                        'number' => 222,
                    ])) $PDOx->rollBack();

                    if (false === $PDOx->insert('pdo_test1', [
                        'title' => 'auto_insert3',
                        'text' => 'auto_insert3',
                        'number' => 333,
                    ])) $PDOx->rollBack();
                    
                    $PDOx->commit();
                ?>
            </table>
        </div>
    </body>
</html>
