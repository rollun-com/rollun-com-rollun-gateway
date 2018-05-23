<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 13.09.17
 * Time: 11:42
 */

namespace rollun\gateway\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\Serializer;
use Zend\Http\Response;

class ResponseDecoder implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /** @var Response $serviceResponse */
        $serviceResponse = $request->getAttribute(RequestSender::ATTR_SERVICE_RESPONSE);

        $response = Serializer::fromString($serviceResponse->toString());

        return $response;
    }
}
