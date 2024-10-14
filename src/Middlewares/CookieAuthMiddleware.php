<?php

namespace Middlewares;

use Fenrir\Framework\Lib\Request;
use Fenrir\Framework\Middleware;

class CookieAuthMiddleware implements Middleware
{

    public function __construct(
        private Request $request
    ) {}
    public function execute(callable $next)
    {
        $access_token = trim($this->request->cookies->get('access_token', ''));        
        if ('' !== $access_token) {
            $this->request->headers->set('Authorization', $access_token);            
        }
        $next();
    }
}
