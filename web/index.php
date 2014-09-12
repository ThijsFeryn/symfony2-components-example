<?php
ini_set('display_errors','on');
error_reporting(E_ALL);

require dirname(__DIR__).'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

$twigLoader = new Twig_Loader_Filesystem(dirname(__DIR__).'/templates');
$twig = new Twig_Environment($twigLoader);
$template = $twig->loadTemplate('index.html');

$routes = new RouteCollection();
$routes->add(
    'home',
    new Route(
        '/',
        array(
            '_controller' =>
            function () use($template) {
                return new Response($template->render(array('content'=>'Welcome home')));
            }
        )
    )
);

$routes->add(
    'hello',
    new Route(
        '/hello/{name}',
        array(
            '_controller' =>
                function (Request $request) use ($template) {
                    return new Response($template->render(array('content'=>sprintf("Hello %s", $request->get('name')))));
                },
            'name'=>''
        ),
        array(
            'name'=>'.*'
        )
    )
);

$request = Request::createFromGlobals();

$context = new RequestContext();
$context->fromRequest($request);

$matcher = new UrlMatcher($routes, $context);

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new RouterListener($matcher));

$resolver = new ControllerResolver();

$kernel = new HttpKernel($dispatcher, $resolver);

$kernel->handle($request)->send();