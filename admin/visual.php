<!DOCTYPE html>
<?php $menu2="active"; ?>

<html>

<head>
    <title>Frequent Item Sets Visualization</title>
    <?php include "head.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
</head>

<body>

    <?php include "eclat-table.php"; ?>
    <?php include "sidebar.php"; ?>

    <div class="content">
        <div class="container pt-3">
            <h3>Frequent Item Sets Visualization</h3>
            View Grafik Batang
            <hr>

            <h1 style="padding:12px;" class="text-center label label-warning"></h1>
            <canvas id="myChart"></canvas>
    
        </div>
    </div>

    <?php include "scripts.php"; ?>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var labels = <?php echo json_encode(array_column($frequentSetsData, 'item')); ?>;
        var supportCounts = <?php echo json_encode(array_column($frequentSetsData, 'supportCount')); ?>;
        
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Support Count',
                    data: supportCounts,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>