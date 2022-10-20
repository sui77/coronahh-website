<?php

$col = $_GET['col'] ?? 1;
$fp = fopen('data.csv', 'r');
$titles = fgetcsv($fp, 10000, ';');
$rows = [];
$dates = [];
$data = [];
while (!feof($fp)) {
    $row = fgetcsv($fp, 10000, ';');
    $dates[] = $row[0];
    $rows[] = $row;
    $data[] = $row[$col];
}

?>
<!doctype html>
<html>

<head>
    <title>Line Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <style>
        canvas{
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
</head>

<body>

<div style="border: solid 1px;background-color:#ccc;padding:20px;">
<form>
    <strong>Auswählen:</strong> <select name="col" onChange="this.form.submit()">

    <?php foreach ($titles as $n => $col) { ?>
    <option <?=$_GET['col']==$n?'selected':''?> value="<?=$n?>"><?=$col?></option>
    <?php } ?>
</select>
</form>
</div>
<hr>

<div style="width:75%;">
    <canvas id="canvas"></canvas>
</div>

<script>
    var config = {
        type: 'line',
        data: {
            labels: <?=json_encode($dates)?>,
            datasets: [{
                label: 'My First dataset',
                backgroundColor: 'rgb(255, 99, 132)',
                borderColor: 'rgb(255, 99, 132)',
                data: <?=json_encode($data)?>,
                fill: false,
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Chart.js Line Chart'
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Month'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Value'
                    }
                }]
            }
        }
    };

    window.onload = function() {
        var ctx = document.getElementById('canvas').getContext('2d');
        window.myLine = new Chart(ctx, config);
    };


</script>
</body>

</html>
