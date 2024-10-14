<?php

namespace Controllers;

use Exception;
use Fenrir\Authentication\Attributes\Auth;
use Fenrir\Authentication\Services\JwtService;
use Fenrir\Framework\Lib\Request;
use Fenrir\Framework\Lib\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;
use Twig\Environment;

class ExampleController
{

    public function __construct(
        private Request $request,
        private Response $response,
        private Environment $twig,
        private JwtService $jwt_service
    ) {}

    #[Route(path: '/')]
    public function index()
    {
        $body = $this->twig->render('index.html.twig', [
            'message' => "Hello World"
        ]);
        $this->response->setContent($body);
    }

    #[Route(path: '/protected')]
    #[Auth(redirect_url: 'login')]
    public function protectedRoute()
    {
        $body = $this->twig->render('protected.html.twig');
        $this->response->setContent($body);
    }

    #[Route(path: '/sign-in', methods: ['POST'])]
    public function signIn()
    {
        try {
            $login = $this->request->get('login', '');
            $password = $this->request->get('password', '');

            if ($login === 'admin' && $password === '12345') {
                $jwt_token = $this->jwt_service->encode([
                    'iat' => time(),
                    'exp' => time() + 3600,
                    'rule' => 'admin',
                    'acl' => ['admin:access'],
                    'sub' => 'admin',
                    'name' => "Administrator"
                ]);
                $this->response->headers->setCookie(new Cookie('access_token', $jwt_token, time() + 3600, '/', null, null, true));
                $this->response->json([
                    'success' => true
                ]);
                return;
            }

            throw new Exception("Username or password invalid");
        } catch (Throwable $th) {
            $this->response->setStatusCode(400);
            $this->response->json([
                'error' => $th->getMessage()
            ]);
        }
    }

    #[Route(path: 'sign-out')]
    public function signOut() {
        $this->response->headers->setCookie(new Cookie('access_token', null, -1000, '/', null, null, true));
        $this->response->headers->set('Location', $this->request->getBasePath().'/');
        $this->response->setStatusCode(307);
    }
}
