<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 12.12.17
 * Time: 19:08
 */

namespace rollun\gateway\Middleware\Factory;


use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\gateway\Middleware\GatewayRouter;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class GatewayRouterFactory implements FactoryInterface
{

    const KEY = GatewayRouterFactory::class;

    const KEY_GATEWAY_MIDDLEWARE_PIPE = "keyGatewayMiddlewarePipe";

    const KEY_GATEWAY_HOST_PATTERN = "keyGatewayHostPattern";

    /**
     * Create an object
     * GatewayRouterFactory::KEY => [
     *      GatewayRouterFactory::KEY_GATEWAY_MIDDLEWARE_PIPE => ConfigProvider::API_GATEWAY_SERVICE_CONFIG ,
     *      GatewayRouterFactory::KEY_GATEWAY_HOST_PATTERN => '/^([\w\d_-]+)\.gw\.domain\.com$/',
     * ]
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        try {
            $config = $container->get("config");
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException("Can't get config from container.", $e->getCode(), $e);
        }
        if(!isset($config[static::KEY])) {
            throw new ServiceNotCreatedException("Not find factory config.");
        }
        $factoryConfig = $config[static::KEY];

        if(!isset($factoryConfig[static::KEY_GATEWAY_MIDDLEWARE_PIPE])) {
            throw new ServiceNotCreatedException("Not find factory ".static::KEY_GATEWAY_MIDDLEWARE_PIPE." config.");
        }
        try {
            $gatewayMiddlewarePipe = $container->get($factoryConfig[static::KEY_GATEWAY_MIDDLEWARE_PIPE]);
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            throw new ServiceNotCreatedException(
                "Can't find service GATEWAY_MIDDLEWARE_PIPE[".
                $factoryConfig[static::KEY_GATEWAY_MIDDLEWARE_PIPE].
                "] from container.", $e->getCode(), $e);
        }

        if(!isset($factoryConfig[static::KEY_GATEWAY_HOST_PATTERN])){
            throw new ServiceNotCreatedException("Not find factory ".static::KEY_GATEWAY_HOST_PATTERN." config.");
        }

        return new GatewayRouter($gatewayMiddlewarePipe, $factoryConfig[static::KEY_GATEWAY_HOST_PATTERN]);
    }
}