<?php
/**
 * include library classes
 */
include '../src/dduers/pdoxpress/PDOXpressConnection.php';
include '../src/dduers/pdoxpress/PDOXpressDataModel.php';
include '../src/dduers/pdoxpress/PDOXpressException.php';
/**
 * sample configuration
 */
$database_driver = 'mysql';
$database_server = 'localhost';
$database_name = 'pdo_playground';
$database_charset = 'utf8mb4';
$database_dsn = "$database_driver:host=$database_server;dbname=$database_name;charset=$database_charset";
$database_user = 'root';
$database_password = '';
/**
 * create PDOXpressConnection instance
 */
$PDOXpressConnection = new \Dduers\PDOXpress\PDOXpressConnection($database_dsn, $database_user, $database_password);
/**
 * create PDOXpressDataModel with mapped table 'pdo_test'
 */
$PDOXpressDataModel = new \Dduers\PDOXpress\PDOXpressDataModel($PDOXpressConnection, 'pdo_test');
/**
 * create a record
 */
if (isset($_POST['Create'])) {
    // remove unused post values, that represent no table column name
    unset($_POST['Create']);
    // insert to table `pdo_test`
    $PDOXpressDataModel->insert($_POST, true);
    // get the id of the inserted record
    $recordId = $PDOXpressConnection->lastInsertId();
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
        $PDOXpressDataModel->update($_POST, $_GET['id'], 'id', false);
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
        $PDOXpressDataModel->delete($_GET['id']);
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
    $PDOXpressConnection->execQuery("DELETE FROM `pdo_test`");
    // redirect, avoid resending another post on page refresh
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
/**
 * select record for edit / update
 */
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // select the record from table `pdo_test` 
    $PDOXpressDataModel->select(['id' => $_GET['id']]);
    // set to post var
    $_POST = $PDOXpressConnection->fetch(true);
}
/** 
 * render your page template ...
 **/
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
                    use $PDOXpressDataModel->selectFetchAllObject 
                    to fetch all records at once as an array with objects with UPPERCASE property names
                    also, encode html special chars for display
                -->
                <?php 
                    foreach (
                        ($result = $PDOXpressDataModel->selectFetchAllObject([], [], PDO::CASE_UPPER, true)) 
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
                    use $PDOXpressDataModel->select / $PDOXpressConnection->fetch
                    to prepare the statement and fetch throu every record in a while loop
                    also, encode html special chars for display
                -->
                <!--
                <?php 
                    $PDOXpressDataModel->select(); 
                    while ($row = $PDOXpressConnection->fetch(true)): 
                ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><a href="?id=<?= $row['id'] ?>"><?= $row['title'] ?></a></td>
                        <td><?= $row['text'] ?></td>
                        <td><?= $row['number'] ?></td>
                    </tr>
                <?php endwhile; ?>
                -->
                <?php
                    /**
                     * Transactions
                     */
                    $PDOXpressConnection->beginTransaction();
                    $error = false;
                    try {
                        $PDOXpressDataModel->insert([
                            'title' => 'auto_insert1',
                            'text' => 'auto_insert1',
                            'number' => 111,
                        ]);
                        $PDOXpressDataModel->insert([
                            'title' => 'auto_insert2',
                            'text' => 'auto_insert2',
                            'number' => 222,
                        ]);
                        $PDOXpressDataModel->insert([
                            'title' => 'auto_insert3',
                            'text' => 'auto_insert3',
                            'number' => 333,
                        ]);
                        // error within transaction happens here
                        $PDOXpressDataModel->insert([
                            'non_existing_column' => 'auto_insert3',
                            'text' => 'auto_insert3',
                            'number' => 333,
                        ]);
                    } catch (Exception $e) {
                        $PDOXpressConnection->rollBack();
                        $error = true;
                        //echo $e->getMessage();
                    }
                    // commit transaction when no error
                    if (false === $error)
                        $PDOXpressConnection->commit();
                ?>
            </table>
        </div>
    </body>
</html>
