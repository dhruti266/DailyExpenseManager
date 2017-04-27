<?php require_once "Template/database_conn.php";
$error = $success = "";
$pagePath = __FILE__;
// checks valid user name and password to sign in
if(isset($_SESSION['username'])){
    header("Location: dashboard.php");
}
if(isset($_POST['submit']))
{
    $user = User::checkIfUserExists($_POST['loginname'], md5($_POST['password']));

    if( $user )
    {
        $_SESSION['username'] = $user;
        header("Location: dashboard.php");
    }
    else{
        $error = "Username and password does not match.";
    }
}

include_once "Template/header.php";
// registration form validation
$nameError = "";
$usernameError = "";
$pinError = "";
$passwordError = "";
$cPasswordError = "";

$firstName = $lastName = $username = $pin = $password = $cPassword = "";

$validFName = $validLName = $validUsername = $validPin = $validCPassword = $validPassword = false;
if(isset($_POST['signup'])){
    // get the values from text boxes
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $pin = $_POST['pin'];
    $password = $_POST['password'];
    $cPassword = $_POST['confirm_password'];


    // first and last name validation
    $nameRegEx = "/^[a-zA-Z ]+$/";
    if((preg_match($nameRegEx, $firstName) == 0) || (preg_match($nameRegEx, $lastName) == 0)){
        $nameError = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span> Invalid!";
    }
    else {
        $nameError = "<span style = 'color: green' class = 'glyphicon glyphicon-ok'></span>";
        $validFName = true;
        $validLName = true;
    }

    // username validation
    $usernameRegEx = "/^[0-9a-zA-Z]{3,10}$/";
    if((preg_match($usernameRegEx, $username) == 0)){
        $usernameError = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span> Invalid!";
    }
    else if(User::checkIfUserExists($username)){
        $usernameError = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span> Username Exists!";
    }
    else {
        $usernameError = "<span style = 'color: green' class = 'glyphicon glyphicon-ok'></span>";
        $validUsername = true;
    }

    // Security pin validation
    $pinRegEx = "/^[0-9]{4}$/";
    if(preg_match($pinRegEx, $pin) == 0){
        $pinError = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span> Invalid!(Min 4)";
    }
    else {
        $pinError = "<span style = 'color: green' class = 'glyphicon glyphicon-ok'></span>";
        $validPin = true;
    }

    // password validation
    if ($password === "") {
        $passwordError = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span>  Password required!";
    }
    else if (strlen($password) < 8) {
        $passwordError = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span>  Invalid(Min 8)!";
    }
    else {
        $passwordError = "<span style = 'color: green' class = 'glyphicon glyphicon-ok'></span>";
        $validPassword = true;
    }

    //validate if passwords are matching
    if ($cPassword === "") {
        $cPasswordError = "<span style = 'color: red'class = 'glyphicon glyphicon-remove'></span>  Please confirm the password!";
    }
    else if ($password !== $cPassword){
        $cPasswordError = "<span style = 'color: red' class = 'glyphicon glyphicon-remove'></span>  Password does not match!";
    }
    else {
        $cPasswordError = "<span style = 'color: green' class = 'glyphicon glyphicon-ok'></span>";
        $validCPassword = true;
    }
}
try{

    if($validFName = true && $validLName == true && $validUsername == true && $validPin == true && $validPassword == true && $validCPassword == true)
    {
        $password = md5($password);
        $success = "Registeration Completed.. Now try to Login.";
        $stmt = Connection::get()->prepare("INSERT INTO user_information (firstName, lastName, username, pin, password) VALUES (:firstName,:lastName,:username,:pin,:password);");
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':pin', $pin);
        $stmt->bindParam(':password', $password );
        $stmt->execute();

        $userId = Connection::get()->lastInsertId();
        $defaultCategoryArray = ['Hydro', 'Loan', 'School Fee'];

        foreach ($defaultCategoryArray as $item) {
            $stmt = Connection::get()->prepare("INSERT INTO `category`(`categoryName`, `userId`) VALUES (:categoryName,:userId)");
            $stmt->bindParam(':categoryName', $item);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            unset($stmt);
        }
    }
}
catch(Exception $ex){
     /* if (!$stmt) {
          echo "\nPDO::errorInfo():\n";
          print_r(Connection::get()->errorInfo());}*/
    print $ex->getMessage();
}

?>

