

<div class="col-lg-3 col-md-3">
    <ul class="nav nav-pills nav-stacked" id="navigation">
        <li role="presentation" class="<?php if($page == "dashboard") echo "active"; else echo ''; ?>"><a href="dashboard.php"><span class="glyphicon glyphicon-dashboard" aria-hidden="true"></span>   Dashboard</a></li>
        <li role="presentation" class="<?php if($page == "transaction") echo "active"; else echo ''; ?>"><a href="add_transaction.php"><span class="glyphicon glyphicon-transfer" aria-hidden="true"></span>   Transactions</a></li>
        <li role="presentation" class="<?php if($page == "category") echo "active"; else echo ''; ?>"><a href="category.php"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span>   Category</a></li>
        <li role="presentation" class="<?php if($page == "history") echo "active"; else echo ''; ?>"><a href="history.php"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>   History</a></li>
    </ul>
</div>
