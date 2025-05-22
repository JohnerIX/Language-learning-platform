<?php
require 'config.php';

// Verify Google ID token
function verifyGoogleToken($idToken) {
    $client = new Google_Client(['client_id' => 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com']);
    $payload = $client->verifyIdToken($idToken);
    
    if ($payload) {
        return [
            'email' => $payload['email'],
            'name' => $payload['name'] ?? '',
            'picture' => $payload['picture'] ?? null
        ];
    }
    return false;
}

// Handle the Google auth callback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['google_auth'])) {
    $data = json_decode(file_get_contents('php://input'), true);
    $userData = verifyGoogleToken($data['credential']);

    if ($userData) {
        // Check if user exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$userData['email']]);
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'Email already registered']);
            exit;
        }

        // Create new user
        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, password_hash, role, profile_pic) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        // Generate random password for Google users
        $password_hash = password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT);
        
        if ($stmt->execute([
            $userData['name'],
            $userData['email'],
            $password_hash,
            'learner',
            $userData['picture']
        ])) {
            $_SESSION['user_id'] = $conn->lastInsertId();
            $_SESSION['role'] = 'learner';
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid Google token']);
    }
    exit;
}