<?php

require_once("includes/configuration.php");
require_once("includes/session.php");

$customerUsername = filter_input(INPUT_POST, 'customerUsername', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerPassword = filter_input(INPUT_POST, 'customerPassword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($customerUsername == NULL || $customerPassword == NULL)
{
    $JSON->result = "input_error";
    $JSON->response = "incorrect credentials";
}
if (strlen($customerUsername) < 6 || strlen($customerPassword) < 8)
{
    $JSON->result = "input_error";
    $JSON->response = "incorrect credentials";
}
else
{
    $uppercase = preg_match('@[A-Z]@', $customerPassword);
    $lowercase = preg_match('@[a-z]@', $customerPassword);
    $number = preg_match('@[0-9]@', $customerPassword);

    if (!$uppercase || !$lowercase || !$number)
    {
        $JSON->result = "input_error";
        $JSON->response = "incorrect credentials";
    }
    else
    {

        try
        {
            $query = 'SELECT accountID, username, password FROM accounts WHERE username = :customerUsername';
            $records = $db->prepare($query);
            $records->bindParam(':customerUsername', $customerUsername);
            $records->execute();
            $results = $records->fetch(PDO::FETCH_ASSOC);
            $records->closeCursor();

            if (password_verify($customerPassword, $results['password']))
            {
                $_SESSION['id'] = filter_var($results['accountID'], FILTER_VALIDATE_INT);
                $_SESSION['username'] = filter_var($results['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $JSON->result = "success";
                $JSON->response = "";
            }
            else
            {
                $JSON->result = "input_error";
                $JSON->response = "incorrect credentials";
            }
        }
        catch (Exception $ex)
        {
            $JSON->result = "technical_error";
            $JSON->response = "";
        }
    }
    
}
echo json_encode($JSON);

