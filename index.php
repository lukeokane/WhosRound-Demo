<?php
require_once("includes/configuration.php");
require_once("includes/session.php");
require_once("basketMethods.php");

// Person is redirected to the page on successful login...
// will check if nsiRelocation variable is set and send them to that location

if (isset($_SESSION['nsiRelocation'])) {
    
    $nsiRelocation = filter_var($_SESSION['nsiRelocation'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    unset($_SESSION['nsiRelocation']);
    
    header('Location: ' . $nsiRelocation);
    die();
}


$queryEstablishments = 'SELECT * FROM establishments';
$stmt = $db->prepare($queryEstablishments);
$stmt->execute();
$results = $stmt->fetchAll();
$stmt->closeCursor();
//echo '<pre>'; print_r($results);

$si = (isset($_SESSION["id"]) && isset($_SESSION["username"])) ? "true" : "false";

$estabSet = (isset($_SESSION["establishmentID"])) ? "true" : "false";
?>

<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">

        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
        <link href="css/index.css" rel="stylesheet" type="text/css"/>
        <title>Who'sRound? - Establishments</title>      
    </head>

    <body>

        <nav class="navbar fixed-top">
            <div style='padding: 0px; margin: 0px;' id="navbar-whole" class="container-fluid">
                <div style="padding: 0px;" id='top-navbar' class="col-md-12 column">
                    <table  style="padding-top: 0px; margin-bottom:0px;" class="table table-bordered table-hover" id="tab_logic">
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
                                        <?php echo "Locations"; ?>
                                </th>
                                <th style="text-align: right; vertical-align: middle;">
                                    <i style="font-size:30px;" data-estabset="<?php echo $estabSet ?>" class="fa fa-shopping-cart" aria-hidden="true"><span style=" font-size: 15px; vertical-align:top; background-color:#FA8E4B;" class="badge"><?php echo quantityTotal(); ?></span></i>
                                </th>
                            </tr>
                        </thead>	
                    </table>
                </div>       
            </div>

            <div style=' background-color: #bfbfbf; margin: 0px;' id="navbar-whole" class="container-fluid">
                <div class="input-group">
                    <span id="searchicon" style="background-color: transparent; border:none;" class="input-group-addon" id="basic-addon1"><i style="font-size:25px;" class="fa fa-search" aria-hidden="true"></i></span>
                    <input id="drinkSearch" style=" font-size: 20px; background-color: transparent; border:none;" type="text" class="form-control" placeholder="Search" >
                    <span id="clearsearch" style="background-color: transparent; border:none;" class="input-group-addon"><i style="font-size:30px;" class="fa fa-times-circle-o" aria-hidden="true"></i></span>
                </div>
            </div>  

        </nav>

<?php foreach ($results as $establishment): ?>
    <?php
    echo '<div id="listDiv" class=" container-fluid">';
    echo '<div data-establishmentID="' . htmlspecialchars($establishment['establishmentID'], ENT_QUOTES, 'utf-8') . '" class="element">';
    echo '<div class="img-div text-center">';
    echo '<img src="images/' . htmlspecialchars($establishment['img'], ENT_QUOTES, 'utf-8') . '"/>';
    echo '</div>';
    echo '<div class="one">';
    echo '<p>' . htmlspecialchars($establishment['name'], ENT_QUOTES, 'utf-8') . '</p>';
    echo '</div>';
    echo '<div class="two">';
    echo '<p>' . htmlspecialchars($establishment['address'], ENT_QUOTES, 'utf-8') . '</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    ?>
        <?php endforeach; ?>

        <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
        <script src="js/index.js"></script>

    </body>
</html>