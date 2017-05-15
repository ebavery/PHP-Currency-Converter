<?php
/** 
*   Controller for the login screen of the Currency Converter, obtains data from user and send to model for processing.
*   HTML View is at the end
*/

require_once('LoginDataModel.php');
$login = new LoginDataModel();
$loginArray = $login->getLoginArray();

$error_msg = "";
$username = "";
$password = "";

if (array_key_exists(filter_input(INPUT_POST, $loginArray[LoginDataModel::USER_KEY]))){
    if (!(filter_input(INPUT_POST,$loginArray[LoginDataModel::USER_KEY]))){
        $error_msg = "Please enter a username and password";
        $username = "";
        $password = "";
    }
    else if (!(filter_input(INPUT_POST,$loginArray[LoginDataModel::PWD_KEY]))){
        $error_msg = "Please enter a username and password";
        $username = "";
        $password = "";
    }    
    else { 
        $username = filter_input(INPUT_POST,$loginArray[LoginDataModel::USER_KEY]);
        $password = filter_input(INPUT_POST,$loginArray[LoginDataModel::PWD_KEY]);
        
        if ($login->validateUser($username, $password)){
            session_start();
            $_SESSION[LoginDataModel::USER_KEY] = $username;
            include('fxCalc.php');
            exit();
        }
        else{
            $error_msg = "Invalid username or password";
            $username = "";
            $password = "";
        }
    }
}
?>
<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Money Banks F/X Calculator</title>
        <link href="fxCalc.css" rel="stylesheet" type="text/css"/>
        
    </head>
    <body>
        <header>
            <h1>Money Banks F/X Calculator</h1>
            <hr />
        </header>
        <?php if (!empty($error_msg)){ ?>
            <p class="error"><?php echo $error_msg; ?></p>
        <?php } ?>
        <form name="login" action="login.php" method="post">  
            <div>
                <label>Username: </label><input type="text" name="<?php echo $loginArray[LoginDataModel::USER_KEY] ?>"/><br /><br />
                <label>Password: </label><input type='password' name='<?php echo $loginArray[LoginDataModel::PWD_KEY] ?>'/>
            </div>
            <div>
                <input type="submit" value="Login">
                <input type="reset" value="Reset">
            </div>
        </form>
           
    </body>
</html>
