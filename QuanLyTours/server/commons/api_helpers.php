<?php
function sendJsonResponse($data = [], $message = 'Thành công', $status = 200) {
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-Type: application/json');
    http_response_code($status);
    
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

function sendErrorResponse($errors = 'Có lỗi xảy ra', $status = 400) {
    if (is_string($errors)) {
        $errors = [$errors];
    }
    
    header('Content-Type: application/json');
    http_response_code($status);

    echo json_encode([
        'status' => $status,
        'message' => 'Yêu cầu không hợp lệ',
        'errors' => $errors
    ]);
    exit();
}