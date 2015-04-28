<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
//print_r($config->application->controllersDir);
$loader->registerDirs(
    array(
        $config->application->controllersDir,
        $config->application->modelsDir,
	)
)->registerNamespaces(
	array(
		'Lib'=>$config->application->libDir
	)
)->register();
