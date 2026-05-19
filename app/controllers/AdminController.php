<?php
declare(strict_types=1);

namespace App\controllers;

use App\models\ProgrammeModel;
use App\models\ModuleModel;
use App\models\InterestModel;
use App\models\AdminModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminController
{
    private ProgrammeModel $programmes;
    private ModuleModel $modules;
    private InterestModel $interests;
    private AdminModel $adminModel;

    public function __construct()
    {
        $this->programmes  = new ProgrammeModel();
        $this->modules     = new ModuleModel();
        $this->interests   = new InterestModel();
        $this->adminModel  = new AdminModel();
    }

    // ── Auth ──────────────────────────────────────────────────────────────────

    public function loginForm(Request $request, Response $response): Response
    {
        $error = $_SESSION['auth_error'] ?? null;
        $old   = $_SESSION['auth_old']   ?? [];
        unset($_SESSION['auth_error'], $_SESSION['auth_old']);
        ob_start();
        extract(['error' => $error, 'old' => $old]);
        require __DIR__ . '/../views/admin/login.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    public function login(Request $request, Response $response): Response
    {
        $body     = (array)$request->getParsedBody();
        $username = trim($body['username'] ?? '');
        $password = $body['password'] ?? '';

        $admin = $this->adminModel->findByUsername($username);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']       = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            return $response->withHeader('Location', url('/admin/dashboard'))->withStatus(302);
        }

        $_SESSION['auth_error']        = 'Invalid username or password.';
        $_SESSION['auth_old']          = ['username' => $username];
        $_SESSION['open_admin_modal']  = true;
        return $response->withHeader('Location', url('/'))->withStatus(302);
    }

    public function logout(Request $request, Response $response): Response
    {
        session_destroy();
        return $response->withHeader('Location', url('/'))->withStatus(302);
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function dashboard(Request $request, Response $response): Response
    {
        $allProgrammes = $this->programmes->getAll();
        $allModules    = $this->modules->getAll();
        $allRegs       = $this->interests->getAll();

        $published = array_filter($allProgrammes, fn($p) => $p['published']);

        $stats = [
            'total_programmes'  => count($allProgrammes),
            'published_programmes' => count($published),
            'total_modules'     => count($allModules),
            'total_registrations' => count($allRegs),
        ];

        $recentRegistrations = array_slice($allRegs, 0, 5);
        $flashMessage = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        ob_start();
        extract(['stats' => $stats, 'recentRegistrations' => $recentRegistrations, 'flashMessage' => $flashMessage]);
        require __DIR__ . '/../views/admin/dashboard.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    // ── Programmes ────────────────────────────────────────────────────────────

    public function programmesList(Request $request, Response $response): Response
    {
        $programmes   = $this->programmes->getAll();
        $flashMessage = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        ob_start();
        extract(['programmes' => $programmes, 'flashMessage' => $flashMessage]);
        require __DIR__ . '/../views/admin/programmes.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    public function programmeCreate(Request $request, Response $response): Response
    {
        $allStaff = $this->adminModel->getAllStaff();
        $old      = $_SESSION['form_old'] ?? [];
        $errors   = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_old'], $_SESSION['form_errors']);
        ob_start();
        extract(['programme' => null, 'allStaff' => $allStaff, 'old' => $old, 'errors' => $errors]);
        require __DIR__ . '/../views/admin/programme_form.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    public function programmeStore(Request $request, Response $response): Response
    {
        $body   = (array)$request->getParsedBody();
        $errors = $this->validateProgramme($body);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $body;
            return $response->withHeader('Location', url('/admin/programmes/create'))->withStatus(302);
        }

        $this->programmes->create($body);
        $_SESSION['flash'] = ['type' => 'success', 'text' => 'Programme created successfully.'];
        return $response->withHeader('Location', url('/admin/programmes'))->withStatus(302);
    }

    public function programmeEdit(Request $request, Response $response, array $args): Response
    {
        $id        = (int)$args['id'];
        $programme = $this->programmes->getByIdAdmin($id);
        if (!$programme) {
            return $response->withStatus(404);
        }
        $allStaff = $this->adminModel->getAllStaff();
        $old      = $_SESSION['form_old'] ?? [];
        $errors   = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_old'], $_SESSION['form_errors']);
        ob_start();
        extract(['programme' => $programme, 'allStaff' => $allStaff, 'old' => $old, 'errors' => $errors]);
        require __DIR__ . '/../views/admin/programme_form.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    public function programmeUpdate(Request $request, Response $response, array $args): Response
    {
        $id   = (int)$args['id'];
        $body = (array)$request->getParsedBody();
        $errors = $this->validateProgramme($body);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $body;
            return $response->withHeader('Location', url('/admin/programmes/' . $id . '/edit'))->withStatus(302);
        }

        $this->programmes->update($id, $body);
        $_SESSION['flash'] = ['type' => 'success', 'text' => 'Programme updated successfully.'];
        return $response->withHeader('Location', url('/admin/programmes'))->withStatus(302);
    }

    public function programmeToggle(Request $request, Response $response, array $args): Response
    {
        $this->programmes->togglePublished((int)$args['id']);
        $_SESSION['flash'] = ['type' => 'success', 'text' => 'Programme status updated.'];
        return $response->withHeader('Location', url('/admin/programmes'))->withStatus(302);
    }

    public function programmeDelete(Request $request, Response $response, array $args): Response
    {
        $this->programmes->delete((int)$args['id']);
        $_SESSION['flash'] = ['type' => 'success', 'text' => 'Programme deleted.'];
        return $response->withHeader('Location', url('/admin/programmes'))->withStatus(302);
    }

    // ── Modules ───────────────────────────────────────────────────────────────

    public function modulesList(Request $request, Response $response): Response
    {
        $modules      = $this->modules->getAll();
        $flashMessage = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        ob_start();
        extract(['modules' => $modules, 'flashMessage' => $flashMessage]);
        require __DIR__ . '/../views/admin/modules.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    public function moduleCreate(Request $request, Response $response): Response
    {
        $allStaff = $this->adminModel->getAllStaff();
        $old      = $_SESSION['form_old'] ?? [];
        $errors   = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_old'], $_SESSION['form_errors']);
        ob_start();
        extract(['module' => null, 'allStaff' => $allStaff, 'old' => $old, 'errors' => $errors]);
        require __DIR__ . '/../views/admin/module_form.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    public function moduleStore(Request $request, Response $response): Response
    {
        $body   = (array)$request->getParsedBody();
        $errors = $this->validateModule($body);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $body;
            return $response->withHeader('Location', url('/admin/modules/create'))->withStatus(302);
        }

        $this->modules->create($body);
        $_SESSION['flash'] = ['type' => 'success', 'text' => 'Module created successfully.'];
        return $response->withHeader('Location', url('/admin/modules'))->withStatus(302);
    }

    public function moduleEdit(Request $request, Response $response, array $args): Response
    {
        $id     = (int)$args['id'];
        $module = $this->modules->getById($id);
        if (!$module) {
            return $response->withStatus(404);
        }
        $allStaff = $this->adminModel->getAllStaff();
        $old      = $_SESSION['form_old'] ?? [];
        $errors   = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_old'], $_SESSION['form_errors']);
        ob_start();
        extract(['module' => $module, 'allStaff' => $allStaff, 'old' => $old, 'errors' => $errors]);
        require __DIR__ . '/../views/admin/module_form.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    public function moduleUpdate(Request $request, Response $response, array $args): Response
    {
        $id   = (int)$args['id'];
        $body = (array)$request->getParsedBody();
        $errors = $this->validateModule($body);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $body;
            return $response->withHeader('Location', url('/admin/modules/' . $id . '/edit'))->withStatus(302);
        }

        $this->modules->update($id, $body);
        $_SESSION['flash'] = ['type' => 'success', 'text' => 'Module updated successfully.'];
        return $response->withHeader('Location', url('/admin/modules'))->withStatus(302);
    }

    public function moduleDelete(Request $request, Response $response, array $args): Response
    {
        $this->modules->delete((int)$args['id']);
        $_SESSION['flash'] = ['type' => 'success', 'text' => 'Module deleted.'];
        return $response->withHeader('Location', url('/admin/modules'))->withStatus(302);
    }

    // ── Mailing List ──────────────────────────────────────────────────────────

    public function mailingList(Request $request, Response $response): Response
    {
        $params            = $request->getQueryParams();
        $filterProgrammeId = isset($params['programme_id']) && $params['programme_id'] !== '' ? (int)$params['programme_id'] : null;
        $allProgrammes     = $this->programmes->getAll();

        $registrations = $filterProgrammeId
            ? $this->interests->getByProgramme($filterProgrammeId)
            : $this->interests->getAll();

        $flashMessage = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        ob_start();
        extract([
            'registrations'     => $registrations,
            'allProgrammes'     => $allProgrammes,
            'filterProgrammeId' => $filterProgrammeId,
            'flashMessage'      => $flashMessage,
        ]);
        require __DIR__ . '/../views/admin/mailing_list.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    public function exportCsv(Request $request, Response $response): Response
    {
        $params            = $request->getQueryParams();
        $filterProgrammeId = isset($params['programme_id']) && $params['programme_id'] !== '' ? (int)$params['programme_id'] : null;

        $registrations = $filterProgrammeId
            ? $this->interests->getByProgramme($filterProgrammeId)
            : $this->interests->getAll();

        $filename = 'mailing_list_' . date('Ymd_His') . '.csv';

        ob_start();
        $f = fopen('php://output', 'w');
        fputcsv($f, ['First Name', 'Last Name', 'Email', 'Phone', 'Programme', 'Registered At']);
        foreach ($registrations as $r) {
            fputcsv($f, [
                $r['first_name'],
                $r['last_name'],
                $r['email'],
                $r['phone'] ?? '',
                $r['programme_title'],
                $r['registered_at'],
            ]);
        }
        fclose($f);
        $csv = ob_get_clean();

        $response->getBody()->write($csv);
        return $response
            ->withHeader('Content-Type', 'text/csv; charset=utf-8')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function deleteRegistration(Request $request, Response $response, array $args): Response
    {
        $this->interests->deleteById((int)$args['id']);
        $_SESSION['flash'] = ['type' => 'success', 'text' => 'Registration removed.'];
        return $response->withHeader('Location', url('/admin/mailing-list'))->withStatus(302);
    }

    // ── Validation helpers ────────────────────────────────────────────────────

    private function validateProgramme(array $data): array
    {
        $errors = [];
        if (empty(trim($data['title'] ?? ''))) $errors['title'] = 'Title is required.';
        if (!in_array($data['level'] ?? '', ['Undergraduate', 'Postgraduate'], true)) $errors['level'] = 'Please select a valid level.';
        if (empty(trim($data['description'] ?? ''))) $errors['description'] = 'Description is required.';
        $dur = (int)($data['duration_years'] ?? 0);
        if ($dur < 1 || $dur > 6) $errors['duration_years'] = 'Duration must be between 1 and 6 years.';
        return $errors;
    }

    private function validateModule(array $data): array
    {
        $errors = [];
        if (empty(trim($data['title'] ?? ''))) $errors['title'] = 'Title is required.';
        $credits = (int)($data['credits'] ?? 0);
        if ($credits < 1 || $credits > 120) $errors['credits'] = 'Credits must be between 1 and 120.';
        return $errors;
    }
}
