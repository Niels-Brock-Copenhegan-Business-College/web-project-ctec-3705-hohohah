<?php
declare(strict_types=1);

namespace App\controllers;

use App\models\ProgrammeModel;
use App\models\ModuleModel;
use App\models\InterestModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StudentController
{
    private ProgrammeModel $programmes;
    private ModuleModel $modules;
    private InterestModel $interests;

    public function __construct()
    {
        $this->programmes = new ProgrammeModel();
        $this->modules    = new ModuleModel();
        $this->interests  = new InterestModel();
    }

    public function home(Request $request, Response $response): Response
    {
        $programmes = $this->programmes->getAllPublished();
        ob_start();
        extract(['programmes' => $programmes]);
        require __DIR__ . '/../views/student/home.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    public function programmes(Request $request, Response $response): Response
    {
        $params      = $request->getQueryParams();
        $search      = trim($params['search'] ?? '');
        $levelFilter = $params['level'] ?? '';
        $programmes  = $this->programmes->getAllPublished($search, $levelFilter);

        ob_start();
        extract(['programmes' => $programmes, 'search' => $search, 'levelFilter' => $levelFilter]);
        require __DIR__ . '/../views/student/programmes.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    public function programmeDetail(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $programme = $this->programmes->getById($id);

        if (!$programme) {
            $response->getBody()->write('<h1>404 - Programme not found</h1>');
            return $response->withStatus(404);
        }

        $modulesByYear = $this->modules->getByProgramme($id);

        // Collect unique staff (programme leader + module leaders)
        $staffMap = [];
        if ($programme['programme_leader_id']) {
            $staffMap[$programme['programme_leader_id']] = [
                'name'  => $programme['leader_name'],
                'email' => $programme['leader_email'],
                'bio'   => $programme['leader_bio'],
                'photo' => $programme['leader_photo'],
                'role'  => 'Programme Leader',
            ];
        }
        foreach ($modulesByYear as $yearModules) {
            foreach ($yearModules as $mod) {
                if ($mod['module_leader_id'] && !isset($staffMap[$mod['module_leader_id']])) {
                    $staffMap[$mod['module_leader_id']] = [
                        'name'  => $mod['leader_name'],
                        'email' => $mod['leader_email'],
                        'bio'   => $mod['leader_bio'],
                        'photo' => null,
                        'role'  => 'Module Leader',
                    ];
                }
            }
        }
        $staff = array_values($staffMap);

        $flash    = $_SESSION['flash'] ?? null;
        $errors   = $_SESSION['form_errors'] ?? [];
        $old      = $_SESSION['form_old'] ?? [];
        $alreadyRegistered = false;

        if (!empty($old['email'])) {
            $alreadyRegistered = $this->interests->isRegistered($id, $old['email']);
        }

        unset($_SESSION['flash'], $_SESSION['form_errors'], $_SESSION['form_old']);

        ob_start();
        extract([
            'programme'     => $programme,
            'modulesByYear' => $modulesByYear,
            'staff'         => $staff,
            'flashMessage'  => $flash,
            'errors'        => $errors,
            'old'           => $old,
            'alreadyRegistered' => $alreadyRegistered,
        ]);
        require __DIR__ . '/../views/student/programme_detail.php';
        $response->getBody()->write(ob_get_clean());
        return $response;
    }

    public function registerInterest(Request $request, Response $response, array $args): Response
    {
        $id   = (int)$args['id'];
        $body = (array)$request->getParsedBody();

        $errors = $this->validateInterest($body);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old']    = $body;
            return $response->withHeader('Location', url('/programmes/' . $id))->withStatus(302);
        }

        $body['programme_id'] = $id;
        $registered = $this->interests->register($body);

        if ($registered) {
            $_SESSION['flash'] = ['type' => 'success', 'text' => 'Thank you! Your interest has been registered. We will be in touch soon.'];
        } else {
            $_SESSION['flash'] = ['type' => 'warning', 'text' => 'You have already registered interest in this programme.'];
        }

        return $response->withHeader('Location', url('/programmes/' . $id))->withStatus(302);
    }

    public function withdrawInterest(Request $request, Response $response, array $args): Response
    {
        $id   = (int)$args['id'];
        $body = (array)$request->getParsedBody();
        $email = trim($body['email'] ?? '');

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $withdrawn = $this->interests->withdraw($id, $email);
            $_SESSION['flash'] = $withdrawn
                ? ['type' => 'success', 'text' => 'Your interest registration has been withdrawn.']
                : ['type' => 'warning', 'text' => 'No matching registration found for that email.'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'text' => 'Please enter a valid email address.'];
        }

        return $response->withHeader('Location', url('/programmes/' . $id))->withStatus(302);
    }

    private function validateInterest(array $data): array
    {
        $errors = [];
        if (empty(trim($data['first_name'] ?? ''))) {
            $errors['first_name'] = 'First name is required.';
        }
        if (empty(trim($data['last_name'] ?? ''))) {
            $errors['last_name'] = 'Last name is required.';
        }
        $email = trim($data['email'] ?? '');
        if (empty($email)) {
            $errors['email'] = 'Email address is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }
        return $errors;
    }
}
