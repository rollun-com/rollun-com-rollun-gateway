<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12.09.17
 * Time: 16:24
 */

namespace rollun\gateway\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\gateway\RuntimeException;

class ServiceResolver implements MiddlewareInterface
{
    const DEFAULT_GATEWAY_SUB_DOMAIN = "gw";

    const ATTR_SERVICE_NAME = "serviceName";

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     * @throws RuntimeException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $host = $request->getUri()->getHost();

        $serviceName = $this->getServiceName($host);
        $request = $request->withAttribute(static::ATTR_SERVICE_NAME, $serviceName);
        $response = $delegate->process($request);
        return ($response);
    }

    /**
     * @param $host
     * @return string
     * @throws RuntimeException
     */
    protected function getServiceName($host)
    {
        $pattern = '/(?<name>[\w_-]+)\.' . static::DEFAULT_GATEWAY_SUB_DOMAIN . '/';
        if (!preg_match($pattern, $host, $math)) {
            throw new RuntimeException("$host is not service");
        }
        return ($math['name']);
    }
}
