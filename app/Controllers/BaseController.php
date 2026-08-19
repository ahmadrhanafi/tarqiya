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
    protected $tenantId;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $uri = service('uri');
        $segment = $uri->getSegment(1);

        // 1. Whitelist Route Publik
        $allowedRoutes = ['', 'home', 'login', 'auth', 'logout', 'loading', 'unauthorized'];
        if (in_array($segment, $allowedRoutes)) {
            return;
        }

        // 2. Cek Autentikasi
        if (!session()->get('logged_in')) {
            header('Location: ' . base_url('login'));
            exit();
        }

        // Ambil tenant_id dari session
        $this->tenantId = session()->get('tenant_id');
        $roleSession = session()->get('role');

        // 3. Validasi Role & Akses Superadmin vs Tenant
        if ($segment === 'superadmin' && $roleSession !== 'superadmin') {
            return redirect()->to('unauthorized');
        }
        if ($segment === 'admin' && $roleSession !== 'admin') {
            return redirect()->to('unauthorized');
        }
        if ($segment === 'guru' && $roleSession !== 'guru') {
            return redirect()->to('unauthorized');
        }
        if ($segment === 'wali' && $roleSession !== 'wali') {
            return redirect()->to('unauthorized');
        }
    }
}
