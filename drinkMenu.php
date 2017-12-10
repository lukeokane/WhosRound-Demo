<?php
require_once("includes/session.php");
require_once("includes/configuration.php");
require_once("basketMethods.php");

$drinkType = filter_input(INPUT_GET, "drinkType", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$establishID = $_SESSION['establishmentID'];

$queryCategoryID = 'SELECT categoryID FROM drinkcategories WHERE categoryName = :drinkType';
$stmt1 = $db->prepare($queryCategoryID);
$stmt1->bindValue(':drinkType', $drinkType);
$stmt1->execute();
$result = $stmt1->fetch();
$stmt1->closeCursor();

$categoryID = filter_var($result['categoryID'], FILTER_VALIDATE_INT);

$queryEstablishmentDrinkType = 'SELECT d.drinkID, name, alcoholPercentage, calories, img '
        . 'FROM drinks d, drinkcategories dc, menuitems mi '
        . 'WHERE mi.establishmentID = :establishID '
        . 'AND dc.categoryID = :categoryID '
        . 'AND d.categoryID = :categoryID '
        . 'GROUP BY d.drinkID, name, alcoholPercentage, calories';

//$queryEstablishmentDrinkType = 'SELECT drinkID, name, alcoholPercentage, calories, categoryName '
//        . 'FROM drinks d, drinkcategories dc, menuItems mi '
//        . 'WHERE d.drinkID = mi.drinkID '
//        . 'AND mi.establishmentID = :establishID '
//        . 'AND dc.categoryID = :categoryID '
//        . 'AND d.categoryID = :categoryID';
//. 'GROUP BY drinkID, name, alcoholPercentage, calories, categoryName';
$stmt2 = $db->prepare($queryEstablishmentDrinkType);
$stmt2->bindValue(':establishID', $establishID);
$stmt2->bindValue(':categoryID', $categoryID);
$stmt2->execute();
$results = $stmt2->fetchAll();
$stmt2->closeCursor();

$si = (isset($_SESSION["id"]) && isset($_SESSION["username"])) ? "true" : "false";
?>

<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <link rel="stylesheet" href="css/drinkMenu.css">
        <title>Who'sRound? - Drink Menu</title>

    </head>
    <body>
        <!-- Modal -->
        <div class="modal fade" id="selectItem" tabindex="-1" role="dialog" aria-labelledby="selectItem" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-5 drinkImageDiv"><img class="drinkSelectedImage" src=""/></div>
                        <div class="col-7 drinkInfo">
                            <div class="row bold drinkName"></div>
                            <div class="row drinkPercentage"></div>
                        </div>
                    </div>
                    <div class="itemMeasurements  modal-body" data-test="1">

                    </div>
                    <div class="modal-footer">
                        <div class="col">
                            <div class="total row">
                                <div class="col-6 text-right">
                                    Total
                                </div>
                                <div id="basketTot" class="col-6 bold float-left">
                                    &euro;0.00
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 addToBasket text-center">
                                    Add to Basket?
                                </div>                              
                            </div>
                            <div class="row">
                                <div class="col-6 cancelAdd text-center">
                                    <i class="fa fa-times-circle"  aria-hidden="true"></i>
                                </div>     
                                <div class="col-6 addToBasket text-center">
                                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                                </div>    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <nav class="navbar fixed-top">
            <div style='padding: 0px; margin: 0px;' id="navbar-whole" class="container-fluid">
                <div style="padding: 0px;" id='top-navbar' class="col-md-12 column">
                    <table  style="padding-top: 0px; color:white; margin-bottom:0px;" class="table table-bordered table-hover" id="tab_logic">
                        <thead>
                            <tr>
                                <th style="vertical-align: middle;">
                                    <i style="font-size:30px;" class="fa fa-bars " aria-hidden="true"></i>
                                </th>
                                <th id="profileSection" data-si="<?php echo $si; ?>" data-drinktype="<?php echo htmlspecialchars($drinkType, ENT_QUOTES, 'utf-8'); ?>" style='padding-left:0px; padding-right: 2px;' class="text-center">
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
<?php echo htmlspecialchars($drinkType, ENT_QUOTES, 'utf-8'); ?> <img style="height:30px; padding-left: 2%;" src="images/drinkicondarkgrey.png">
                            </th>                         
                        </tr>
                    </thead>	
                </table>
            </div>    

            <div style=' background-color: #bfbfbf; margin: 0px;' id="navbar-whole" class="container-fluid">
                <div class="input-group">
                    <span id="searchicon" style="background-color: transparent; border:none;" class="input-group-addon" id="basic-addon1"><i style="font-size:25px;" class="fa fa-search" aria-hidden="true"></i></span>
                    <input id="search" data-drinktypeid="<?php echo $categoryID; ?>"style=" font-size: 20px; background-color: transparent; border:none;" type="text" class="form-control" placeholder="Search for a drink..." >
                    <span id="clearsearch" style="background-color: transparent; border:none;" class="input-group-addon"><i style="font-size:30px;" class="fa fa-times-circle-o" aria-hidden="true"></i></span>
                </div>
            </div>    

        </nav>

        <div id="listDiv" class="container-fluid">
<?php foreach ($results as $drink): ?>
    <?php
    echo '<div class="container">';
    echo '<div class="element" data-drinkid="' . htmlspecialchars($drink['drinkID'], ENT_QUOTES, 'utf-8') . '"  data-drinkname="' . htmlspecialchars($drink['name'], ENT_QUOTES, 'utf-8') . '" data-alcoholpercentage="' . htmlspecialchars($drink['alcoholPercentage'], ENT_QUOTES, 'utf-8') . '">';
    echo '<div class="img-div text-center">';
    echo '<img src="images/' . htmlspecialchars($drink['img'], ENT_QUOTES, 'utf-8') . '"/>';
    echo '</div>';
    echo '<p>' . htmlspecialchars($drink['name'], ENT_QUOTES, 'utf-8') . '</p>';
    echo '</div>';
    echo '</div>';
    ?>
            <?php endforeach; ?>
        </div>

        <div id="previewOrder" data-link="basket.php" style="padding: 0px;" class="navbar fixed-bottom">
            <div id='previewOrder' style='padding: 0px; margin: 0px;' class="container-fluid">
                <table id='drink-navbar'   style="background-color: #424C58; color: #ffffff; padding-top: 0px; margin-bottom:0px;" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center bold">
                                Preview Order
                            </th>                         
                        </tr>
                    </thead>	
                </table>
            </div>    
        </div>

        <template id="modalRowTemplate">
            <div class="measurementInfo" data-drinkid="{{drinkID}}" data-drinktypeid="{{drinkTypeID}}" data-price="{{price}}">
                <div class="measureInfo row">
                    <div class="col-7 bold text-center measureType">{{typeName}}</div>
                    <div class="col-5  measurePrice">&euro;{{price}}</div>
                </div>
                <div class="col-centered measureQuantity">
                    <div class="input-group">
                        <span class="input-group-addon  minusIcon"><i class="fa fa-minus" aria-hidden="true"></i></span>
                        <input type="number" value="{{quantity}}" class="form-control quantityInput" >
                        <span class="input-group-addon addIcon"><i class="fa fa-plus" aria-hidden="true"></i></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col measureSpecInstr">
                        <input class="form-control specInstr input-sm" placeholder="Add special instructions here..." id="inputsm" type="text">
                    </div>
                </div>
            </div>
        </template>

        <template id="drinkTemplate">
            <div class="container">
                <div class="element" data-drinkid="{{drinkID}}"  data-drinkname="{{name}}" data-alcoholpercentage="{{alcoholPercentage}}">
                    <div class="img-div text-center">
                        <img src="images/{{img}}"/>
                    </div>
                    <p>{{name}}</p>
                </div>                      
            </div>
        </template>


        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script src="js/mustache.js"></script>
        <script type="text/javascript">var itemBasket = $.parseJSON('<?php echo json_encode($_SESSION["basket"]); ?>');</script>
        <script src="js/drinkMenu.js"></script>
        <script src="https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>            
    </body>
</html>