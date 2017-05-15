<?php 
/** 
* The controller for the Currency Converter program, obtains data from the user and sends it to the model for processing.
* Also contains the HTML view
*
* Author: Elizabeth Avery
*/
    require_once('FxDataModel.php');
    require_once('LoginDataModel.php');    
    require_once('EmailModel.php');
             
    $fxDataModel;
    $username;  
    $emailModel;
    
    if (!isset($_SESSION)){
        session_start();
    }    
    if (!isset($_SESSION[LoginDataModel::USER_KEY])){
        include('login.php');            
        exit();
    }
    else{
        $username = $_SESSION[LoginDataModel::USER_KEY];
    }
    if (isset($_SESSION[FxDataModel::FX_DATA_MODEL_KEY])){            
        $fxDataModel = unserialize($_SESSION[FxDataModel::FX_DATA_MODEL_KEY]);
    }
    else {
        $fxDataModel = new FxDataModel();
        $_SESSION[FxDataModel::FX_DATA_MODEL_KEY] = serialize($fxDataModel);
    }                   
    
    $iniArray = $fxDataModel->getIniArray();
    $currencies = $fxDataModel->getFxCurrencies();
    $source_currency = $currencies[0];
    $destination_currency = $currencies[0];
    $error_message = "";        
    $starting_amount = "";
    $converted_amount = "";
    $address = "";
    $email_msg = "";

    if (array_key_exists(filter_input(INPUT_POST,$iniArray[FxDataModel::SRC_AMT_KEY]))){    

        $source_currency = filter_input(INPUT_POST,$iniArray[FxDataModel::SRC_CUR_KEY]);
        $destination_currency = filter_input(INPUT_POST,$iniArray[FxDataModel::DEST_CUR_KEY]);
        $starting_amount = filter_input(INPUT_POST,$iniArray[FxDataModel::SRC_AMT_KEY]);
        

        if ($source_currency === $destination_currency){
            $error_message = 'Source currency and destination currency are the same.';
            $source_currency = $currencies[0];
            $destination_currency = $currencies[0];
            $starting_amount= "";
            $converted_amount= "";
        }
        else if ($starting_amount === FALSE){
            $error_message = 'Starting amount must be a valid number.';
            $source_currency = $currencies[0];
            $destination_currency = $currencies[0];
            $starting_amount= "";
            $converted_amount= "";
        }
        else if ($starting_amount <= 0){
            $error_message = 'Starting amount must be greater than zero.';
            $source_currency = $currencies[0];
            $destination_currency = $currencies[0];
            $starting_amount= "";
            $converted_amount="";
        }
        else {
            $error_message = '';            
            $converted_amount = sprintf("%.2f", $starting_amount * $fxDataModel->getFxRate($source_currency, $destination_currency));
            $len = strlen(trim($address));
            if ($len != 0){
                $valid = EmailModel::validateEmailAddr($address);
                if ($valid){                     
                    $emailModel = new EmailModel();                    
                    $body = $starting_amount . $source_currency . " = " . $converted_amount . $destination_currency;                    
                    $sent = $emailModel->sendMail($address, $body);                    
                    if ($sent > 0){
                        $email_msg = "An error occurred when trying to email results to " . $address . ":<br>" . htmlspecialchars($sent);
                    }
                    else{
                        $email_msg = "Results are being emailed to " . $address . ".";
                    }
                }
                else {
                    $email_msg = $address . " is an invalid email address. Results could not be emailed.";
                }
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
        <?php if (!empty($error_message)){ ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php } ?>
            <p>Welcome <?php echo $username; ?></p>
        <form name="fxCalc" action="fxCalc.php" method="post">        
            <div>
                <select name="<?php echo $iniArray[FxDataModel::SRC_CUR_KEY] ?>">
                    <?php                     
                    foreach($currencies as $currency) { ?>
                    <option value="<?php echo $currency; ?>" <?php if ($currency === $source_currency) {echo ' selected="selected"';} ?>><?php echo $currency; ?></option>
                    <?php
                        }
                        
                    ?>                    
                </select>
                <input type="text" name="<?php echo $iniArray[FxDataModel::SRC_AMT_KEY] ?>" value="<?php echo $starting_amount; ?>"/>
                <select name="<?php echo $iniArray[FxDataModel::DEST_CUR_KEY] ?>">            
                    <?php                     
                    foreach($currencies as $currency) { ?>
                    <option value="<?php echo $currency; ?>" <?php if ($currency === $destination_currency) {echo ' selected="selected"';}?>><?php echo $currency; ?></option>
                    <?php
                        }
                        
                    ?>   
                    
                </select>
                <input type="text" name="<?php echo $iniArray[FxDataModel::DEST_AMT_KEY] ?>" disabled="disabled" 
                       value="<?php echo $converted_amount; ?>"/>                
            </div>
            <div>
                <label>Email address: </label><input type="text" name="<?php echo $iniArray[FxDataModel::EMAIL_KEY] ?>" value="<?php echo $address; ?>"/>
            
                <?php if (!empty($email_msg)){ ?>
                    <p><?php echo $email_msg; ?></p>
                <?php } ?>
            </div>        
            <div>
                <input type="submit" value="Convert">
                <input type="reset" value="Reset">
            </div>
            
        </form>
    </body>

</html>
