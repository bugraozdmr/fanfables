<?php
//* authenticate
include "../admin/auth/authenticate.php";

header('Content-Type: application/json');

include '../connect.php';

$response = [
    'status' => 'error',
    'message' => 'Invalid request method'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // JSON verisini ayrıştırın
    if (json_last_error() === JSON_ERROR_NONE) {
        $userId = isset($data['id']) ? filter_var($data['id'], FILTER_SANITIZE_STRING) : null;
        $time = isset($data['time']) ? filter_var($data['time'], FILTER_SANITIZE_STRING) : null;
        
        if (!empty($userId) || !empty($time)) {
            // ONLY INSERT
            try {
                $db->beginTransaction();

                if($time == "1_day"){
                    $dd = 1;
                }
                else if($time == "2_days"){
                    $dd = 2;
                }
                else if($time == "1_week"){
                    $dd = 7;
                }
                else if($time == "unlimited"){
                    $dd = 4000;
                }
                else{
                    $response = [
                        'status' => 'error',
                        'message' => 'Something went wrong !'
                    ];
                }

                if(isset($dd)){
                    $istanbulTimeZone = new DateTimeZone('Europe/Istanbul');
                    $now = new DateTime('now', $istanbulTimeZone);
                    $now->add(new DateInterval('P' . $dd . 'D'));
                    $tt = $now->format('Y-m-d H:i:s');

                    $sql = "INSERT INTO UserBans (userId,until) VALUES (:userId,:until)";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':userId', $userId);
                    $stmt->bindParam(':until', $tt);

                    $stmt->execute();

                    $db->commit();
                    $response = [
                        'status' => 'success',
                        'message' => 'Record created successfully'
                    ];
                }
            } catch (\PDOException $e) {
                $response = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        } 
        else{
            $response = [
                'status' => 'error',
                'message' => 'You can\'t do that sir !'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Invalid JSON: ' . json_last_error_msg()
        ];
    }

    echo json_encode($response);
    exit();
}

?>
