<?php
header('Content-Type: application/json');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';


$response = [
    'status' => 'error',
    'message' => 'Something went wrong'
];

function sendmail_verify($name, $email, $verify_token)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'bugra.ozdemir5834@gmail.com';
        $mail->Password   = 'nuvschcsmpsvzfve';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('bugra.ozdemir5834@gmail.com', 'Mailer');
        $mail->addAddress($email); // Name is optional

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verification Email Again';
        $email_template = "
        <table style='width: 100%; max-width: 600px; margin: 0 auto; background-color: #f4f4f4; padding: 20px; font-family: Arial, sans-serif;'>
            <tr>
                <td style='background-color: #ffffff; padding: 30px; border-radius: 10px; text-align: center;'>
                    <h2 style='color: #333333; margin-bottom: 20px;'>🎈 You look nice today ! $name 🎈</h2>
                    <p style='color: #555555; font-size: 16px; margin-bottom: 30px;'>We can't wait to see you around!</p>
                    <a href='http://localhost/e-trade/verify-email.php?token=$verify_token' 
                    style='display: inline-block; padding: 15px 30px; background-color: #28a745; color: #ffffff; text-decoration: none; font-size: 16px; border-radius: 5px;'>
                    Confirm Your Account
                    </a>
                </td>
            </tr>
            <tr>
                <td style='text-align: center; padding: 20px; color: #999999; font-size: 14px;'>
                    If you did not sign up for this account, you can ignore this email.
                </td>
            </tr>
        </table>
";


        $mail->Body = $email_template;

        $mail->send();
    } catch (Exception $e) {
        $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $email = isset($data['email']) ? trim(filter_var($data['email'], FILTER_SANITIZE_STRING)) : null;

        if (empty($email)) {
            $response['message'] = "Please fill all the fields";
        } else {
            $allowedCharacters = '/^[a-zA-Z0-9._+-@]+$/';
            if (preg_match('/\s/', $email)) {
                $response['message'] = "Email cannot contain spaces.";
            } elseif (!preg_match($allowedCharacters, $email)) {
                $response['message'] = "Email contains invalid characters.";
            } else {
                //? connect to the db
                include '../connect.php';

                try {
                    $query = "SELECT id,username,verified FROM users where email=:email";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }

                if ($result) {
                    $verified = $result['verified'];
                    if($verified != 1){
                        $verify_token = hash('sha256', uniqid(mt_rand(), true));
                        $userId = $result['id'];
                        $username = $result['username'];
    
                        try{
                            $db->beginTransaction();
                            $query = "UPDATE tokenUser SET token=:token where userId=:userId";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(':token', $verify_token, PDO::PARAM_STR);
                            $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
                            $stmt->execute();
    
                            sendmail_verify($username, $email, $verify_token);
                        }
                        catch (Exception $e) {
                            $db->rollBack();
                            $response = [
                                'status' => 'error',
                                'message' => $e->getMessage()
                            ];
                        }
    
                        $db->commit();
                        $response['status'] = 'success';
                        $response['message'] = 'Mail sent check your mailbox';
                    }
                    else{
                        $response['message'] = 'Your mail has already been confirmed';
                    }
                } else {
                    $response['message'] = 'Mail not found ...';
                }
            }
        }
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Invalid JSON: ' . json_last_error_msg()
    ];
}


echo json_encode($response);
exit();
?>