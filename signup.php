<?php

require_once("includes/configuration.php");
require_once("includes/session.php");

$customerID = null;
$customerUsername = filter_input(INPUT_POST, 'customerUsername', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$customerEmail = filter_input(INPUT_POST, 'customerEmail', FILTER_SANITIZE_EMAIL);
$customerPassword1 = filter_input(INPUT_POST, 'customerPassword1');
$customerPassword2 = filter_input(INPUT_POST, 'customerPassword2');

if ($customerID != NULL || $customerUsername == NULL || $customerEmail == NULL || $customerPassword1 == NULL || $customerPassword2 == NULL)
{
    $JSON->result = "input_error";
    $JSON->response = "incorrect input format";
}
if (strlen($customerUsername) < 6)
{
    $JSON->result = "input_error";
    $JSON->response = "Username must be at least 6 characters";
}
else if (strlen($customerPassword1) < 8 || strlen($customerPassword2) < 8)
{
    $JSON->result = "input_error";
    $JSON->response = "Password must be at least 8 characters";
}
else
{
    $uppercase1 = preg_match('@[A-Z]@', $customerPassword1);
    $lowercase1 = preg_match('@[a-z]@', $customerPassword1);
    $number1 = preg_match('@[0-9]@', $customerPassword1);
    $uppercase2 = preg_match('@[A-Z]@', $customerPassword2);
    $lowercase2 = preg_match('@[a-z]@', $customerPassword2);
    $number2 = preg_match('@[0-9]@', $customerPassword2);

    if (!$uppercase1 || !$lowercase1 || !$number1 || !$uppercase2 || !$lowercase2 || !$number2)
    {
        $JSON->result = "input_error";
        $JSON->response = "	Password must contain at least one number and one uppercase character.";

    }

    else if ($customerPassword1 == $customerPassword2)
    {
        if ($customerEmail === FALSE || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL))
        {
            $JSON->result = "input_error";
            $JSON->response = "Email address is not valid, try again.";
        }
        else
        {
            $hashed_password = password_hash($customerPassword1, PASSWORD_BCRYPT); // changed to bcrypt from password_default

            if ($hashed_password == NULL)
            {
                $JSON->result = "input_error";
                $JSON->response = "incorrect credentials";
            }
            else
            {
                $queryCheckEmail = "SELECT email FROM accounts WHERE email = :customerEmail";
                $statement1 = $db->prepare($queryCheckEmail);
                $statement1->bindValue(':customerEmail', $customerEmail);
                $statement1->execute();
                $matched_email = $statement1->fetch();
                $statement1->closeCursor();

                if ($matched_email > 0)
                {
                    $JSON->result = "input_error";
                    $JSON->response = "email already exists";
                }
                else
                {
                    $queryCheckUsername = "SELECT username FROM accounts WHERE username = :customerUsername";
                    $statement2 = $db->prepare($queryCheckUsername);
                    $statement2->bindValue(':customerUsername', $customerUsername);
                    $statement2->execute();
                    $matched_username = $statement2->fetch();
                    $statement2->closeCursor();

                    if ($matched_username > 0)
                    {
                        $JSON->result = "input_error";
                        $JSON->response = "Username already exists, try again.";
                    }
                    else
                    {
                            $queryInsert = "INSERT INTO accounts (accountID, username, email, password) VALUES (:customerID, :customerUsername, :customerEmail, :hashed_password)";
                            $statement3 = $db->prepare($queryInsert);
                            $statement3->bindValue(':customerID', $customerID);             
                            $statement3->bindValue(':customerUsername', $customerUsername);
                            $statement3->bindValue(':customerEmail', $customerEmail);
                            $statement3->bindValue(':hashed_password', $hashed_password);
                            $statement3->execute();
                            $statement3->closeCursor();
                               
                            
                        if ($statement3->rowCount() == 1)
                        {	
							$JSON->result = "success";
                            $JSON->response = "Success, please log in"; 					
                        }
                        else
                        {
                            $JSON->result = "input_error";
                            $JSON->response = "database error";
                        }

                    }
                }
            }
        }
    }
    else
    {
        $JSON->result = "input_error";
        $JSON->response = "passwords must match";
    }
}
echo json_encode($JSON);



