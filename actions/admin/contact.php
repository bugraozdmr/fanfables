<?php
//* authenticate
include "../admin/auth/authenticate.php";

header('Content-Type: application/json');

include '../connect.php';

$response = [
    'status' => 'error',
    'message' => 'Invalid request method'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $twitter = isset($data['twitter']) ? filter_var($data['twitter'], FILTER_SANITIZE_STRING) : null;
        $youtube = isset($data['youtube']) ? filter_var($data['youtube'], FILTER_SANITIZE_STRING) : null;
        $instagram = isset($data['instagram']) ? filter_var($data['instagram'], FILTER_SANITIZE_STRING) : null;
        $facebook = isset($data['facebook']) ? filter_var($data['facebook'], FILTER_SANITIZE_STRING) : null;
        $mail = isset($data['mail']) ? filter_var($data['mail'], FILTER_SANITIZE_STRING) : null;
        $address = isset($data['address']) ? filter_var($data['address'], FILTER_SANITIZE_STRING) : null;
        $google_maps_iframe = isset($data['google_maps_iframe']) ? filter_var($data['google_maps_iframe'], FILTER_SANITIZE_STRING) : null;
        $phone = isset($data['phone']) ? filter_var($data['phone'], FILTER_SANITIZE_STRING) : null;


        $updateFields = [];
        $updateParams = [];

        if ($youtube !== null) {
            $updateFields[] = "youtube = :youtube";
            $updateParams[':youtube'] = $youtube;
        }
        if ($twitter !== null) {
            $updateFields[] = "twitter = :twitter";
            $updateParams[':twitter'] = $twitter;
        }
        if ($instagram !== null) {
            $updateFields[] = "instagram = :instagram";
            $updateParams[':instagram'] = $instagram;
        }
        if ($facebook !== null) {
            $updateFields[] = "facebook = :facebook";
            $updateParams[':facebook'] = $facebook;
        }
        if ($mail !== null) {
            $updateFields[] = "mail = :mail";
            $updateParams[':mail'] = $mail;
        }
        if ($address !== null) {
            $updateFields[] = "address = :address";
            $updateParams[':address'] = $address;
        }
        if ($google_maps_iframe !== null) {
            $updateFields[] = "google_maps_iframe = :google_maps_iframe";
            $updateParams[':google_maps_iframe'] = $google_maps_iframe;
        }
        if ($phone !== null) {
            $updateFields[] = "phone = :phone";
            $updateParams[':phone'] = $phone;
        }
        try {
            if (!empty($updateFields)) {
                $updateQuery = "UPDATE ContactInfo SET " . implode(", ", $updateFields) . " WHERE id = :id";
                $id = 1;
                $updateParams[':id'] = $id;

                $stmt = $db->prepare($updateQuery);
                foreach ($updateParams as $param => $value) {
                    $stmt->bindValue($param, $value);
                }
                $stmt->execute();

                $response = [
                    'status' => 'success',
                    'message' => 'Contact information updated successfully!'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'No data to update'
                ];
            }
        } catch (PDOException $e) {
            $response = [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}

echo json_encode($response);
