<?php
require_once "Template/database_conn.php";
$pagePath = __FILE__;
$userInfo = User::getUserInfo();
$page = "dashboard";
if(!User::isUserLoggedIn()){
    header("Location: index.php");
}

include_once "Template/header.php";

$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : 1;
$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

$checkStmt = Connection::get()->prepare("SELECT SUM(amount) as trans, transType FROM `transaction` WHERE MONTH(date) = :month AND userId = :userId GROUP BY transType");
$checkStmt->bindParam(':month', $selectedMonth);
$checkStmt->bindParam(':userId', $userInfo['userId'] );
$checkStmt->execute();

$savings = 0;
$chartData = [['Transaction Type', 'Amount']];
while($row = $checkStmt->fetch(PDO::FETCH_ASSOC)) {
    $chartData[] = [$row['transType'], (float)$row['trans']];
    $savings = ($row['transType'] == 'Income') ? $savings + (int)$row['trans'] : $savings - (int)$row['trans'];
}

if($savings > 0) {
    $chartData[] = ['Savings', (float)$savings];
}

?>

<div class="container" id="contents">
    <div class="row">
        <?php include_once "Template/sidebar.php"; ?>
        <!-- Dashboard page content starts -->
        <div id="part2" class="col-lg-9 col-md-9"><!--this div is the "second" vertical part of the page-->

                <h3>
                    <span class="col-lg-10 col-md-10 col-sm-8 col-xs-8">Dashboard</span>
                    <a href="logout.php" id="log-out" class="btn btn-danger btn-sm col-lg-1 col-md-1 col-sm-3 col-xs-3">
                        <span class="glyphicon glyphicon-log-out"></span> Log Out
                    </a>
                </h3>
                <select id="months" class="form-control col-lg-offset-4 col-md-offset-7 col-sm-offset-8 col-xs-offset-8" style="width: 130px;" onchange="window.location.href = '?month=' + this.value">
                    <?php foreach ($months as $i => $month): ?>
                        <option value="<?=$i+1?>" <?php echo ($i+1 == $selectedMonth) ? 'selected' : ''; ?>><?=$month?></option>
                    <?php endforeach; ?>
                </select>

            <div id="piechart" class="col-lg-offset-2"></div>
            <div class="row well col-lg-offset-2 col-lg-6"><p>
                Welcome !<br><br>Know what you earn. Know what you spend and make sure what you spend is less than what you earn.</p> </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        // json_encode will covert php array to javascript
        var data = google.visualization.arrayToDataTable(<?php echo json_encode($chartData); ?> );

        var options = {
            width: 500,
            height: 300,
            title: 'My Monthly Spending',
            colors: ['#CC0000', 'green', '#317b91']
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
    }
</script>
<?php include_once "Template/footer.php"; ?>
