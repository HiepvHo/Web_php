<?php
class SessionHelper {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['session_id'])) {
            $_SESSION['session_id'] = session_id();
        }

        if (!isset($_SESSION['cart_count'])) {
            $_SESSION['cart_count'] = 0;
        }
    }

    public static function setFlash($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function getFlash($key) {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $value;
        }
        return null;
    }

    public static function hasFlash($key) {
        return isset($_SESSION[$key]);
    }

    public static function updateCartCount($count) {
        $_SESSION['cart_count'] = $count;
    }

    public static function getCartCount() {
        return $_SESSION['cart_count'] ?? 0;
    }

    public static function clearCart() {
        $_SESSION['cart_count'] = 0;
    }

    public static function destroy() {
        session_destroy();
    }
}