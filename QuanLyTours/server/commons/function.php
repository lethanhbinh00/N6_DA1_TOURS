<?php
function setSessionFlash($key, $message) {
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION[$key] = $message;
}
function getSessionFlash($key) {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (isset($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }
    return null;
}

if (!isset($_SESSION)) {
    session_start();
}