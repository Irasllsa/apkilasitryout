<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;

final class AuthController extends Controller
{
    public function showLogin(): string
    {
        return $this->view('auth.login', [
            'title' => 'Masuk',
            'error' => Session::getFlash('error'),
            'old_username' => Session::getFlash('old_username'),
        ]);
    }

    public function login(): never
    {
        $username = trim((string) input('username'));
        $password = (string) input('password');

        if ($username === '' || $password === '') {
            Session::flash('error', 'Username dan password wajib diisi.');
            Session::flash('old_username', $username);
            $this->redirect('login');
        }

        if (!Auth::attempt($username, $password)) {
            Session::flash('error', 'Username atau password salah, atau akun nonaktif.');
            Session::flash('old_username', $username);
            $this->redirect('login');
        }

        $this->redirect('dashboard');
    }

    public function logout(): never
    {
        Auth::logout();
        $this->redirect('login');
    }
}
