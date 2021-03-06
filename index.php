<?php
session_start();
require_once 'validator.php';
$pass = $emailID = $message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validObj = new validator();
    $emailID = $validObj->sanitize($_POST['email']);
    $pass = $validObj->sanitize($_POST['pass']);

    require("config.php");
    $hashpass = hash("sha512", $pass);
    $sql = "SELECT id FROM a1_users WHERE emailID = '" . $emailID . "' and hashpass='" . $hashpass . "'";
    $result = mysqli_query($dbConn, $sql);

    $row = mysqli_fetch_assoc($result);

    if (mysqli_num_rows($result) > 0) {
        $sessionID = mysqli_real_escape_string($dbConn, session_id());
        $expires = time() + 60 * 30; //Session expires after 30 minutes
        $_SESSION['user_id'] = $row['id'];
        $sql = "INSERT INTO a1_active_users (user_id,session_id,expires) VALUES (" . (int)$row['id'] . ",'" . $sessionID . "'," . $expires . " )";
        $result = mysqli_query($dbConn, $sql);

        if ($result) {
            header("Location: home.php");
        } else {
            $message = "Login error. Report to test@gmail.com.<br /> Error: " . mysqli_error($dbConn);
        }
    } else {
        $message = "Login unsuccessful.  Incorrect email ID and/or password.";
    }
} else if (isset ($_GET['logout'])) {
    if ($_GET['logout'] === "true") {
        unset($_SESSION['user_id']);
        $message = "Logged out successfully";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link href="css/custom.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600" rel="stylesheet">
    <style>
        h1, h2, h3, h4, h5, h6, p, a, li, ul, label, input, span {
            font-family: 'Source Sans Pro', sans-serif;
            font-weight: 400;
        }

        /* Sticky footer styles
        -------------------------------------------------- */
        html {
            position: relative;
            min-height: 100%;
        }

        .footer {
            position: relative;
            bottom: 0;
            width: 100%;
            /* Set the fixed height of the footer here */
            height: 60px;
            background-color: #333;
            padding-top: 20px;
        }

    </style>
    <title>Online Quiz Portal</title>
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
    <h1 class="text-center">Online Quiz Portal</h1>
    <div class="page-header"></div>
    <div class="row">
        <div class="col-md-6"><br/>
            <h4 style="line-height:200%">WELCOME to our online quiz portal that helps you to undertake quiz
                on different web technologies. New users can sign up for first time and enjoy quizzing!<br/><br/>We also
                welcome experts to update our quiz database with your knowledge by adding new subjects and new
                questions. </h4>
        </div>
        <div class="col-md-5"><br/>
            <div class="panel panel-default">
                <div class="panel-heading text-center">
                    <span class="panel-title glyphicon glyphicon-user"></span>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" id="login-form" method="post"
                          action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" name="email" placeholder="Email Address"
                                   autofocus="autofocus" required="required"/> <br/>
                        </div>
                        <div class="col-sm-12">
                            <input type="password" name="pass" class="form-control" placeholder="Password"
                                   required="required"/><br/>
                        </div>
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-primary btn-block">Sign in</button>
                        </div>
                    </form>
                </div>
                <div class="panel-footer">
                    <div class="btn-group btn-group-justified">
                        <a href="forgotPwd.php" class="btn btn-default"><span class="glyphicon glyphicon-lock"></span>
                            Forgot Password?</a>
                        <a href="register.php" class="btn btn-default"><span class="glyphicon glyphicon-check"></span>
                            Sign up</a>
                    </div>
                </div>
            </div>
            <div class="well">
                Demo Email/Password: test@gmail.com/test123
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="text-center text-error"><?php echo $message; ?></div>
    </div>
</div>

<footer class="footer" style="margin-top: 45px">
    <div class="container">
        <p class="text-muted" style="color: #ffffff;">Copyright &copy; <a href="http://riteshhota2008.github.io"
                                                                          style="color: gold">Ritesh Hota</a>. All
            Rights Reserved.</p>
    </div>
</footer>

</body>
</html>
