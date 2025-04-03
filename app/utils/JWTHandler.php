<?php
namespace App\Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler {
    private string $jwtSecret = "Hiep"; // In production, this should be in environment variables
    private string $tokenAlgorithm = 'HS256';
    private int $tokenExpiry = 3600; // 1 hour in seconds

    public function generateToken(array $userData): string {
        $issuedAt = time();
        $expire = $issuedAt + $this->tokenExpiry;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'user_id' => $userData['id'],
            'email' => $userData['email']
        ];

        return JWT::encode($payload, $this->jwtSecret, $this->tokenAlgorithm);
    }

    public function validateToken(?string $token): ?array {
        if (!$token) {
            return null;
        }

        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, $this->tokenAlgorithm));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTokenFromHeader(): ?string {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? null;

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        return trim(substr($authHeader, 7));
    }
}