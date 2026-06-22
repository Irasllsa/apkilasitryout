<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;

final class DashboardController extends Controller
{
    public function index(): string
    {
        if (!Auth::check()) {
            $this->redirect('login');
        }

        $user = Auth::user();

        return match ($user['role']) {
            'admin'   => $this->adminDashboard($user),
            'pembuat' => $this->pembuatDashboard($user),
            default   => $this->siswaDashboard($user),
        };
    }

    private function adminDashboard(array $user): string
    {
        $stats = [
            'siswa' => (int) Database::scalar("SELECT COUNT(*) FROM users WHERE role='siswa'"),
            'pembuat' => (int) Database::scalar("SELECT COUNT(*) FROM users WHERE role='pembuat'"),
            'paket_published' => (int) Database::scalar("SELECT COUNT(*) FROM paket_soal WHERE status='published'"),
            'perlu_validasi' => (int) Database::scalar("SELECT COUNT(*) FROM paket_soal WHERE status='diajukan'"),
        ];

        return $this->view('dashboard.admin', [
            'title' => 'Dashboard',
            'stats' => $stats,
        ]);
    }

    private function pembuatDashboard(array $user): string
    {
        $stats = [
            'draft' => (int) Database::scalar("SELECT COUNT(*) FROM paket_soal WHERE created_by=? AND status='draft'", [$user['id']]),
            'diajukan' => (int) Database::scalar("SELECT COUNT(*) FROM paket_soal WHERE created_by=? AND status='diajukan'", [$user['id']]),
            'revisi' => (int) Database::scalar("SELECT COUNT(*) FROM paket_soal WHERE created_by=? AND status='revisi'", [$user['id']]),
            'published' => (int) Database::scalar("SELECT COUNT(*) FROM paket_soal WHERE created_by=? AND status='published'", [$user['id']]),
        ];

        return $this->view('dashboard.pembuat', [
            'title' => 'Dashboard',
            'stats' => $stats,
        ]);
    }

    private function siswaDashboard(array $user): string
    {
        $stats = [
            'tugas_baru' => (int) Database::scalar("SELECT COUNT(*) FROM delegasi WHERE siswa_id=? AND status IN ('assigned','ongoing')", [$user['id']]),
            'selesai' => (int) Database::scalar("SELECT COUNT(*) FROM delegasi WHERE siswa_id=? AND status='done'", [$user['id']]),
        ];

        return $this->view('dashboard.siswa', [
            'title' => 'Dashboard',
            'stats' => $stats,
        ]);
    }
}
