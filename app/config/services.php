<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use \Phalcon\Mvc\Dispatcher;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines(array(
        '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ));

            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));

    return $view;
}, true);

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        "charset" => $config->database->charset
    ));
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});


$di->set('config', function () use ($config) {
    return $config;
});


$di->set('dispatcher', function() use ($di) {

        $eventsManager = $di->getShared('eventsManager');

        $eventsManager->attach('dispatch:beforeException', function($event, $dispatcher, $exception) {
               
                switch ($exception->getCode()) {

                    case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                    case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                        $dispatcher->forward(
                            array(
                                'controller' => 'error',
                                'action' => 'notFound',
                            )
                        );
                        return false;
                        break; // for checkstyle
                    default:
                        $dispatcher->forward(
                            array(
                                'controller' => 'error',
                                'action' => 'uncaughtException',
                                'params' => array('exception' => $exception)
                            )
                        );
                        return false;
                        break; // for checkstyle
                }
            }
        );

        $dispatcher = new Dispatcher();
        $dispatcher->setEventsManager($eventsManager);

        return $dispatcher;
    }, true
);

$di->set('acl', function() {

        $acl = new \Phalcon\Acl\Adapter\Memory();
        $acl->setDefaultAction(Phalcon\Acl::DENY);

        $resources = CbkUserResources::find();
        foreach ( $resources as $resource ) {
            $acl->addResource($resource->controller, $resource->action);
        }

        $roles = CbkUserRole::find();
        foreach ( $roles as $role ) {
            $acl->addRole($role->name);

            $permissions = unserialize($role->permission);

            if ( is_array($permissions) ) {
                try {
                    foreach( $permissions as $perm ) {
                        if ( $perm['allow'] )
                            $acl->allow($role->name, $perm['controller'], $perm['action']);
                    }
                } catch( Exception $ex ) {
                    echo $ex->getMessage();
                    exit;
                }
            }
        }

        return $acl;
});

$di->set('user', function() use ($di) {

    $user = new \Lib\User($di->getShared('session'));

    return $user;
});


$di->set('validation', function() {

    return new \Phalcon\Validation();

});


$di->set('mail', function(){
    return new \Lib\Mail();
});

// Para personalizar urls :D

$di->set('router', function() {
    return require __DIR__ . '/routers.php';
});

$di->setShared('cookies', function () {
    $cookies = new Phalcon\Http\Response\Cookies();
    $cookies->useEncryption(false);
    return $cookies;
});

$di->setShared('crypt', function() use ($config) {
    $crypt = new Phalcon\Crypt();
    $crypt->setKey($config->application->crypt_key);
    return $crypt;
});