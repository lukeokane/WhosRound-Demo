   <?php 
   
require_once("../includes/configuration.php");
require_once("../includes/session.php");

// setting session variables here temporarily
$_SESSION['staffEstablishment'] = "Ridleys";
$_SESSION['staffEstablishmentID'] = 1;
$_SESSION['staffUsername'] = "Billy";


$establishID = filter_var($_SESSION['staffEstablishmentID'], FILTER_SANITIZE_FULL_SPECIAL_CHARS); // get from session when pub picker is setup
$establishName = filter_var($_SESSION['staffEstablishment'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$staffUsername = filter_var($_SESSION['staffUsername'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$queryOrders = 'SELECT a.username, o.orderID, o.establishmentID, o.accountID, o.delivery, o.orderedAt, o.completed '
        . 'FROM accounts a, orders o '
        . 'WHERE o.establishmentID = :establishID '
        . 'AND a.accountID = o.accountID '
        . 'ORDER BY o.orderedAt DESC';
$stmt = $db->prepare($queryOrders);
$stmt->bindValue(':establishID', $establishID);
$stmt->execute();
$orders = $stmt->fetchAll();
$stmt->closeCursor();

   
   
   foreach ($orders as $order): ?>
                <?php
                //$result .= '';
                
                echo '<div class="container2"><a style="margin-bottom: 0.4em" href="viewOrder.php?orderID=' . htmlspecialchars($order['orderID'], ENT_QUOTES, 'utf-8') . '" class="order col-sm-12" tf bb>';
                if (filter_var($order['completed']) == 0) {
                    echo '<div style="background-color: #FFFFFF; color:#424C58;" class="orderRow col-sm-4">' . htmlspecialchars($order['username'], ENT_QUOTES, 'utf-8') . '</div>';
                    echo '<div style="background-color: #FFFFFF; color:#424C58; text-align:center;" class="orderRow col-sm-2">' . htmlspecialchars($order['orderID'], ENT_QUOTES, 'utf-8') . '</div>';
                    echo '<div style="background-color: #FFFFFF; color:#424C58; text-align:center;" class="orderRow col-sm-4">' . htmlspecialchars($order['delivery'], ENT_QUOTES, 'utf-8') . '</div>';
                    echo '<div style="text-align:center; padding-left: 1em;" class="col-sm-2">';
                    echo '<img style="margin: -.45em" src="../images/' . htmlspecialchars($order['completed'], ENT_QUOTES, 'utf-8') . '.png"></div></div>';
                } else if (filter_var($order['completed']) == 1) {
                    echo '<div style="background-color: #33C4B3;" class="orderRow col-sm-4">' . htmlspecialchars($order['username'], ENT_QUOTES, 'utf-8') . '</div>';
                    echo '<div style="background-color: #33C4B3; text-align:center;" class="orderRow col-sm-2">' . htmlspecialchars($order['orderID'], ENT_QUOTES, 'utf-8') . '</div>';
                    echo '<div style="background-color: #33C4B3; text-align:center;" class="orderRow col-sm-4">' . htmlspecialchars($order['delivery'], ENT_QUOTES, 'utf-8') . '</div>';
                    echo '<div style="text-align:center; padding-left: 1em;" class="col-sm-2">';
                    echo '<img style="margin: -.45em" src="../images/' . htmlspecialchars($order['completed'], ENT_QUOTES, 'utf-8') . '.png"></div></div>';
                } else if (filter_var($order['completed']) == 2) {
                    echo '<div style="background-color: #f78e50;" class="orderRow col-sm-4">' . htmlspecialchars($order['username'], ENT_QUOTES, 'utf-8') . '</div>';
                    echo '<div style="background-color: #f78e50; text-align:center;" class="orderRow col-sm-2">' . htmlspecialchars($order['orderID'], ENT_QUOTES, 'utf-8') . '</div>';
                    echo '<div style="background-color: #f78e50; text-align:center;" class="orderRow col-sm-4">' . htmlspecialchars($order['delivery'], ENT_QUOTES, 'utf-8') . '</div>';
                    echo '<div style="text-align:center; padding-left: 1em;" class="col-sm-2">';
                    echo '<img style="margin: -.45em" src="../images/' . htmlspecialchars($order['completed'], ENT_QUOTES, 'utf-8') . '.png"></div></div>';
                }
                ?>
            <?php endforeach; ?>