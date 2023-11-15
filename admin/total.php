<!DOCTYPE html>
<?php $menu4="active"; ?>

<html>

<head>
    <title>Frequent Item Sets Total</title>
    <?php include "head.php"; ?>
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body>
    <?php include "eclat-table.php"; ?>
    <?php include "sidebar.php"; ?>

    <div class="content pb-3">
        <div class="container pt-3">
            <h3>Frequent Item Sets Total</h3>
            <hr>
            <div class="mt-5">
                <div class="table-responsive">
                    <table id="frequentSetsDataTable" class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Item</th>
                                <th>Item Set</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($frequentSetsData as $set): ?>
                            <tr>
                                <td><?php echo $set['item']; ?></td>
                                <td><?php echo $set['supportCount']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php include "scripts.php"; ?>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <script>
            $(document).ready(function () {
                $('#frequentSetsDataTable').DataTable();
            });
        </script>
</body>

</html>