<?php

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Fenrir\Authentication\Middlewares\AuthMiddleware;
use Fenrir\Authentication\Services\JwtService;
use Fenrir\Framework\MiddlewareCollection;
use Middlewares\CookieAuthMiddleware;

return [
    JwtService::class => function () {
        return new JwtService(
            key: $_ENV['JWT_SECRET'],
            alg: $_ENV['JWT_ALG']
        );
    },
    MiddlewareCollection::class => function () {
        return new MiddlewareCollection(            
            AuthMiddleware::class,
            CookieAuthMiddleware::class,
        );
    },
    Connection::class => function () {
        $dsn = (new DsnParser)->parse($_ENV['DATABASE_URL']);
        $config = new Configuration();
        $connection = DriverManager::getConnection($dsn, $config);
        return $connection;
    }
];
