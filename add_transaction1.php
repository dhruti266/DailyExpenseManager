<?php
require_once "Template/database_conn.php";

$transType  = 'Expense';
$page = "transaction";

if(!User::isUserLoggedIn()){
    header("Location: index.php");
}

include_once "Template/header.php";
?>

<div class="container" id="contents">
    <div class="row">
        <?php include_once "Template/sidebar.php"; ?>

        <!-- add transaction page content starts -->
        <div id="part2" class="col-lg-9 col-md-9"><!--this div is the "second" vertical part of the page-->
            <?php include_once "Template/transaction.php"; ?>
        </div>

    </div>
</div>

<?php include_once "Template/footer.php"; ?>

