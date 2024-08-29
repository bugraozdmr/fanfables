<?php
header('Content-Type: application/json');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';


$response = [
    'status' => 'error',
    'message' => 'Something went wrong'
];

function sendmail_info($name, $email)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'veni.odeaweb.com';       // SMTP sunucusu
        $mail->SMTPAuth   = true;                       // SMTP kimlik doğrulaması etkin
        $mail->Username   = 'info@techarsiv.com';       // SMTP kullanıcı adı
        $mail->Password   = '/Kardanadam1';             // SMTP şifresi
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // TLS güvenliği
        $mail->Port       = 587;                        // TLS için 587 portu

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false, // self-signed sertifikalar kabul edilmeyecek
            )
        );

        // Recipients
        $mail->setFrom('info@techarsiv.com', 'Mailer');
        $mail->addAddress($email); // Name is optional

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password has been reset';
        $email_template = "
        <table style='width: 100%; max-width: 600px; margin: 0 auto; background-color: #f4f4f4; padding: 20px; font-family: Arial, sans-serif;'>
            <tr>
                <td style='background-color: #ffffff; padding: 30px; border-radius: 10px; text-align: center;'>
                    <h2 style='color: #333333; margin-bottom: 20px;'>💻 You have a new style now ! $name 💻</h2>
                    <p style='color: #555555; font-size: 16px; margin-bottom: 30px;'>We just want to inform you that your password has been reset.</p>
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


        if(!$mail->send()) {
            $flagg1 = 1;
            $response['message'] = 'Error';
        } else {
            $response['message'] = 'Success ';
        }
    } catch (Exception $e) {
        $response['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $password = isset($data['password']) ? trim(filter_var($data['password'], FILTER_SANITIZE_STRING)) : null;
        $token = isset($data['token']) ? trim(filter_var($data['token'], FILTER_SANITIZE_STRING)) : null;

        if (empty($password) || empty($token)) {
            $response['message'] = "Please fill all the fields";
        } else {
            $allowedCharacters = '/^[a-zA-Z0-9@._+\-%\/]+$/';
            if (preg_match('/\s/', $password)) {
                $response['message'] = "Password cannot contain spaces.";
            } elseif (!preg_match($allowedCharacters, $password)) {
                $response['message'] = "Password contains invalid characters.";
            } else {
                //? connect to the db
                include '../connect.php';
                try {
                    $db->beginTransaction();

                    $query = "SELECT * FROM forgotTokenUser where token=:token";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!empty($result['token'])) {
                        $tokenCreatedAt = $result['createdAt'];

                        // (24 hours)
                        $tokenCreatedAtTime = new DateTime($tokenCreatedAt);
                        $currentTime = new DateTime();
                        $interval = $currentTime->diff($tokenCreatedAtTime);
                        $hours = ($interval->days * 24) + $interval->h;

                        if ($hours > 24) {
                            $response['message'] = "Token expired !";
                        }
                        else{
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                            //* UPDATE
                            $query = "UPDATE users SET password=:password where id=:userId";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
                            //? id
                            $userId = $result['userId'];
                            $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
                            $stmt->execute();

                            //* GET RID OF THE OLD TOKEN :D
                            $verify_token = hash('sha256', uniqid(mt_rand(), true));
                            $query = "UPDATE forgotTokenUser SET token=:token where userId=:userId";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(':token', $verify_token, PDO::PARAM_STR);
                            //? id
                            $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
                            $stmt->execute();

                            $query = "SELECT username,email FROM users WHERE id=:userId";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
                            $stmt->execute();
                            $username = $stmt->fetch(PDO::FETCH_ASSOC);


                            sendmail_info($username['username'], $username['email']);


                            if(!isset($flagg1)){
                                $response['status'] = 'success';
                                $response['message'] = 'Your password changed ! Now go to login';

                                $db->commit();
                            }
                            
                        }
                    } else {
                        $response['message'] = "Token is invalid.";
                    }
                } catch (Exception $e) {
                    $db->rollBack();
                    $response = [
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
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
