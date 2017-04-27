<?php
require_once "Template/database_conn.php";
$pagePath = __FILE__;
$userInfo = User::getUserInfo();
$page = "history";
if(!User::isUserLoggedIn()){
    header("Location: index.php");
}

$categoryName = $error = $success = "";

if(isset($_POST['save']))
{
    try{

        $date = $_POST['date'];
        $category = $_POST['category'];
        $amount = $_POST['amount'];
        $paymentMode = $_POST['paymentMode'];
        $transType = $_POST['transType'];
        $id = $_POST['transId'];

        if((int)$id > 0 ){
            $updateStmt = Connection::get()->prepare("UPDATE `transaction` SET transType=:transType, `date`=:pdate, category=:category, amount=:amount, paymentMode=:paymentMode  WHERE transId=:id AND userId = :userId");
            $updateStmt->bindParam(':id', $id );
            $updateStmt->bindParam(':pdate', $date);
            $updateStmt->bindParam(':category', $category );
            $updateStmt->bindParam(':amount', $amount );
            $updateStmt->bindParam(':paymentMode', $paymentMode );
            $updateStmt->bindParam(':transType', $transType );
            $updateStmt->bindParam(':userId', $userInfo['userId'] );

            $updateStmt->execute();
            $success = "Category has been updated";
        }
        else{
            $insertStmt = Connection::get()->prepare("INSERT INTO `transaction`(transId,category,date,amount,paymentMode,transType,userId) VALUES (:id,:category,:date,:amount,:paymentMode,:transType,:userId)");
            $insertStmt->bindParam(':id', $categoryId );
            $insertStmt->bindParam(':date', $categoryName);
            $insertStmt->bindParam(':category', $categoryId );
            $insertStmt->bindParam(':amount', $categoryId );
            $insertStmt->bindParam(':paymentMode', $categoryId );
            $insertStmt->bindParam(':transType', $categoryId );
            $insertStmt->bindParam(':userId', $userInfo['userId'] );
            $insertStmt->execute();
            $success = "New Transaction has been created";
        }

    }
    catch(Exception $ex){
        $error = "Something went wrong";
    }
}

if(isset($_POST['delete'])){
    try{
        $deleteStmt = Connection::get()->prepare("DELETE FROM `transaction` WHERE transId=:transId AND userId = :userId");
        $deleteStmt->bindParam(':transId', $_POST['transId2']);
        $deleteStmt->bindParam(':userId', $userInfo['userId'] );
        $deleteStmt->execute();
        $success = "Transaction has been removed";
    }
    catch(Exception $ex){
        $error = "Something went wrong";
    }
}

// count total records according to selected drop down item for pagination
if(isset($_POST['selectedMonth'])) {
    $selectedMonth = date('m',strtotime($_POST['selectedMonth']));
    $recordStmt = Connection::get()->prepare("SELECT COUNT(*) AS Total FROM `transaction` WHERE MONTH(date) = :month AND userId = :userId");
    $recordStmt->bindParam(':month', $selectedMonth);
    $recordStmt->bindParam(':userId', $userInfo['userId'] );
}
else if(isset($_POST['selectedCategory'])) {
    $selectedCategory = $_POST['selectedCategory'];
    $recordStmt = Connection::get()->prepare("SELECT COUNT(*) AS Total FROM `transaction` WHERE category = :category AND userId = :userId");
    $recordStmt->bindParam(':category', $selectedCategory);
    $recordStmt->bindParam(':userId', $userInfo['userId'] );
}
else if(isset($_POST['selectedDate'])) {
    $selectedDate = date('Y-m-d',  strtotime($_POST['selectedDate']));
    $recordStmt = Connection::get()->prepare("SELECT COUNT(*) AS Total FROM `transaction` WHERE date = :date AND userId = :userId");
    $recordStmt->bindParam(':date', $selectedDate);
    $recordStmt->bindParam(':userId', $userInfo['userId'] );
}
else{
    $recordStmt = Connection::get()->prepare("SELECT COUNT(*) AS Total FROM `transaction` WHERE userId = :userId");
    $recordStmt->bindParam(':userId', $userInfo['userId'] );
}

$recordStmt->execute();
$record = $recordStmt->fetch(PDO::FETCH_ASSOC);

$limit = 10;
$totalRecords = $record['Total'];
$totalPages = ceil($totalRecords/$limit);


// logic for pagination
$pageNum = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($pageNum-1) * $limit;




