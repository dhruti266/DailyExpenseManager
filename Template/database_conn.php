<?php

// important to fetch
// existing session data
session_start();

// singleton is used to create object
// only for one time and can be used
// many times anywhere in the whole project.
class Connection{
    static $connection;
    public static function get(){
        try {
            if (!self::$connection) { // 'self keyword is used to use ststic variables or properties in the scope of the class.
                self::$connection = new PDO('mysql:host=127.0.0.1;dbname=f6team11_dem', 'f6team11', 'f6@team11');
            }
        }
        catch(Excception $ex){
            echo $ex->getMessage();
        }
        return self::$connection;
    }
}

// user class to
// manage all user
// related functions
class User {

    /**
     * function checks to see
     * if user exists in the database
     * if it does it will return that user
     *
     * @param $user
     * @param $pass
     * @return mixed
     */
    public static function checkIfUserExists($user, $pass=null)
    {
        $query = "SELECT * FROM user_information WHERE username=:username";
        $query = $pass ? $query. " AND password=:pass" : $query;

        // prepare() function is used to bind any user-input, do not include the user-input directly in the query.
        // To use static method or function of the class from outside the class just use class name instead of creating an object.
        $stmt = Connection::get()->prepare($query);

        // clean userdata to prevent from hacking of database
        $stmt->bindParam(':username', $user);
        if($pass) $stmt->bindParam(':pass', $pass);

        // have to have it to execute prepare statement.
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC); // without any loop fetch() will return single record of the table as a single dimentional array.
    }

    public static function isUserLoggedIn(){
        return isset($_SESSION['username']);
    }

    public static function getUserInfo(){
        return $_SESSION['username'];
    }
    public static function logOut(){
        session_destroy();
        header("Location:index.php");
        exit();
    }

    public static function checkIfUserWithPinExists($user, $pin){

        // prepare() function is used to bind any user-input, do not include the user-input directly in the query.
        // To use static method or function of the class from outside the class just use class name instead of creating an object.
        $stmt = Connection::get()->prepare("SELECT * FROM user_information WHERE username=:username AND pin=:pin");

        // clean userdata to prevent from hacking of database
        $stmt->bindParam(':username', $user);
        $stmt->bindParam(':pin', $pin);

        // have to have it to execute prepare statement.
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
