<?php
/** View that shows the error to the user */

require_once('IError.php');
;
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
            <h1 class="error">Money Banks Error</h1>
            <hr />
            <p>Sorry, an exception has occured. </p>
            <p>To continue, click the Back button.</p>
             <h2>Details</h2>
            <p>Message: <?php
                echo $_GET[IError::ERR_MSG_KEY]; ?></p>            
        </header>       
    </body>
</html>
