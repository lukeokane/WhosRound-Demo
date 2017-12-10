<?php
require_once("../includes/configuration.php");
require_once("../includes/session.php");

// setting session variables here temporarily
$_SESSION['staffEstablishment'] = "Ridleys";
$_SESSION['staffEstablishmentID'] = 1;
$_SESSION['staffUsername'] = "Billy";
//
$establishID = filter_var($_SESSION['staffEstablishmentID'], FILTER_SANITIZE_FULL_SPECIAL_CHARS); // get from session when pub picker is setup
$establishName = filter_var($_SESSION['staffEstablishment'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$staffUsername = filter_var($_SESSION['staffUsername'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
//
//// get all orders for current establishment
//$queryOrders = 'SELECT * FROM orders '
//        . 'WHERE establishmentID = :establishID '
//        . 'ORDER BY orderedAt DESC';
//$stmt = $db->prepare($queryOrders);
//$stmt->bindValue(':establishID', $establishID);
//$stmt->execute();
//$orders = $stmt->fetchAll();
//$stmt->closeCursor();
//$result = "";
?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="manifest" href="../json/manifest.json">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">

        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <link rel="stylesheet" href="../css/orders.css">

        <title>Who'sRound? - Bartender - Orders</title>

    </head>

    <body>

        <nav class="navbar navbar-default  navbar-fixed-top  " role="navigation">
            <div class="container3">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">		                 
                    <div style="font-size: 30px;  padding-top: 10px; " id="backbutton" class="navbar-brand navbar-brand-left">
                        <span class="glyphicon glyphicon glyphicon-user"></span> <?php echo htmlspecialchars($staffUsername, ENT_QUOTES, 'utf-8') ?></div>

                    <div style="font-size: 30px; margin-left: -4em; margin-top: 1%" id="navbar-centre" 
                         class="navbar-brand bold navbar-brand-centered">
                             <?php echo htmlspecialchars($establishName, ENT_QUOTES, 'utf-8') ?>
                    </div>

                </div>
            </div><!-- /.container-fluid -->
        </nav>

        <nav style=" top: 50px;" class="navbar navbar-default headersNav  navbar-fixed-top  " role="navigation">
            <div class="orderContainer orderHeaders">
                <div style="font-size: 30px;" class="col-sm-12">
                    <div style="text-align: center;" class="col-sm-4">Name</div>
                    <div style="text-align:center;" class="col-sm-2">Order</div>
                    <div style="text-align:center;" class="col-sm-4">Collection/Table</div>
                    <div style="padding-left: 1em;" class="text-center col-sm-2">View</div>
                </div>
            </div> <!-- /.container-fluid -->
        </nav>

        <div style=" margin-top: 22%; margin-bottom: 5%;" class="container" scrollable>

         

        </div>

        <div class="footer">
            <div style="font-size: 30px;" id="backbutton" class="navbar-brand navbar-brand-left">
                <span class="glyphicon glyphicon-chevron-left"></span>Back
            </div>
        </div>
        
              

        <script>       
            function ajaxCall() {
                $.ajax({
                    url: "newOrders.php",
                    success: (function (result) {
                         $(".container").empty();
                        $(".container").html(result);
                    })
                });
                
            };

            ajaxCall(); 
            setInterval(ajaxCall, (10000));
            
        </script>

    </body>
</html>