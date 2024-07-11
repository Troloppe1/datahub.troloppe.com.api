<?php

$baseUrl = env('FRONTEND_URL', 'http://localhost:4200');

return [
    'base' => $baseUrl,
    'signin' => "{$baseUrl}/sign-in",
    'reset_password' => "{$baseUrl}/reset-password",
];