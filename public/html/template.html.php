<!DOCTYPE html>
<html>
    <head>
        <title>PDO Test Drive</title>
        <link rel="stylesheet" href="css/style.css">
        <base href="/">
    </head>
    <body>
        <div class="container">
            <h1>PDO Form</h1>
            <form method="POST" action="<?= '?'.$_SERVER['QUERY_STRING'] ?>">
                <label>Title</label>
                <input name="title" required="required" value="<?= $_POST['title'] ?? '' ?>"/>
                <label>Text</label>
                <textarea name="text" required="required"><?= $_POST['text'] ?? '' ?></textarea>
                <label>Number</label>
                <input name="number" type="number" required="required" value="<?= $_POST['number'] ?? '' ?>"/>
                <button name="<?= isset($_GET['id']) && $_GET['id'] ? 'Update' : 'Create' ?>" type="submit"><?= isset($_GET['id']) && $_GET['id'] ? 'Update' : 'Create' ?></button>
                <?php if (isset($_GET['id']) && is_numeric($_GET['id'])) { ?>
                    <button name="Delete" type="submit">Delete</button>
                <?php } ?>
            </form>
            <?php
                echo '<table>';
                
                /**
                 * EXAMPLE
                 * use $PDOx->selectFetchAllObject 
                 * to fetch all records at once as an array with objects with UPPERCASE property names
                 * also, encode html special chars for display
                 */
                foreach ($PDOx->selectFetchAllObject('pdo_test', [], [], PDO::CASE_UPPER, true) as $row) {
                    echo 
                    '<tr>'.
                        '<td>'.$row->ID.'</td>'.
                        '<td><a href="?id='.$row->ID.'">'.$row->TITLE.'</a></td>'.
                        '<td>'.$row->TEXT.'</td>'.
                        '<td>'.$row->NUMBER.'</td>'.
                    '</tr>';
                }

                /**
                 * EXAMPLE
                 * use $PDOx->select / $PDOx->fetch
                 * to prepare the statement and fetch throu every record in a while loop
                 * also, encode html special chars for display
                 */
                $PDOx->select('pdo_test');
                while ($row = $PDOx->fetch(true)) {
                    echo 
                    '<tr>'.
                        '<td>'.$row['id'].'</td>'.
                        '<td><a href="?id='.$row['id'].'">'.$row['title'].'</a></td>'.
                        '<td>'.$row['text'].'</td>'.
                        '<td>'.$row['number'].'</td>'.
                    '</tr>';
                }

                echo '</table>';
            ?>
        </div>
    </body>
</html>
