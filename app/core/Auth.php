<?php

/**
 * Auth helpers – current user and role checks.
 * Data comes from session (set after login from public.users).
 */
class Auth
{
    public static function id(): ?string
    {
        return Session::get('user_id');
    }

    public static function jwt(): ?string
    {
        return Session::get('jwt');
    }

    public static function name(): ?string
    {
        return Session::get('user_name');
    }

    public static function role(): ?string
    {
        return Session::get('user_role');
    }

    public static function branchId(): ?string
    {
        return Session::get('user_branch_id');
    }

    public static function email(): ?string
    {
        return Session::get('user_email');
    }

    public static function phone(): ?string
    {
        return Session::get('user_phone');
    }

    public static function check(): bool
    {
        $id = self::id();
        $jwt = self::jwt();
        if ($id === null || $jwt === null) {
            return false;
        }

        // Auto-logout after 15 minutes of inactivity
        $now = time();
        $last = Session::get('last_activity', $now);
        $timeoutSeconds = 15 * 60;
        if ($last + $timeoutSeconds < $now) {
            Session::destroy();
            return false;
        }

        // Update last activity timestamp
        Session::set('last_activity', $now);
        return true;
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function isManager(): bool
    {
        return self::role() === 'manager';
    }

    public static function isUser(): bool
    {
        return self::role() === 'user';
    }

    /**
     * Whether the current role can access a permission.
     * admin: full; manager: reports + team; user: own clients + interactions.
     */
    public static function can(string $permission): bool
    {
        $role = self::role();
        if ($role === 'admin') {
            return true;
        }
        if ($role === 'manager') {
            return in_array($permission, ['reports', 'clients', 'interactions', 'email_generator', 'calling_script'], true);
        }
        if ($role === 'user') {
            return in_array($permission, ['clients', 'interactions', 'email_generator', 'calling_script'], true);
        }
        return false;
    }

    /**
     * Redirect to login if not authenticated.
     */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . base_url());
            exit;
        }
    }
}