<div id="contents">
    <div class="row error">
        <div class = "col-lg-6 col-lg-offset-3">
            <?php
            if(isset($_POST['submit'])){
                if($error != ""): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Error!</strong> <?php echo $error; ?>
                    </div>
            <?php endif; }
            else if(isset($_POST['signup'])){
                if($success != ""): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Success!</strong> <?php echo $success; ?>
                    </div>
            <?php endif; }?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-sm-4" id="login">
            <h3 class="col-lg-offset-4 col-md-offset-4">Sign In</h3>
            <br>
            <form role="form"  class="col-lg-offset-1" method="POST">
                <fieldset>
                    <div class="row">
                        <div class="col-sm-12 col-lg-10 col-md-10 col-md-offset-1 col-lg-offset-1">
                            <div class="form-group">
                                <!-- Username -->
                                <div class="input-group">
												<span class="input-group-addon">
													<i class="glyphicon glyphicon-user"></i>
												</span>
                                    <input class="form-control" placeholder="Username" name="loginname" type="text" autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
												<span class="input-group-addon">
													<i class="glyphicon glyphicon-lock"></i>
												</span>
                                    <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <a href="forgot_password.php" >Forgot password ?</a>
                                <input type="submit" class="btn btn-md pull-right" name="submit" value="Sign in">
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>

        <div class="col-lg-8 col-sm-8" id="register">
            <h3 class="col-lg-offset-2 col-md-offset-3">Create a New Account</h3>
            <br>
            <form role="form" class="col-lg-offset-1 border" action="#" method="POST">
                <div class="form-group row">
                    <label class="col-md-3 col-lg-2 col-sm-3 col-xs-12 control-label" for="Name">Name</label>
                    <div class="col-md-3 col-lg-3 col-sm-3 col-xs-6">
                        <input id="fName" name="firstName" type="text" value="<?php echo htmlentities($firstName); ?>" placeholder="First Name" class="form-control input-md" required>
                    </div>
                    <div class="col-md-3 col-lg-3 col-sm-3 col-xs-6">
                        <input id="lName" name="lastName" type="text" value="<?php echo htmlentities($lastName); ?>" placeholder="Last Name" class="form-control input-md" required>
                    </div>
                    <div class="col-md-3 col-lg-4 col-sm-3 col-xs-12" id = "nameError"><?php echo $nameError; ?></div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-lg-2 col-sm-3 control-label" for="username">Username</label>
                    <div class="col-md-6 col-lg-6 col-sm-6">
                        <input id="username" name="username" type="text" value="<?php echo htmlentities($username); ?>" placeholder="Username" class="form-control input-md" required>
                    </div>
                    <div class="col-md-3 col-lg-4 col-sm-3" id = "usernameError"><?php echo $usernameError; ?></div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-lg-2 col-sm-3 control-label" for="security">Security PIN</label>
                    <div class="col-md-6 col-lg-6 col-sm-6">
                        <input id="security" name="pin" type="text" value="<?php echo htmlentities($pin); ?>" placeholder="Enter 4-digit security PIN..." class="form-control input-md" required>
                    </div>
                    <div class="col-md-3 col-lg-4 col-sm-3" id ="pinError"><?php echo $pinError; ?></div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-lg-2 col-sm-3 control-label" for="passwordinput">Password</label>
                    <div class="col-md-6 col-lg-6 col-sm-6">
                        <input id="password" name="password" type="password" placeholder="Enter minimum 8 character long password.." class="form-control input-md" required>
                    </div>
                    <div class="col-md-3 col-lg-3 col-sm-3" id = "passwordError"><?php echo $passwordError; ?></div>
                </div>

                <!-- Password input-->
                <div class="form-group row">
                    <label class="col-md-3 col-lg-2 col-sm-3 control-label" for="confirm_password">Confirm</label>
                    <div class="col-md-6 col-lg-6 col-sm-6">
                        <input id="confirm_password" name="confirm_password" type="password" placeholder="Re-type password" class="form-control input-md" required >
                    </div>
                    <div class="col-md-3 col-lg-3 col-sm-3" id = "cPasswordError"><?php echo $cPasswordError; ?></div>
                </div>
                <div class="form-group col-md-9 col-lg-8 col-sm-9">
                    <input type="submit" class="btn btn-md pull-right btn-success" name="signup" value="Sign Up">
                </div>
            </form>
        </div>

    </div>
</div>

<?php include_once "Template/footer.php"; ?>

