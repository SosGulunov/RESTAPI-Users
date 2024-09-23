<?php

header("Content-Type: application/json; charset=UTF-8");
require 'config.php';
require 'db.php';
require 'functions.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;


$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($method == 'OPTIONS') {
    exit(0);
}

$pdo = DB::getInstance();

if ($pathParts[0] !== 'users') {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found']);
    exit;
}

switch ($method) {
    case 'POST':
        if (isset($pathParts[1]) && $pathParts[1] === 'login') {
            login($pdo);
        } else {
            createUser($pdo);
        }
        break;
    case 'GET':
        authenticate();
        if (isset($pathParts[1])) {
            getUser($pdo, $pathParts[1]);
        } else {
            getUsers($pdo);
        }
        break;
    case 'PUT':
        authenticate();
        if (isset($pathParts[1])) {
            updateUser($pdo, $pathParts[1]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'User ID is required']);
        }
        break;
    case 'DELETE':
        authenticate();
        if (isset($pathParts[1])) {
            deleteUser($pdo, $pathParts[1]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'User ID is required']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}
