<?php
require_once 'includes/session.php';
require_once 'includes/configuration.php';

// Check if already signed in...
// no point signing in again if signed in.
if (isset($_SESSION["id"]) && isset($_SESSION["username"]))
{
    header('Location: index.php');
    die();
}

// Check if redirected from another page...
// ... that wants you to go to a certain page...
// ... when signed in.

$nsiRelocation = filter_input(INPUT_POST, "nsiRelocation", FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (isset($nsiRelocation))
{
    $_SESSION['nsiRelocation'] = $nsiRelocation;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">

        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <link rel="stylesheet" href="css/signin.css">

        <title>Who'sRound? - Sign In</title>
    </head>
    <body>
        
        <nav class="navbar navbar-default" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">		    
                    <div id="navbar-centre"class="navbar-brand bold navbar-brand-centered">Sign In</div>
                    <div id="navbar-backbutton" class="navbar-brand navbar-brand-left">
                        <span class="glyphicon glyphicon-chevron-left"></span>Back</div>
                </div>
            </div><!-- /.container-fluid -->
        </nav>
        
        <div class="container">
            <div class="row main">

                <div class="main-login main-center">
                    <h3 id="sign_in_alerts"></h3>
                    <form id="signin_form" class="form-horizontal" method="post" action="#">

                        <div class="col-md-6 mx-auto">
                            <div class="form-group">
<!--                                username input           -->
                                <input type="text" id="name" name="customerUsername" class="form-control" required>
                                <label class="form-control-placeholder light" for="name">Username</label>                               
                            </div>

                            <div class="form-group">
<!--                                password input-->
                                <input type="password" id="password" name="customerPassword" class="form-control" required>
                                <label class="form-control-placeholder light" for="password">Password</label>
                            </div>

                            <div style="color: #ffffff" class="form-group">
                                <div class="checkbox">
                                    <label><input type="checkbox" value="">Remember me</label>
                                    <span class="pull-right">Forgotten Password</span>
                                </div>
                            </div>        

                        </div>

                        <a href="#" id="signinbutton" class="btn  btn-lg btn-block  center-block">Sign In</a>
                        <p class="center-block text-center" style="color: #B3B7BC;">If you don't have an account, sign up.</p>   
                        <a href="signupPage.php" id="signupbutton" class="btn btn-lg btn-block  center-block">Sign Up</a>
                    </form>
                </div>
            </div>
        </div>
        
        <script>
      
            $('#navbar-backbutton').click(function() {
            window.location=document.referrer;
        });
        
        </script>
        
        <script src="js/signinpage.js" type="text/javascript"></script>

    </body>
    
</html>