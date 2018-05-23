<?php

chdir(dirname(__DIR__));
require 'vendor/autoload.php';
require_once 'config/env_configurator.php';

/**
 * Self-called anonymous function that creates its own scope and keep the global namespace clean.
 */
call_user_func(function () {
    /** @var \Interop\Container\ContainerInterface $container */
    $container = require 'config/container.php';
    \rollun\dic\InsideConstruct::setContainer($container);

    /** @var \Zend\Expressive\Application $app */
    $app = $container->get(\Zend\Expressive\Application::class);

    // Import programmatic/declarative middleware pipeline and routing
    // configuration statements
    require 'config/pipeline.php';

    $app->run();
});
