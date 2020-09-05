<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

if(isset($_SERVER['HTTP_CLIENT_IP']) || !(in_array($_SERVER['HTTP_HOST'], ['0.0.0.0:8080', 'plansv4.ggs-svb.com'], true))){
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require __DIR__.'/../vendor/autoload.php';
Debug::enable();

if (PHP_VERSION_ID < 70000) {
    include_once __DIR__.'/../var/bootstrap.php.cache';
}

$kernel = new AppKernel('demo', true);
if (PHP_VERSION_ID < 70000) {
    $kernel->loadClassCache();
}

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
