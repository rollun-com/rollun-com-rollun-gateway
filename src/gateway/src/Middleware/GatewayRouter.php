<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 12.12.17
 * Time: 18:56
 */

namespace rollun\gateway\Middleware;


use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class GatewayRouter if host is gateway-typed - start gateway pipe.
 * @package rollun\gateway\Middleware
 */
class GatewayRouter implements MiddlewareInterface
{
    /** @var  */
    protected $gatewayHostPattern;

    /** @var MiddlewareInterface */
    protected $gatewayPipe;

    /**
     * GatewayRouter constructor.
     * @param MiddlewareInterface $gatewayPipe
     * @param string $gatewayHostPattern
     */
    public function __construct(MiddlewareInterface $gatewayPipe, $gatewayHostPattern)
    {
        $this->gatewayPipe = $gatewayPipe;
        $this->gatewayHostPattern = $gatewayHostPattern;
    }

    /**
     * Start gateway MiddlewarePipeLine if resource host coincides to pattern.
     * Else return request to default way.
     *
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
        $host = $request->getUri()->getHost();
        if(preg_match($this->gatewayHostPattern, $host)) {
            return $this->gatewayPipe->process($request, $delegate);
        }
        return $delegate->process($request);
    }
}