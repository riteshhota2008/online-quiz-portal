<?php
include('config.php');
include('forget-password/function.php');

if (isset($_POST['submit'])) {
    $emailID = $_POST['email'];
    $emailID = mysqli_real_escape_string($dbConn, $emailID);

    if (checkUser($emailID) == "true") {
        $userID = UserID($emailID);
        $token = generateRandomString();

        $query = mysqli_query($dbConn, "INSERT INTO recovery_keys (userID, token) VALUES ($userID, '$token') ");
        if ($query) {
            $send_mail = send_mail($emailID, $token);

            if ($send_mail === 'success') {
                $msg = 'A mail with recovery instruction has sent to your email.';
                $msgclass = 'bg-success';
            } else {
                $msg = 'There is something wrong.';
                $msgclass = 'bg-danger';
            }

        } else {
            $msg = 'There is something wrong.';
            $msgclass = 'bg-danger';
        }
    } else {
        $msg = "This email doesn't exist in our database.";
        $msgclass = 'bg-danger';
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
    <title>Forgot Password</title>
    <style>
        h1, h2, h3, h4, h5, h6, p, a, li, ul, label, input, span {
            font-family: 'Source Sans Pro', sans-serif;
            font-weight: 400;
        }
    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

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
                <li class="active"><a href="http://riteshhota.16mb.com">Home <span class="sr-only">(current)</span></a>
                </li>
                <li><a href="http://riteshhota.16mb.com">Login</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

<div class="container">
    <div class="row">
        <div class="col-lg-4 col-lg-offset-4">
            <form class="form-horizontal" role="form" method="post" style="margin-top: 50px">
                <h2>Forgot Password</h2>

                <?php if (isset($msg)) { ?>
                    <div class="<?php echo $msgclass; ?>" style="padding:5px;"><?php echo $msg; ?></div>
                <?php } ?>

                <p>
                    Forgot your password? No problem, we will fix it. Just type your email below and we will send you
                    password recovery instruction to your email. Follow easy steps to get back to your account.
                </p>

                <div class="row">
                    <div class="col-lg-12">
                        <label class="control-label">Your Email</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <input class="form-control" name="email" type="email" placeholder="Enter your email here..."
                               required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <button class="btn btn-success btn-block" name="submit" style="margin-top:8px;">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="js/jquery-1.11.3.min.js"></script>

<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.js"></script>
</body>
</html>
