<?php
declare(strict_types=1);

namespace App\middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): Response
    {
        if (empty($_SESSION['admin_id'])) {
            $_SESSION['open_admin_modal'] = true;
            $response = new SlimResponse();
            return $response->withHeader('Location', url('/'))->withStatus(302);
        }
        return $handler->handle($request);
    }
}
