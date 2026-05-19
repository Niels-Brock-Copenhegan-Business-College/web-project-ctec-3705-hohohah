<?php
declare(strict_types=1);

// Start session before anything else
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/helpers.php';

use Slim\Factory\AppFactory;
use App\controllers\StudentController;
use App\controllers\AdminController;
use App\middleware\AuthMiddleware;

$app = AppFactory::create();

// Auto-detect base path so Slim works in a subdirectory (e.g. /Final Project)
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = rtrim(str_replace('/public/index.php', '', $scriptName), '/');
$app->setBasePath($basePath);

// Make base path available globally for redirects and view links
define('APP_BASE', $basePath);

// Add routing middleware
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// ── Student-facing routes ─────────────────────────────────────────────────
$student = new StudentController();

$app->get('/', function ($req, $res) use ($student) {
    return $student->home($req, $res);
});

$app->get('/programmes', function ($req, $res) use ($student) {
    return $student->programmes($req, $res);
});

$app->get('/programmes/{id:[0-9]+}', function ($req, $res, $args) use ($student) {
    return $student->programmeDetail($req, $res, $args);
});

$app->post('/programmes/{id:[0-9]+}/interest', function ($req, $res, $args) use ($student) {
    return $student->registerInterest($req, $res, $args);
});

$app->post('/programmes/{id:[0-9]+}/withdraw', function ($req, $res, $args) use ($student) {
    return $student->withdrawInterest($req, $res, $args);
});

// ── Admin routes ──────────────────────────────────────────────────────────
$admin = new AdminController();
$auth  = new AuthMiddleware();

// Auth (public)
$app->get('/admin/login', function ($req, $res) use ($admin) {
    return $admin->loginForm($req, $res);
});
$app->post('/admin/login', function ($req, $res) use ($admin) {
    return $admin->login($req, $res);
});
$app->get('/admin/logout', function ($req, $res) use ($admin) {
    return $admin->logout($req, $res);
});

// Protected admin routes group
$app->group('/admin', function ($group) use ($admin) {

    $group->get('/dashboard', function ($req, $res) use ($admin) {
        return $admin->dashboard($req, $res);
    });

    // Programmes
    $group->get('/programmes', function ($req, $res) use ($admin) {
        return $admin->programmesList($req, $res);
    });
    $group->get('/programmes/create', function ($req, $res) use ($admin) {
        return $admin->programmeCreate($req, $res);
    });
    $group->post('/programmes/store', function ($req, $res) use ($admin) {
        return $admin->programmeStore($req, $res);
    });
    $group->get('/programmes/{id:[0-9]+}/edit', function ($req, $res, $args) use ($admin) {
        return $admin->programmeEdit($req, $res, $args);
    });
    $group->post('/programmes/{id:[0-9]+}/update', function ($req, $res, $args) use ($admin) {
        return $admin->programmeUpdate($req, $res, $args);
    });
    $group->post('/programmes/{id:[0-9]+}/toggle', function ($req, $res, $args) use ($admin) {
        return $admin->programmeToggle($req, $res, $args);
    });
    $group->post('/programmes/{id:[0-9]+}/delete', function ($req, $res, $args) use ($admin) {
        return $admin->programmeDelete($req, $res, $args);
    });

    // Modules
    $group->get('/modules', function ($req, $res) use ($admin) {
        return $admin->modulesList($req, $res);
    });
    $group->get('/modules/create', function ($req, $res) use ($admin) {
        return $admin->moduleCreate($req, $res);
    });
    $group->post('/modules/store', function ($req, $res) use ($admin) {
        return $admin->moduleStore($req, $res);
    });
    $group->get('/modules/{id:[0-9]+}/edit', function ($req, $res, $args) use ($admin) {
        return $admin->moduleEdit($req, $res, $args);
    });
    $group->post('/modules/{id:[0-9]+}/update', function ($req, $res, $args) use ($admin) {
        return $admin->moduleUpdate($req, $res, $args);
    });
    $group->post('/modules/{id:[0-9]+}/delete', function ($req, $res, $args) use ($admin) {
        return $admin->moduleDelete($req, $res, $args);
    });

    // Mailing list
    $group->get('/mailing-list', function ($req, $res) use ($admin) {
        return $admin->mailingList($req, $res);
    });
    $group->get('/mailing-list/export', function ($req, $res) use ($admin) {
        return $admin->exportCsv($req, $res);
    });
    $group->post('/mailing-list/{id:[0-9]+}/delete', function ($req, $res, $args) use ($admin) {
        return $admin->deleteRegistration($req, $res, $args);
    });

})->add($auth);

$app->run();
