<?php
$pagePath = __FILE__;

$userInfo = User::getUserInfo();
    try {

        $success = null;

        if(isset($_POST['save']))
        {
            $amount = $_POST['amount'];
            $category = $_POST['category'];
            $date = $_POST['date1'];
            $description = $_POST['description'];
            $paymentMode = $_POST['paymentMode'];


            if ($amount) {
                $stmt = Connection::get()->prepare("INSERT INTO `transaction`  (amount,category,date,description,paymentMode,transType,userId)
                                                VALUES (:amount,:category,:date,:description,:paymentMode,:transType,:userId)");
                $stmt->bindParam(':amount',$amount);
                $stmt->bindParam(':category',$category);
                $stmt->bindParam(':date',$date);
                $stmt->bindParam(':description',$description);
                $stmt->bindParam(':paymentMode',$paymentMode);
                $stmt->bindParam(':transType',$transType);
                $stmt->bindParam(':userId',$userInfo['userId']);
                $stmt->execute();
                $success = "New Transaction added successfully.";
            }
        }

    }
    catch (OutOfRangeException $exception){
    }

    // get category
    $checkStmt = Connection::get()->prepare("SELECT * FROM category WHERE userId = :userId");
    $checkStmt->bindParam(':userId',$userInfo['userId']);
    $checkStmt->execute();

?>

    <h3>
        <span class="col-lg-10 col-md-10 col-sm-8 col-xs-8">Add Transaction</span>
        <a href="logout.php" id="log-out" class="btn btn-danger btn-sm col-lg-1 col-md-1 col-sm-3 col-xs-3">
            <span class="glyphicon glyphicon-log-out"></span> Log Out
        </a>
    </h3>

    <?php if($success != null): ?>
        <div class="alert alert-success alert-dismissible col-lg-9 col-lg-offset-1 col-md-10 col-sm-10 col-xs-10" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong>Success!</strong> <?php echo $success; ?>
        </div>
    <?php endif;?>

    <form id="add_transaction-form" role="form" name="tform" method="POST" onsubmit="return validateAmount();" >
        <div class="row">
            <nav id="inc-exp" class="btn-group col-lg-10 col-md-10 col-sm-10 col-xs-10" data-toggle="buttons">
                <!-- Indicates a successful or positive action -->
                <button id='incbtn' type = "button" name='income' class="btn btn-success col-lg-6 col-md-6 col-sm-6 col-xs-6 <?php echo $transType == 'Income' ? 'disabled' : 'active'; ?>">Income</button>
                <!-- Indicates a dangerous or potentially negative action -->
                <button id='expbtn' type = "button" name='expense' class="btn btn-danger col-lg-6 col-md-6 col-sm-6 col-xs-6 <?php echo $transType == 'Expense' ? 'disabled' : 'active'; ?>">Expense</button>
            </nav>
        </div>
        <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11"><!-- this div contains all the contents of form : label and input-->
            <div id="amount_container" class="form-group row"><!-- this div contains label and input of amount-->
                <!--<div class="col-md-1 col-lg-1"></div>-->
                <label  class="col-md-5 col-lg-3 col-lg-offset-1 control-label" for="amount">Amount</label>
                <div  class="col-md-6 col-lg-6">
                    <input name="transType" id="transType" type="hidden">
                    <input id="amount" name="amount" type="text" placeholder="Enter amount here..." class="form-control input-md" />
                </div>
                <div  id="amountError" class="col-md-1 col-lg-2"></div><!-- this div is besides the input of amount and it is build to display the error message for wrong input-->
            </div>

            <div id="category_container" class="form-group row"><!-- this div contains label and input of category-->
                <label class="col-md-5 col-lg-3 col-lg-offset-1 control-label" for="category">Category</label>
                <div class="col-md-6 col-lg-6">
                    <select name = "category" class="form-control" id="category">
                        <?php while ($row = $checkStmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <option><?=$row['categoryName']?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div  id="categoryError" class="col-md-1 col-lg-2"></div><!-- this div is besides the input of amount and it is build to display the error message for wrong input-->
            </div>

            <div id="date_container" class="form-group row"><!-- this div contains label and input of date-->
                <label  class="col-md-5 col-lg-3 col-lg-offset-1 control-label" for="date">Date</label>
                <div  class="col-md-6 col-lg-6">
                    <input class="form-control" id="input-date" name="date1" type="date" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>

            <div id="description_container" class="form-group row"><!-- this div contains label and input of description-->
                <label class="col-md-5 col-lg-3 col-lg-offset-1 col-sm-12 col-xs-12 control-label" for="description">Description</label>
                <div  class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                    <textarea class="form-control" rows="5" id="description" name="description" placeholder="Enter description of the transaction here..."></textarea>
                </div>
            </div>

            <div id="paymentmode_container" class="form-group row"><!-- this div contains label and input of payment mode-->
                <label  class="col-md-5 col-lg-3 col-lg-offset-1 col-sm-12 col-xs-12 control-label" for="paymentMode">Payment Mode</label>
                <div  class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                    <select class="form-control" id="payment-mode" name="paymentMode">
                        <option>Cash</option>
                        <option>Credit</option>
                        <option>Debit</option>
                        <option>E-transfer</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-11 col-md-11 col-sm-11 col-xs-11">
                <button  id="reset" type="reset" class="btn btn-default btn-sm">Reset</button>
                <button id="save" name = 'save' type="submit" class="btn btn-info btn-sm">Save</button>
            </div>
        </div>
    </form>

<script>

    var amount = document.forms["tform"]["amount"];
    var category = document.forms["tform"]["category"];
    var amountError = document.getElementById("amountError");
    amount.addEventListener("blur", amountVerify, true);

    function amountVerify(){
        if(!isNaN(amount.value)){
            amount.style.border = "1px solid #5E6E66";
            amountError.innerHTML = "";
            return true;
        }
    }

    function validateAmount (){
        if(!category.value) {
            category.focus();
            category.style.border = "1px solid red";
            categoryError.innerHTML = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span> Invalid Category!";
            return false;
        }
        if(amount.value == "" || !(/^[-+]?\d*\.?\d*$/.test(amount.value))){
            amount.style.border = "1px solid red";
            amountError.innerHTML = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span> Invalid!";
            amount.focus();
            return false;
        }
        amountError.innerHTML = "<span style = 'color: green' class = 'glyphicon glyphicon-ok'></span>";
        return true;
    }

    $(function(){

        $('#incbtn').click(function(){
            window.location='add_transaction.php';
        });

        $('#expbtn').click(function(){
            window.location='add_transaction1.php';
        });
    });
</script>
