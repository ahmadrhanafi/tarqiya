<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;
    protected $request;
    protected $helpers = [];

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
        $uri = service('uri');
        $segment = $uri->getSegment(1); // Contoh: admin, guru, wali, login, auth, dll

        // 1. DAFTARKAN KECUALIAN (Whitelist)
        // Jika sedang mengakses halaman login, proses login, logout, atau loading, lewati pengecekan!
        $allowedRoutes = ['', 'home', 'login', 'auth', 'logout', 'loading'];
        if (in_array($segment, $allowedRoutes)) {
            return; // Keluar dari initController, biarkan halaman publik diakses bebas
        }

        // 2. Cek autentikasi umum untuk halaman selain di atas
        if (!session()->get('logged_in')) {
            header('Location: ' . base_url('login'));
            exit();
        }

        // 3. Validasi kecocokan role dengan URL segment
        $roleSession = session()->get('role'); // misal: 'admin', 'guru', 'wali'

        if ($segment === 'admin' && $roleSession !== 'admin') {
            header('Location: ' . base_url('unauthorized'));
            exit();
        }
        if ($segment === 'guru' && $roleSession !== 'guru') {
            header('Location: ' . base_url('unauthorized'));
            exit();
        }
        if ($segment === 'wali' && $roleSession !== 'wali') {
            header('Location: ' . base_url('unauthorized'));
            exit();
        }
    }
}
