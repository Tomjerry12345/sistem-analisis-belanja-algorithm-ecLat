<!DOCTYPE html>
<?php $menu3="active"; ?>

<html>

<head>
    <title>Frequent Item Sets Group</title>
    <?php include "head.php"; ?>
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body>
    <?php include "eclat-table.php"; ?>
    <?php include "sidebar.php"; ?>

    <div class="content pb-3">
        <div class="container pt-3">
            <h3>Frequent Item Sets Group</h3>
            Item & TD List
            <hr>

            <div class="mt-5">
                <div class="table-responsive">
                    <table id="frequentSetsTable" class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Item</th>
                                <th>TID List</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($frequentSets as $frequentSet): ?>
                            <?php if (!empty($frequentSet['item'])): ?>
                            <tr>
                                <td><?php echo $frequentSet['item']; ?></td>
                                <td><?php echo "[" . implode(", ", $frequentSet['tidList']) . "]"; ?></td>
                            </tr>
                            <?php endif; ?>
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
                $('#frequentSetsTable, #frequentSetsDataTable').DataTable();
            });
            $(document).ready(function () {
                $('#example').DataTable();
            });
        </script>
</body>

</html>