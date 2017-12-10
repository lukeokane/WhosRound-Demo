<?php
require_once("includes/configuration.php");
require_once("includes/session.php");
require_once("basketMethods.php");

$establishID = $_SESSION['establishmentID'];

echo $establishID;
// get establishment name by establishment ID
$queryEstablishName = 'SELECT name FROM establishments WHERE establishmentID = :establishID';
$stmt = $db->prepare($queryEstablishName);
$stmt->bindValue(':establishID', $establishID);
$stmt->execute();
$establishName = $stmt->fetch();
$stmt->closeCursor();

// set session establishment name
$_SESSION['establishment'] = filter_var($establishName['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

//*******************************************************************************************************************
//********************** need to only do categories for whatever pub was picked**************************************
//*******************************************************************************************************************
$queryDrinkCategories = 'SELECT dc.categoryName from drinkcategories dc, menuitems mi, drinks d '
                     . 'WHERE mi.establishmentID = :establishID AND mi.drinkID = d.drinkID AND '
                     . 'd.categoryID = dc.categoryID GROUP BY dc.categoryName';
$stmt1 = $db->prepare($queryDrinkCategories);
$stmt1->bindValue(':establishID', $establishID, PDO::PARAM_INT);
$stmt1->execute();
$results = $stmt1->fetchAll();
$stmt1->closeCursor();

$si = (isset($_SESSION["id"]) && isset($_SESSION["username"])) ? "true" : "false";
?>

<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">

        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <link rel="stylesheet" href="css/drinkCategory.css">

        <title>Who'sRound? - Drink Category Menu</title>

    </head>
    <body>
        <nav class="navbar fixed-top">
            <div style='padding: 0px; margin: 0px;' id="navbar-whole" class="container-fluid">
                <div style="padding: 0px;" id='top-navbar' class="col-md-12 column">
                    <table  style="padding-top: 0px; color:white; margin-bottom:0px;" class="table table-bordered table-hover" id="tab_logic">
                        <thead>
                            <tr>
                                <th style="vertical-align: middle;">
                                    <i style="font-size:30px;" class="fa fa-bars " aria-hidden="true"></i>
                                </th>
                                
                                <th id="profileSection" data-si="<?php echo $si; ?>" style='padding-left:0px; padding-right: 2px;' class="text-center">
                                    <i class="fa fa-user "  aria-hidden="true"></i>
                                    <div class=' light'>
                                        <?php
                                        if (isset($_SESSION['username'])) {
                                            echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'utf-8');
                                        } else {
                                            echo "Sign In";
                                        }
                                        ?>
                                    </div>
                                </th>
                                <th style=" padding-left:0px; padding-right: 0px; vertical-align: middle;" class="text-center bold">
                                    <?php echo htmlspecialchars($_SESSION['establishment'], ENT_QUOTES, 'utf-8'); ?>
                                </th>
                                <th style="text-align: right; vertical-align: middle;">
                                    <i style="font-size:30px;" class="fa fa-shopping-cart" aria-hidden="true"><span style=" font-size: 15px; vertical-align:top; background-color:#FA8E4B;" id="basketAmtIcon" class="badge"><?php echo quantityTotal(); ?></span></i>
                                </th>
                            </tr>
                        </thead>	
                    </table>
                </div>       
            </div>
            <div style='padding: 0px; margin: 0px;' id="navbar-whole" class="container-fluid">

                <table id='drink-navbar'   style=" padding-top: 0px; margin-bottom:0px;" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="max-width: 70px;" class="bold">
                                <a id="backbutton" ><i class="fa fa-chevron-left" aria-hidden="true"></i> Back</a>
                            </th>   
                            <th class="bold">
                                Categories<img style="height:30px; padding-left: 2%;" src="images/drinkicondarkgrey.png">
                            </th>                         
                        </tr>
                    </thead>	
                </table>
            </div>    

            <div style=' background-color: #bfbfbf; margin: 0px;' id="navbar-whole" class="container-fluid">
                <div class="input-group">
                    <span id="searchicon" style="background-color: transparent; border:none;" class="input-group-addon" id="basic-addon1"><i style="font-size:25px;" class="fa fa-search" aria-hidden="true"></i></span>
                    <input id="categorySearch" style=" font-size: 20px; background-color: transparent; border:none;" type="text" class="form-control" placeholder="Search" >
                    <span id="clearsearch" style="background-color: transparent; border:none;" class="input-group-addon"><i style="font-size:30px;" class="fa fa-times-circle-o" aria-hidden="true"></i></span>
                </div>
            </div>  
        </nav>

        <div id="listDiv" class="container">
       
                <?php foreach ($results as $categoryName): ?>

                    <?php
                    echo '<div class="element text-center" data-link="drinkMenu.php?drinkType=' . htmlspecialchars($categoryName["categoryName"], ENT_QUOTES, 'utf-8') . '">';
                    echo '<p>' . htmlspecialchars($categoryName["categoryName"], ENT_QUOTES, 'utf-8') . '</p></div>';
                    ?>

                <?php endforeach; ?>

            </div>
        
        <template id="beverages-template">
                    <div class="element text-center" data-link="drinkMenu.php?drinkType={{categoryName}}">
                        <p>{{categoryName}}</p></div>
                </template>


        <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
        <script type="text/javascript">var itemBasket = $.parseJSON('<?php echo json_encode($_SESSION["basket"]); ?>');</script>
        <script src="js/drinkCategory.js"></script>
        <script src="js/mustache.js"></script>
        <script src="js/showDrinks.js"></script>

        <script>

            $(document).ready(function () {
                $("[data-link]").click(function () {
                    window.location.href = $(this).attr("data-link");
                    return false;
                });
            });

        </script>

    </body>
</html>