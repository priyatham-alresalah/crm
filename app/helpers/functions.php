<?php

/**
 * Base URL for links (no trailing slash).
 * Uses BASE_URL from env; works on XAMPP and cPanel.
 */
function base_url(string $path = ''): string
{
    $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '/crm/public';
    $path = ltrim($path, '/');
    return $path ? $base . '/' . $path : $base;
}

/**
 * Current user JWT from session (for API calls).
 */
function session_jwt(): ?string
{
    return Session::get('jwt');
}

/**
 * Check if user is logged in.
 */
function is_logged_in(): bool
{
    return Auth::check();
}

/**
 * Supabase REST client with current session JWT.
 */
function supabase(): SupabaseClient
{
    return new SupabaseClient(Session::get('jwt'));
}
