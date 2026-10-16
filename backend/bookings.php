
<?php

require __DIR__ . '/db.php';

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $stmt = $pdo->query(
            'SELECT id, customer, destination, status
             FROM bookings
             ORDER BY id ASC'
        );

        echo json_encode(
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );

    } elseif ($method === 'POST') {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        $customer = trim($data['customer'] ?? '');
        $destination = trim($data['destination'] ?? '');

        if ($customer === '' || $destination === '') {
            http_response_code(400);

            echo json_encode([
                'error' => 'Customer and destination are required.'
            ]);

            exit;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO bookings (customer, destination, status)
             VALUES (:customer, :destination, :status)'
        );

        $stmt->execute([
            'customer' => $customer,
            'destination' => $destination,
            'status' => 'Pending'
        ]);

        http_response_code(201);

        echo json_encode([
            'id' => (int) $pdo->lastInsertId(),
            'customer' => $customer,
            'destination' => $destination,
            'status' => 'Pending'
        ]);

    } elseif ($method === 'PUT') {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        $id = filter_var(
            $data['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $customer = trim($data['customer'] ?? '');
        $destination = trim($data['destination'] ?? '');

        if (!$id || $id < 1 ||
            $customer === '' || $destination === '') {
            http_response_code(400);

            echo json_encode([
                'error' => 'Valid ID, customer and destination are required.'
            ]);

            exit;
        }

        $stmt = $pdo->prepare(
            'UPDATE bookings
            SET customer = :customer,
                destination = :destination
            WHERE id = :id'
        );

        $stmt->execute([
            'customer' => $customer,
            'destination' => $destination,
            'id' => $id
        ]);

        $stmt = $pdo->prepare(
            'SELECT id, customer, destination, status
            FROM bookings
            WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            http_response_code(404);

            echo json_encode([
                'error' => 'Booking not found.'
            ]);

            exit;
        }

        $booking['id'] = (int) $booking['id'];

        echo json_encode($booking);
    } elseif ($method === 'PATCH') {
        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        $id = filter_var(
            $data['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        $status = $data['status'] ?? '';

        if (
            !$id || $id < 1 ||
            !in_array($status, ['Pending', 'Confirmed'], true)
        ) {
            http_response_code(400);

            echo json_encode([
                'error' => 'Valid ID and status are required.'
            ]);

            exit;
        }

        $stmt = $pdo->prepare(
            'UPDATE bookings
            SET status = :status
            WHERE id = :id'
        );

        $stmt->execute([
            'status' => $status,
            'id' => $id
        ]);

        $stmt = $pdo->prepare(
            'SELECT id, customer, destination, status
            FROM bookings
            WHERE id = :id'
        );

        $stmt->execute(['id' => $id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            http_response_code(404);

            echo json_encode([
                'error' => 'Booking not found.'
            ]);

            exit;
        }

        $booking['id'] = (int) $booking['id'];

        echo json_encode($booking);
} elseif ($method === 'DELETE') {
    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    $id = filter_var(
        $data['id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (!$id || $id < 1) {
        http_response_code(400);

        echo json_encode([
            'error' => 'A valid booking ID is required.'
        ]);

        exit;
    }

    $stmt = $pdo->prepare(
        'DELETE FROM bookings WHERE id = :id'
    );

    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);

        echo json_encode([
            'error' => 'Booking not found.'
        ]);

        exit;
    }

    echo json_encode([
        'message' => 'Booking deleted successfully.'
    ]);
} else {
            http_response_code(405);

            echo json_encode([
                'error' => 'Method not allowed.'
            ]);
        }

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'error' => 'A database operation failed.'
    ]);
}
