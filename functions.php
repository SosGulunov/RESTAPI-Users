<?php

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once 'db.php';

function createUser($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    var_dump($data);
    if (!isset($data['first_name'], $data['second_name'], $data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'Email already exists']);
        exit;
    }

    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (first_name, second_name, email, password) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([
            $data['first_name'],
            $data['second_name'],
            $data['email'],
            $hashedPassword
        ]);
        http_response_code(201);
        echo json_encode(['message' => 'User created successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create user']);
    }
}

function login($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password are required']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($data['password'], $user['password'])) {
        $payload = [
            'iss' => 'yourdomain.com',
            'sub' => $user['id'],
            'iat' => time(),
            'exp' => time() + (60*60*60) // Токен действует 1 час
        ];
        $jwt = JWT::encode($payload, JWT_SECRET, 'HS256');
        echo json_encode(['token' => $jwt]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
}

function getUser($pdo, $id) {
    $stmt = $pdo->prepare("SELECT id, first_name, second_name, email, date_create FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo json_encode($user);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
    }
}

function getUsers($pdo) {
    $stmt = $pdo->query("SELECT id, first_name, second_name, email, date_create FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
}

function updateUser($pdo, $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $fields = [];
    $values = [];

    if (isset($data['first_name'])) {
        $fields[] = 'first_name = ?';
        $values[] = $data['first_name'];
    }
    if (isset($data['second_name'])) {
        $fields[] = 'second_name = ?';
        $values[] = $data['second_name'];
    }
    if (isset($data['email'])) {
        $fields[] = 'email = ?';
        $values[] = $data['email'];
    }
    if (isset($data['password'])) {
        $fields[] = 'password = ?';
        $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
    }

    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        exit;
    }

    $values[] = $id;
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        echo json_encode(['message' => 'User updated successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update user']);
    }
}

function deleteUser($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    try {
        $stmt->execute([$id]);
        echo json_encode(['message' => 'User deleted successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete user']);
    }
}

function authenticate() {
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Authorization header missing']);
        exit;
    }

    $matches = [];
    if (!preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid authorization header']);
        exit;
    }

    $token = $matches[1];
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
    } catch (Exception $e) {
        echo $e;
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
}