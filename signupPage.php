<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        
        <link rel="stylesheet" href="css/signup.css">

        <title>Who'sRound? - Sign Up</title>
    </head>
    <body>
        <nav class="navbar navbar-default" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">		    
                    <div id="navbar-centre"class="navbar-brand bold navbar-brand-centered">Sign Up</div>
                    <div id="navbar-backbutton" class="navbar-brand  navbar-brand-left">
         
                        <span class="glyphicon glyphicon-chevron-left"></span>Back
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </nav>
        <div class="container">
            <div class="row main">

                <div style="padding-top:0px;"class="main-login main-center">
                    <h3 id="sign_up_alerts" style="margin-bottom:1em;"></h3>
                    <form id="signup_form" class="form-horizontal" method="post" action="">

                        <div class="col-md-6 mx-auto">
                            <div class="form-group">
                                <input style="z-index: 3000;" type="text" id="name" name="customerUsername" class="form-control" required >
                                <label style="z-index: 0;" class="form-control-placeholder light" for="name">Username (8-20 characters)</label>
                            </div>

                            <div class="form-group">
                                <input type="text" id="email" name="customerEmail" class="form-control" required >
                                <label class="form-control-placeholder light" for="email">Email</label>
                            </div> 
                            <div class="form-group">
                                <input type="password" id="password" name="customerPassword1" class="form-control" required>
                                <label class="form-control-placeholder light" for="password">Password</label>
                            </div>

                            <div class="form-group">
                                <input type="password" id="passwordAgain" name="customerPassword2" class="form-control" required >
                                <label class="form-control-placeholder light" for="passwordAgain">Password Again</label>
                            </div>
														<p class="bold">Password Requirements: <br>8+ characters, at least 1 capital letter and number</p>
                                                                                                                      
                        </div>

                        <a href="#" id="signupbutton" class="btn btn-lg btn-block  center-block">Sign Up</a>
                        
                        <a href="signinPage.php" id="cancelbutton" class="btn btn-lg btn-block  center-block">Sign In</a>
                    </form>
                </div>
            </div>
        </div>

        <script>
            
            $('#navbar-backbutton').click(function() {
            window.location=document.referrer;
        });
        
        </script>
        
        <script src="js/signuppage.js" type="text/javascript"></script>
        
    </body>
</html>