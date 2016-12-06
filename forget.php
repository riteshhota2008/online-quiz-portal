<?php

include('config.php');
include('forget-password/function.php');

$emailID = $_GET['email'];
$token = $_GET['token'];

$userID = UserID($emailID);

$verifytoken = verifytoken($userID, $token);


if (isset($_POST['submit'])) {
    $new_password = $_POST['new_password'];
    //$new_password = md5($new_password);

    $new_password = hash("sha512", $new_password);

    $retype_password = $_POST['retype_password'];
    //$retype_password = md5($retype_password);

    $retype_password = hash("sha512", $retype_password);

    if ($new_password == $retype_password) {
        $update_password = mysqli_query($dbConn, "UPDATE a1_users SET hashpass = '$new_password' WHERE ID = $userID");
        if ($update_password) {
            mysqli_query($dbConn, "UPDATE recovery_keys SET valid = 0 WHERE userID = $userID AND token ='$token'");
            $msg = 'Your password has changed successfully. Please login with your new passowrd.';
            $msgclass = 'bg-success';
        }
    } else {
        $msg = "Password doesn't match";
        $msgclass = 'bg-danger';
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/custom.css"/>
    <title>Reset Password</title>
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

<div class="container" style="margin-top: 50px">
    <div class="row">
        <?php if ($verifytoken == 1) { ?>
            <div class="col-lg-4 col-lg-offset-4">
                <form class="form-horizontal" role="form" method="post">
                    <h2>Reset Your Password</h2>

                    <?php if (isset($msg)) { ?>
                        <div class="<?php echo $msgclass; ?>" style="padding:5px;"><?php echo $msg; ?></div>
                    <?php } ?>

                    <div class="row">
                        <div class="col-lg-12">
                            <label class="control-label">New Password</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <input class="form-control" name="new_password" type="password" placeholder="New Password"
                                   required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <label class="control-label">Re-type New Password</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <input class="form-control" name="retype_password" type="password"
                                   placeholder="Re-type New Password" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <button class="btn btn-success btn-block" name="submit" style="margin-top:8px;">Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        <?php } else { ?>
            <div class="col-lg-4 col-lg-offset-4">
                <h2>Invalid or Broken Token</h2>
                <p>Opps! The link you have come with is maybe broken or already used. Please make sure that you copied
                    the link correctly or request another token from <a href="index.php">here</a>.</p>
            </div>
        <?php } ?>
    </div>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="js/jquery-1.11.3.min.js"></script>

<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap.js"></script>
</body>
</html>
