<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12.09.17
 * Time: 18:33
 */

namespace rollun\gateway\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rollun\mesh\MeshHttpClient;
use Zend\Http\Request;

class RequestSender implements MiddlewareInterface
{
    const ATTR_SERVICE_RESPONSE = "serviceResponse";

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
        /** @var Request $sendRequest */
        $sendRequest = $request->getAttribute(RequestResolver::ATTR_SEND_REQUEST);

        $client = new MeshHttpClient(null, [
            'timeout' => 90,
        ]);
        $response = $client->send($sendRequest);
        $request = $request->withAttribute(static::ATTR_SERVICE_RESPONSE, $response);
        $response = $delegate->process($request);
        return $response;
    }
}
