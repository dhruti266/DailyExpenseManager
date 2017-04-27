<?php include_once "Template/database_conn.php";
include_once "Template/header.php";
$pagePath = __FILE__;
$usernameError = "";
$pinError = "";
$passwordError = "";
$cPasswordError = "";
$success = "";

$validUsername = $validPin = $validCPassword = $validPassword = false;
if(isset($_POST['update'])) {

    // get the values from text boxes
    $username = $_POST['username'];
    $pin = $_POST['pin'];
    $password = $_POST['password'];
    $cPassword = $_POST['confirm_password'];

    // username validation
    // Security pin validation
    $usernameRegEx = "/^[0-9a-zA-Z]{3,10}$/";
    if (!User::checkIfUserWithPinExists($username,$pin)) {
        $usernameError = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span> User not found!";
        $pinError = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span> Invalid PIN!";
    } else {
        $usernameError = "<span style = 'color: green' class = 'glyphicon glyphicon-ok'></span>";
        $pinError = "<span style = 'color: green' class = 'glyphicon glyphicon-ok'></span>";
        $validUsername = true;
        $validPin = true;
    }

    // password validation
    if ($password === "") {
        $passwordError = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span>  Password required!";
    } else if (strlen($password) < 8) {
        $passwordError = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span>  Invalid(Min 8)!";
    } else {
        $passwordError = "<span style = 'color: green' class = 'glyphicon glyphicon-ok'></span>";
        $validPassword = true;
    }

    //validate if passwords are matching
    if ($cPassword === "") {
        $cPasswordError = "<span style = 'color: red'class = 'glyphicon glyphicon-remove'></span>  Please confirm the password!";
    } else if ($password !== $cPassword) {
        $cPasswordError = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span>  Password does not match!";
    } else {
        $cPasswordError = "<span style = 'color: green' class = 'glyphicon glyphicon-ok'></span>";
        $validCPassword = true;
    }
}
    try{
        if($validUsername == true && $validPin == true && $validPassword == true && $validCPassword == true){
            $encrypt = md5($password);
            $success = "New Password Updated.. Now try to Login.";
            $stmt = Connection::get()->prepare("UPDATE user_information SET password=:password WHERE username=:username AND pin=:pin");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':pin', $pin);
            $stmt->bindParam(':password', $encrypt );
            $stmt->execute();
        }
    }
    catch(Exception $ex){
        /* if (!$stmt) {
             echo "\nPDO::errorInfo():\n";
             print_r(Connection::get()->errorInfo());}*/
        print $ex->getMessage();
    }
?>
<div class="container" id="contents">
    <div class="row error">
        <div class = "col-lg-6 col-lg-offset-3">
            <?php
            if(isset($_POST['update'])){
                if($success != ""): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Success!</strong> <?php echo $success; ?>
                    </div>
                <?php endif; }?>
        </div>
        <div id="part3"  class="col-lg-2 col-lg-offset-1 col-xs-2 col-sm-2 col-md-2">
            <a href="index.php" id="log-out" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-log-in"></span> Sign In
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-sm-8 col-lg-offset-3" id="forgot">
            <h3 class="col-lg-offset-2">Create a New Password</h3>

            <br>
            <form role="form" action="#" method="POST">

                <div class="form-group row">
                    <label class="col-md-3 col-lg-3 control-label" for="username">Username</label>
                    <div class="col-md-6 col-lg-6">
                        <input id="username" name="username" type="text" placeholder="Username" class="form-control input-md" required>
                    </div>
                    <div class="col-md-3 col-lg-3" id = "usernameError"><?php echo $usernameError; ?></div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-lg-3 control-label" for="security">Security PIN</label>
                    <div class="col-md-6 col-lg-6">
                        <input id="security" name="pin" type="text" placeholder="Enter 5-digit security PIN..." class="form-control input-md" required>
                    </div>
                    <div class="col-md-3 col-lg-3" id = "usernameError"><?php echo $pinError; ?></div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-lg-3 control-label" for="passwordinput">Password</label>
                    <div class="col-md-6 col-lg-6">
                        <input id="password" name="password" type="password" placeholder="Password" class="form-control input-md" required>
                    </div>
                    <div class="col-md-3 col-lg-3" id = "usernameError"><?php echo $passwordError; ?></div>
                </div>

                <!-- Password input-->
                <div class="form-group row">
                    <label class="col-md-3 col-lg-3 control-label" for="confirm_password">Confirm Password</label>
                    <div class="col-md-6 col-lg-6">
                        <input id="confirm_password" name="confirm_password" type="password" placeholder="Re-type password" class="form-control input-md" required>
                    </div>
                    <div class="col-md-3 col-lg-3" id = "usernameError"><?php echo $cPasswordError; ?></div>
                </div>
                <div class="form-group row"></div>
                <div class="form-group col-md-9 col-lg-9">
                    <input type="submit" class="btn btn-md pull-right btn-success" name="update" value="Update">
                </div>
            </form>
        </div>

        </div>
    </div>


<?php include_once "Template/footer.php"; ?>


