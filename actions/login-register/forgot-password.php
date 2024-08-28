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
        
        // Alıcı bilgileri
        $mail->setFrom('info@techarsiv.com', 'Mailer');  // Gönderici adresi ve adı
        $mail->addAddress($email);  // Alıcı e-posta adresi (İsim isteğe bağlı)

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Forgot Password';
        $email_template = "
        <table style='width: 100%; max-width: 600px; margin: 0 auto; background-color: #f4f4f4; padding: 20px; font-family: Arial, sans-serif;'>
            <tr>
                <td style='background-color: #ffffff; padding: 30px; border-radius: 10px; text-align: center;'>
                    <h2 style='color: #333333; margin-bottom: 20px;'>🥳 I've seen you better ! $name 🥳</h2>
                    <p style='color: #555555; font-size: 16px; margin-bottom: 30px;'>You seems lost.How's everything going for you ?</p>
                    <a href='http://localhost/anime/reset-password.php?token=$verify_token' 
                    style='display: inline-block; padding: 15px 30px; background-color: #28a745; color: #ffffff; text-decoration: none; font-size: 16px; border-radius: 5px;'>
                    Reset Your Password
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

        ob_start(); // Çıktıyı tamponlamaya başlar
$mail->SMTPDebug = 2;
$mail->Debugoutput = 'html';

// Mail gönderme işlemi
$mail->send();

$debugOutput = ob_get_clean(); // Tamponlanmış çıktıyı yakalar ve temizler

// Çıktıyı dosyaya yazdırma
file_put_contents('smtp_debug_log.html', $debugOutput);


        /*if(!$mail->send()) {
            $flagg1 = 1;
            $response['message'] = 'Error: ' . $mail->ErrorInfo;
        } else {
            $response['message'] = 'Success ';
        }
        */


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
                    $query = "SELECT id,username FROM users where email=:email";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }

                if ($result) {
                    $verify_token = hash('sha256', uniqid(mt_rand(), true));
                    $userId = $result['id'];
                    $username = $result['username'];

                    try {
                        $db->beginTransaction();

                        //* Dummy Get
                        $query = "SELECT COUNT(*) FROM forgotTokenUser WHERE userId = :userId";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
                        $stmt->execute();
                        $result = $stmt->fetchColumn(); // Sadece tek bir değer dönecek

                        if ($result > 0) {
                            //* UPDATE
                            $query = "UPDATE forgotTokenUser SET token=:token where userId=:userId";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(':token', $verify_token, PDO::PARAM_STR);
                            $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
                            $stmt->execute();
                        } else {
                            //* UPDATE
                            $query = "INSERT INTO forgotTokenUser (token, userId) VALUES (:token, :userId)";
                            $stmt = $db->prepare($query);
                            $stmt->bindParam(':token', $verify_token, PDO::PARAM_STR);
                            $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);
                            $stmt->execute();
                        }

                        sendmail_verify($username, $email, $verify_token);
                    } catch (Exception $e) {
                        $db->rollBack();
                        $response = [
                            'status' => 'error',
                            'message' => $e->getMessage()
                        ];
                    }

                    if(!isset($flagg1)){
                        $db->commit();
                        $response['status'] = 'success';
                        $response['message'] = 'Mail sent check your mailbox';
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