// get transaction data for selected month
if(isset($_POST['selectedMonth'])){
    $selectedMonth = date('m',strtotime($_POST['selectedMonth'])); // convert month string to integer (eg. January to 01)
    $stmt = Connection::get()->prepare("SELECT transId,category,`date`,amount,paymentMode,transType FROM `transaction` WHERE MONTH(date) = :month AND userId = :userId ORDER BY `category` ASC LIMIT $start,$limit");
    $stmt->bindParam(':month', $selectedMonth);
    $stmt->bindParam(':userId', $userInfo['userId'] );
    $stmt->execute();
}
else if(isset($_POST['selectedCategory'])){
    $selectedCategory = $_POST['selectedCategory'];
    $stmt = Connection::get()->prepare("SELECT transId,category,`date`,amount,paymentMode,transType FROM `transaction` WHERE category = :category AND userId = :userId ORDER BY `category` ASC LIMIT $start,$limit");
    $stmt->bindParam(':category', $selectedCategory);
    $stmt->bindParam(':userId', $userInfo['userId'] );
    $stmt->execute();
}
else if(isset($_POST['selectedDate'])){
    $selectedDate = date('Y-m-d',  strtotime($_POST['selectedDate']));
    $stmt = Connection::get()->prepare("SELECT transId,category,`date`,amount,paymentMode,transType FROM `transaction` WHERE date = :date AND userId = :userId ORDER BY `category` ASC LIMIT $start,$limit");
    $stmt->bindParam(':date', $selectedDate);
    $stmt->bindParam(':userId', $userInfo['userId'] );
    $stmt->execute();
}
else {
    $stmt = Connection::get()->prepare("SELECT transId,category,`date`,amount,paymentMode,transType FROM `transaction` WHERE userId = :userId ORDER BY `category` ASC LIMIT $start,$limit");
    $stmt->bindParam(':userId', $userInfo['userId'] );
    $stmt->execute();
}

$categoryDropstmt = Connection::get()->prepare("SELECT categoryId, categoryName FROM category WHERE userId = :userId ORDER BY categoryId ASC");
$categoryDropstmt->bindParam(':userId', $userInfo['userId'] );
$categoryDropstmt->execute();

$categories = [];
while ($row = $categoryDropstmt->fetch(PDO::FETCH_ASSOC)) {
    $categories[$row['categoryId']] = $row['categoryName'];
}
function get_pagination($pageNum, $totalPages) {

    if($totalPages < 2){
        return;
    }
    $html = '';
    $current = $pageNum;

    // previous
    if($pageNum>1) {
        $html .= "<li><a href='?page=".($pageNum-1)."'> PREVIOUS </a></li>";
    }
    if($totalPages > 2) {
        if ($pageNum == $totalPages) {
            $current = $pageNum - 2;
        } else if ($pageNum + 1 == $totalPages) {
            $current = $pageNum - 1;
        }
    }
    else {
        $current = $pageNum;
    }


    // loop for next five
    for($i=1; $i<4; $i++) {
        if($current <= $totalPages) {
            $html .= "<li class='" .($pageNum==$current ? 'active' : ''). "'><a href='?page={$current}'>{$current}</a></li>";
            $current++;
        }
    }

    // next
    if($pageNum<$totalPages) {
        $html .= "<li><a href='?page=" .($pageNum+1) ."'> NEXT </a></li></a>";
    }

    return $html;
}


include_once "Template/header.php";
?>


