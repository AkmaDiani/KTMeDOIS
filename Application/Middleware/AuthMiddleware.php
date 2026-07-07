<?php
// ============================================
// AUTH MIDDLEWARE
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AuthMiddleware
{
    public function handle()
    {
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['supplier_id'])) {
            $_SESSION['error'] = 'Please login first.';
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }
        return true;
    }

    public function isStaff()
    {
        return isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'staff';
    }

    public function isSupplier()
    {
        return isset($_SESSION['supplier_id']) && $_SESSION['user_type'] === 'supplier';
    }

    public function requireStaff()
    {
        $this->handle();
        if (!$this->isStaff()) {
            $_SESSION['error'] = 'Access denied. Staff only.';
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }
        return true;
    }

    public function requireSupplier()
    {
        $this->handle();
        if (!$this->isSupplier()) {
            $_SESSION['error'] = 'Access denied. Supplier only.';
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }
        return true;
    }

    public function getUserId()
    {
        return $_SESSION['user_id'] ?? $_SESSION['supplier_id'] ?? null;
    }

    public function getUsername()
    {
        return $_SESSION['username'] ?? $_SESSION['supplier_name'] ?? 'Guest';
    }
}
