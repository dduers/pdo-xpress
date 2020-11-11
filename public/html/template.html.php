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
                $PDOx->select('pdo_test');
                foreach ($PDOx->fetchAllObject(true) as $row) {
                    /*echo 
                    '<tr>'.
                        '<td>'.$row['id'].'</td>'.
                        '<td><a href="?id='.$row['id'].'">'.$row['title'].'</a></td>'.
                        '<td>'.$row['text'].'</td>'.
                        '<td>'.$row['number'].'</td>'.
                    '</tr>';*/
                    echo 
                    '<tr>'.
                        '<td>'.$row->id.'</td>'.
                        '<td><a href="?id='.$row->id.'">'.$row->title.'</a></td>'.
                        '<td>'.$row->text.'</td>'.
                        '<td>'.$row->number.'</td>'.
                    '</tr>';
                }
                echo '</table>';
            ?>
        </div>
    </body>
</html>
