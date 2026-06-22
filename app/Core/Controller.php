<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $template, array $data = []): string
    {
        return View::render($template, $data);
    }

    protected function redirect(string $path): never
    {
        redirect($path);
    }

    protected function back(): never
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? url('dashboard');
        header('Location: ' . $ref);
        exit;
    }

    protected function json(array $data, int $status = 200): array
    {
        http_response_code($status);
        return $data;
    }

    protected function request(): Request
    {
        return new Request();
    }

    protected function validate(array $rules, array $data): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleSet) {
            $value = $data[$field] ?? null;
            foreach (explode('|', $ruleSet) as $rule) {
                [$rule, $param] = array_pad(explode(':', $rule, 2), 2, null);
                if ($rule === 'required' && (($value === null) || trim((string) $value) === '')) {
                    $errors[$field] = 'Wajib diisi.';
                    break;
                }
                if ($rule === 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Format email tidak valid.';
                }
                if ($rule === 'min' && $value !== null && mb_strlen((string) $value) < (int) $param) {
                    $errors[$field] = "Minimal {$param} karakter.";
                }
            }
        }
        return $errors;
    }
}