<div class="container" id="contents">
    <div class="row">
        <?php include_once "Template/sidebar.php"; ?>
        <!-- category page content starts -->
        <div id="part2" class="col-lg-9 col-md-9"><!--this div is the "second" vertical part of the page-->

            <h3>
                <span class="col-lg-10 col-md-10 col-sm-8 col-xs-8">History</span>
                <a href="logout.php" id="log-out" class="btn btn-danger btn-sm col-lg-1 col-md-1 col-sm-3 col-xs-3">
                    <span class="glyphicon glyphicon-log-out"></span> Log Out
                </a>
            </h3>

            <div class="col-lg-10 col-md-10">
                <?php if($error != ""): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Error!</strong> <?php echo $error; ?>
                    </div>
                <?php  elseif($success != ""):?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Success!</strong> <?php echo $success; ?>
                    </div>
                <?php endif;?>

                <div class="btn-toolbar">

                    <!--All button-->
                    <div class="btn-btn">
                        <form action="history.php" name="allForm" method="post">
                            <button type="submit" class="btn btn-primary" >All</button>
                        </form>
                    </div>

                    <!--Month button with dropdown menu-->
                    <div class="btn-group month">
                        <button type="button"  for="month" class="btn btn-success">Month</button>
                        <button type="button" data-toggle="dropdown" class="btn btn-success dropdown-toggle"><span class="caret"></span></button>
                        <form action="history.php" name="monthForm" method="post">
                            <input type="hidden" id="selectedMonth" name="selectedMonth" />
                        </form>
                        <ul name="" id="dropdown-month" class="dropdown-menu" >
                            <li name="month"><a href="#" data-id=0 data-value='january' data-toggle="modal" data-target="#monthModal">January</a></li>
                            <li name="month"><a href="#" data-id=0 data-value='february' data-toggle="modal" data-target="#monthModal">February</a></li>
                            <li name="month"><a href="#" data-id=0 data-value='march' data-toggle="modal" data-target="#monthModal">March</a></li>
                            <li name="month"><a href="#" data-id=0 data-value='april' data-toggle="modal" data-target="#monthModal">April</a></li>
                            <li name="month"><a href="#" data-id=0 data-value='may' data-toggle="modal" data-target="#monthModal">May</a></li>
                            <li name="month"><a href="#" data-id=0 data-value='june' data-toggle="modal" data-target="#monthModal">June</a></li>
                            <li name="month"><a href="#" data-id=0 data-value='july' data-toggle="modal" data-target="#monthModal">July</a></li>
                            <li name="month"><a href="#" data-id=0 data-value='august' data-toggle="modal" data-target="#monthModal">August</a></li>
                            <li name="month"><a href="#" data-id=0 data-value='september' data-toggle="modal" data-target="#monthModal">September</a></li>
                            <li name="month"><a href="#" data-id=0 data-value='october' data-toggle="modal" data-target="#monthModal">October</a></li>
                            <li name="month"><a href="#" data-id=0 data-value='november' data-toggle="modal" data-target="#monthModal">November</a></li>
                            <li name="month"><a href="#" data-id=0 data-value='december' data-toggle="modal" data-target="#monthModal">December</a></li>
                        </ul>
                    </div>

                    <!--Date button with calender -->
                        <div class="btn-group date">
                            <button type="button" class="btn btn-info">Date</button>
                            <button type="button" name="date" data-toggle="dropdown" class="btn btn-info dropdown-toggle"><span class="caret"></span></button>
                            <form action="history.php" name="dateForm" method="post">
                                <input type="hidden" id="selectedDate" name="selectedDate" />
                            </form>
                            <ul class="dropdown-menu">
                                <li><input class="form-control" id="input-date" type="date" name='dateInput' value="<?php echo date('m-d-Y'); ?>"></li>
                            </ul>
                        </div>

                    <!--Category button with dropdown menu-->
                        <div class="btn-group category">
                            <button type="button" class="btn btn-warning">Category</button>
                            <button type="button" name="category" data-toggle="dropdown" class="btn btn-warning dropdown-toggle" id="dropdown"><span class="caret"></span></button>
                            <form action="history.php" name="categoryForm" method="post">
                                <input type="hidden" id="selectedCategory" name="selectedCategory" />
                            </form>
                            <ul class="dropdown-menu" id="dropdown-category">
                                <?php foreach ($categories as $category) {
                                    echo "<li><a data-id=0 data-value={$category} data-toggle='modal' data-target='#categoryModal'>{$category}</a></li>";
                                }?>
                            </ul>
                        </div>
                </div>

                <table id="table" class="table table-hover" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Payment Mode</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if($stmt->rowCount() > 0){
                        // get all transaction records
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            echo "<tr>
                                    <td>{$row['date']}</td>
                                    <td>{$row['category']}</td>
                                    <td>{$row['amount']}</td>
                                    <td>{$row['paymentMode']}</td>
                                    <td>{$row['transType']}</td>
                                    <td>
                                        <a data-id='{$row['transId']}' data-tdate='{$row['date']}' data-mode='{$row['paymentMode']}' data-type='{$row['transType']}' data-amount='{$row['amount']}' data-category='{$row['category']}' class=\"glyphicon glyphicon-edit\"  data-toggle=\"modal\" data-target=\"#editModal\"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a data-id='{$row['transId']}' data-tdate='{$row['date']}' data-mode='{$row['paymentMode']}' data-type='{$row['transType']}' data-amount='{$row['amount']}' data-category='{$row['category']}' class=\"glyphicon glyphicon-trash\"  data-toggle=\"modal\" data-target=\"#removeModal\"></a>
                                    </td>
                                 </tr>";
                        }
                    }
                    else{
                        echo "<tr>
                                  <td class='result' colspan='6'>No Results...</td>
                              </tr>";
                    }

                    ?>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <ul class="pagination col-lg-10 col-md-10">
                    <?php echo get_pagination($pageNum, $totalPages); ?>
                </ul>
            </div>

            <!-- Edit category pop up modal -->
            <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form id="editForm" role="form" onsubmit="return validateAmount()" name="tform" method="POST">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Edit Transaction</h4>
                            </div>
                            <div class="modal-body">
                                <div class="col-lg-11"><!-- this div contains all the contents of form : label and input-->

                                    <div id="transType_container" class="form-group row"><!-- this div contains label and input of category-->
                                        <label class="col-md-5 col-lg-5 control-label" for="transTye">Transaction Type</label>
                                        <div class="col-md-6 col-lg-6">
                                            <select name="transType" class="form-control" id="transType">
                                                <option>Income</option>
                                                <option>Expense</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div id="amount_container" class="form-group row"><!-- this div contains label and input of amount-->
                                        <!--<div class="col-md-1 col-lg-1"></div>-->
                                        <label  class="col-md-5 col-lg-5 control-label" for="amount">Amount</label>
                                        <div  class="col-md-6 col-lg-6">
                                            <input name = "transId" id = "transId" type = "hidden"/>
                                            <input id="amount" name="amount" type="text" placeholder="Enter amount here..." class="form-control input-md" required/>
                                        </div>
                                        <div  id="amountError" class="col-md-1 col-lg-1">
                                            <!-- this div is besides the input of amount and it is build to display the error message for wrong input-->
                                        </div>
                                    </div>

                                    <div id="category_container" class="form-group row"><!-- this div contains label and input of category-->
                                        <label class="col-md-5 col-lg-5 control-label" for="category">Category</label>
                                        <div class="col-md-6 col-lg-6">
                                            <select name="category" class="form-control" id="category">
                                                <?php foreach ($categories as $category) {
                                                    echo "<option name='category' class='form-control input-md'><a>{$category}</a></option>";
                                                }?>
                                            </select>
                                        </div>
                                    </div>

                                    <div id="date_container" class="form-group row"><!-- this div contains label and input of date-->
                                        <label  class="col-md-5 col-lg-5 control-label" for="date">Date</label>
                                        <div  class="col-md-6 col-lg-6">
                                            <input class="form-control" id="date" name="date" type="date" value="<?php echo date('Y-m-d'); ?>"/>
                                        </div>
                                    </div>

                                    <div id="paymentmode_container" class="form-group row"><!-- this div contains label and input of payment mode-->
                                        <label  class="col-md-5 col-lg-5 control-label" for="paymentMode">Payment Mode</label>
                                        <div  class="col-md-6 col-lg-6">
                                            <select class="form-control" id="paymentMode" name="paymentMode">
                                                <option>Cash</option>
                                                <option>Credit</option>
                                                <option>Debit</option>
                                                <option>E-transfer</option>
                                                <option>other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="popup-save"><!-- this div is below all the inputs-->
                                        <button id="reset" type="reset" class="btn btn-default">Reset</button>
                                        <button id="save" name = 'save' type="submit" class="btn btn-info">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

            <!-- Remove category pop up modal -->
            <div id="removeModal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form id="removeForm" class="col-lg-offset-1 border" action="#" method="POST">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Remove Transaction</h4>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="amount2" name="amount2">
                                <input type="hidden" id="transId2" name="transId2">
                                <input type="hidden" id="transType2" name="transType2">
                                <input type="hidden" id="category2" name="category2">
                                <input type="hidden" id="paymentMode2" name="paymentMode2">
                                <input type="hidden" id="date2" name="date2">
                                <p>Are you sure, you want to remove this transaction ?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </form>
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div>

        <script type="text/javascript">

            $('#editModal').on('shown.bs.modal', function (event) {
                var link = $(event.relatedTarget); // refer to <a> tag we clicked
                $('#amount').focus();
                $('#amount').val(link.data('amount')); // sets category name into name text box.
                $('#transId').val(link.data('id'));// sets the category Id into hidden text box
                $('#transType').val(link.data('type'));// sets the category Id into hidden text box
                $('#category').val(link.data('category'));// sets the category Id into hidden text box
                $('#paymentMode').val(link.data('mode'));// sets the category Id into hidden text box
                $('#date').val(link.data('tdate'));// sets the category Id into hidden text box
            });

            $('#removeModal').on('shown.bs.modal', function (event) {
                var link = $(event.relatedTarget); // refer to <a> tag we clicked
                $('#amount2').focus();
                $('#amount2').val(link.data('amount')); // sets category name into name text box.
                $('#transId2').val(link.data('id'));// sets the category Id into hidden text box
                $('#transType2').val(link.data('type'));// sets the category Id into hidden text box
                $('#category2').val(link.data('category'));// sets the category Id into hidden text box
                $('#paymentMode2').val(link.data('mode'));// sets the category Id into hidden text box
                $('#date2').val(link.data('tdate'));// sets the category Id into hidden text box
            });

        </script>
        <script>
            // click event on month dropdown
            $('.month li a').click(function(e){
                var month=$(this).data("value");
                $('#selectedMonth').val(month);
                document.monthForm.submit();
            });

            // click event on category drop down
            $('.category li a').click(function(e){
                var category=$(this).data("value");
                $('#selectedCategory').val(category);
                document.categoryForm.submit();
            });

            // click event on date dropdown
            $('.date li input').change(function(e){
                var date=$(this).val();
                $('#selectedDate').val(date);
                document.dateForm.submit();
            });


        </script>

    </div>
</div>

<?php include_once "Template/footer.php";?>

