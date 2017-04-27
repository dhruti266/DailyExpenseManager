<?php
require_once "Template/database_conn.php";
$pagePath = __FILE__;
$userInfo = User::getUserInfo();
$page = "category";
if(!User::isUserLoggedIn()){
    header("Location: index.php");
}

$categoryName = $error = $success = "";

if(isset($_POST['edit'])){
    $categoryName = $_POST['categoryName'];
    $categoryId = $_POST['categoryId'];

    //check existing category
    $checkStmt = Connection::get()->prepare("SELECT * FROM category WHERE categoryName=:catName AND categoryId<>:catId AND userId = :userId");
    $checkStmt->bindParam(':catName', $categoryName);
    $checkStmt->bindParam(':catId', $categoryId);
    $checkStmt->bindParam(':userId', $userInfo['userId'] );
    $checkStmt->execute();
    // category name validation
    $catRegEx = "/^[a-zA-Z ]+$/";
    if($categoryName == "" || (preg_match($catRegEx, $categoryName) == 0) || (preg_match($catRegEx, $categoryName) == 0)){
       $error = "Invalid Category.";
    }
    else if($checkStmt->fetch(PDO::FETCH_ASSOC)){
        $error = "Entered category already exists.";
    }
    else {
        try{
            if((int)$categoryId > 0 ){
                $updateStmt = Connection::get()->prepare("UPDATE category SET categoryName=:catName where categoryId=:catId AND userId = :userId");
                $updateStmt->bindParam(':catName', $categoryName);
                $updateStmt->bindParam(':catId', $categoryId );
                $updateStmt->bindParam(':userId', $userInfo['userId'] );
                $updateStmt->execute();
                $success = "Category has been updated";
            }
            else{
                $insertStmt = Connection::get()->prepare("INSERT INTO category(categoryName,userId) VALUES (:catName,:userId)");
                $insertStmt->bindParam(':catName', $categoryName);
                $insertStmt->bindParam(':userId', $userInfo['userId'] );
                $insertStmt->execute();
                $success = "New category has been created";
            }

        }
        catch(Exception $ex){
            $error = "Something went wrong";
        }
    }
}

if(isset($_POST['delete'])){
    try{
        $deleteStmt = Connection::get()->prepare("DELETE FROM `category` WHERE categoryId=:catId AND userId = :userId");
        $deleteStmt->bindParam(':catId', $_POST['catId']);
        $deleteStmt->bindParam(':userId', $userInfo['userId'] );
        $deleteStmt->execute();
        $success = "Category has been removed";
    }
    catch(Exception $ex){
        $error = "Something went wrong";
    }
}

// count total records
$recordStmt = Connection::get()->prepare("SELECT COUNT(*) AS Total FROM `category` WHERE userId = :userId");
$recordStmt->bindParam(':userId', $userInfo['userId'] );
$recordStmt->execute();
$record = $recordStmt->fetch(PDO::FETCH_ASSOC);

$limit = 10;

$totalRecords = $record['Total'];
$totalPages = ceil($totalRecords/$limit);


// logic for pagination
$pageNum = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$start = ($pageNum-1) * $limit;


$stmt = Connection::get()->prepare("SELECT * from category WHERE userId = :userId ORDER BY categoryId DESC LIMIT $start,$limit");
$stmt->bindParam(':userId', $userInfo['userId'] );
$stmt->execute();

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
                <span class="col-lg-10 col-md-10 col-sm-8 col-xs-8">Category</span>
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
                <a class="btn btn-success btn-sm col-lg-offset-10 col-md-offset-10 col-sm-offset-8 col-xs-offset-8" data-id=0 data-name='' data-toggle="modal" data-target="#editModal">
                    <span class="glyphicon glyphicon-plus"></span>Add Category
                </a>
                <table id="table" class="table table-hover" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th width="85%">Name</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if($stmt->rowCount() > 0){
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            echo "<tr>
                                <td>{$row['categoryName']}</td>
                                <td>
                                    <a data-id='{$row['categoryId']}' data-name='{$row['categoryName']}' class=\"glyphicon glyphicon-edit\"  data-toggle=\"modal\" data-target=\"#editModal\"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <a data-id='{$row['categoryId']}' data-name='{$row['categoryName']}' class=\"glyphicon glyphicon-trash\"  data-toggle=\"modal\" data-target=\"#removeModal\"></a>
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
                    <form id="editForm" class="col-lg-offset-1 border" action="#" method="POST">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Save Category</h4>
                            </div>
                            <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-md-3 col-lg-2  control-label" for="categoryName">Name</label>
                                        <div class="col-md-6 col-lg-6">
                                            <input type="hidden" id="categoryId" name="categoryId">
                                            <input id="categoryName" name="categoryName" type="text" value="" placeholder="Category Name" class="form-control input-md" required>
                                        </div>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="edit" class="btn btn-success">Save</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div><!-- /.modal-content -->
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
                                <h4 class="modal-title">Remove Category</h4>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="catId" name="catId">
                                <p>Are you sure, you want to remove this category ?</p>
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
                $('#categoryName').focus();
                var link = $(event.relatedTarget); // refer to <a> tag we clicked
                $('#categoryName').val(link.data('name')); // sets category name into name text box.
                $('#categoryId').val(link.data('id'));// sets the category Id into hidden text box
            });

            $('#removeModal').on('shown.bs.modal', function (event) {
                var link = $(event.relatedTarget); // refer to <a> tag we clicked
                $('#catId').val(link.data('id'));// sets the category Id into hidden text box
                $(this).find('h4.modal-title').html(link.data('name'));
            });

        </script>

    </div>
</div>

<?php include_once "Template/footer.php"; ?>
