<?php
session_start();
require_once 'validator.php';

$siteKey = '';//Add your Recaptcha Site Key

if ($_SERVER["REQUEST_METHOD"] == "POST"):

    if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])):
        //your site secret key
        $secret = '';//Add your Recaptcha Secret Key
        //get verify response data
        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
        $responseData = json_decode($verifyResponse);
        if ($responseData->success):

            $name = $pass = $emailID = $cpass = $message = '';
            $nameErr = $emailErr = $passErr = $cpassErr = '';
            $error = 0;

            $validObj = new validator();
            //validate name
            $name = $validObj->sanitize((isset($_POST["name"])) ? $_POST["name"] : " ");
            if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
                $nameErr = "Only letters and white space allowed";
                $error = 1;
            }

            //validate email ID
            $emailID = $validObj->sanitize((isset($_POST["email"])) ? $_POST["email"] : "");
            if (!filter_var($emailID, FILTER_VALIDATE_EMAIL)) {
                $emailErr = "Invalid email ID format";
                $error = 1;
            }

            //validate password
            $pass = $validObj->sanitize((isset($_POST["pass"])) ? $_POST["pass"] : "");
            if (strlen($pass) < 7) {
                $passErr = "Password must be atleast 6 characters";
                $error = 1;
            }

            //validate confirm password
            $cpass = $validObj->sanitize((isset($_POST["conpass"])) ? $_POST["conpass"] : "");
            if (strcmp($pass, $cpass) !== 0) {
                $cpassErr = "Password and Confirm password do not match";
                $error = 1;
            }

            //SHA-512  algorithm to convert password into 256 bit
            if ($error == 0) {
                require("config.php");
                $hashpass = hash("sha512", $pass);
                $sql = "INSERT INTO a1_users (name,emailID,hashpass) VALUES('" . $name . "','" . $emailID . "','" . $hashpass . "')";
                $result = mysqli_query($dbConn, $sql);

                if ($result) {
                    //Get the user id
                    $sql = "SELECT LAST_INSERT_ID() as id";
                    $result = mysqli_query($dbConn, $sql);
                    $row = mysqli_fetch_row($result);
                    $_SESSION['user_id'] = $row[0];

                    //set time to expire the session if there is no action for a predefined time
                    $sessionID = mysqli_real_escape_string($dbConn, session_id());
                    $expires = time() + 60 * 30; //Session expires after 30 minutes
                    $sql = "INSERT INTO a1_active_users (user_id,session_id,expires) VALUES (" . (int)$_SESSION['user_id'] . ",'" . $sessionID . "'," . $expires . " )";
                    $result = mysqli_query($dbConn, $sql);

                    if ($result) {
                        header("Location: home.php");
                    } else {
                        $message = "Registration error.  Report to test@gmail.com<br />.Error: " . mysqli_error($dbConn);
                    }

                } else if (mysqli_errno($dbConn) == 1062) {
                    $message = "Registration was unsuccessful. " . $emailID . " is already a registered user.  ";
                } else {
                    $message = "Registration was unsuccessful.  Report to test@gmail.com <br /> Error: " . mysqli_error($dbConn) . "<br /> Error No: " . mysqli_errno($dbConn);
                }
            }
            $succMsg = 'Your contact request have submitted successfully.';
        else:
            $errMsg = 'Robot verification failed, please try again.';
        endif;
    else:
        $errMsg = 'Please click on the reCAPTCHA box.';
    endif;
else:
    $errMsg = '';
    $succMsg = '';
endif;
?>
<html lang="en">
<head>
    <title>User Registration</title>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link href="css/custom.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600" rel="stylesheet">
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <style>
        h1, h2, h3, h4, h5, h6, p, a, li, ul, label, input, span {
            font-family: 'Source Sans Pro', sans-serif;
            font-weight: 400;
        }
    </style>

</head>
<body>

<nav class="navbar navbar-inverse" style="border-radius: 0px">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">QUIZ</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="active"><a href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li><a href="index.php">Login</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

<div class="container">
    <h2 class="text-center">Online Quiz Portal</h2><br>
    <div class="row">
        <div class="col-xs-12 col-sm-offset-4 col-sm-4 col-sm-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title text-center">Registration</h2>
                </div>
                <div class="panel-body">
                    <form role="form" class="form-horizontal" id="register-form" method="post"
                          action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>>
                        <div class="col-sm-12">
                            <input type="text" name="name" class="form-control" autofocus="autofocus"
                                   required="required" <?php if (isset($_POST["name"])) {
                                echo 'value="' . $name . '"';
                            } else {
                                echo "placeholder='Full Name'";
                            } ?>/>
                            <span class="text-error"><small><?php echo $nameErr ?></small></span><br/>
                        </div>
                        <div class="col-sm-12">
                            <input type="text" name="email" class="form-control"
                                   required="required" <?php if (isset($_POST["email"])) {
                                echo 'value="' . $emailID . '"';
                            } else {
                                echo "placeholder='Email Address'";
                            } ?>/>
                            <span class="text-error"><small><?php echo $emailErr ?></small></span><br/>
                        </div>
                        <div class="col-sm-12">
                            <input type="password" name="pass" class="form-control" required="required"
                                   placeholder="Password: atleast 6 characters"/>
                            <span class="text-error"><small><?php echo $passErr ?></small></span><br/>
                        </div>
                        <div class="col-sm-12">
                            <input type="password" name="conpass" class="form-control" required="required"
                                   placeholder="Re-enter password"/>
                            <span class="text-error"><small><?php echo $cpassErr ?></small></span><br/>
                        </div>
                        <div class="col-sm-12">
                            <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
                            <br>
                        </div>
                        <div class="col-sm-12">
                            <button class="btn btn-success btn-block" type="submit">Register</button>
                            <br/>
                        </div>
                        <div class="col-sm-12">
                            <a class="pull-right" href="index.php">Already a member?</a><br/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="text-error text-center"><h3><?php echo $message ?><?php echo $errMsg ?></h3></div>
    </div>
</div>

</body>
</html>