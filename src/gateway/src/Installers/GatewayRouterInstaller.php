<?php
/**
 * Created by PhpStorm.
 * User: victorynox
 * Date: 13.12.17
 * Time: 11:29
 */

namespace rollun\gateway\Installers;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use rollun\installer\Install\InstallerAbstract;
use rollun\gateway\ConfigProvider as ApiGatewayConfigProvider;
use rollun\gateway\Middleware\Factory\GatewayRouterFactory;

class GatewayRouterInstaller extends InstallerAbstract
{

    /**
     * install
     * @return array
     */
    public function install()
    {
        do {
            $pattern = "/^([\w\d_-]+)\.gw\.([\w\d-_]+)\.com$/";
            $pattern = $this->consoleIO->ask("Enter the gateway domain pattern. (By default use `$pattern`): ", $pattern);
        } while(!$this->consoleIO->askConfirmation("You enter `$pattern`, confirm ? ", true));

        return [
            GatewayRouterFactory::KEY =>  [
                GatewayRouterFactory::KEY_GATEWAY_HOST_PATTERN => $pattern,
                GatewayRouterFactory::KEY_GATEWAY_MIDDLEWARE_PIPE => ApiGatewayConfigProvider::API_GATEWAY_SERVICE
            ]
        ];
    }

    /**
     * Clean all installation
     * @return void
     */
    public function uninstall()
    {

    }

    /**
     * Return string with description of installable functional.
     * @param string $lang ; set select language for description getted.
     * @return string
     */
    public function getDescription($lang = "en")
    {
        switch ($lang) {
            case "ru":
                $description = "Создает конфиг для gateway router.";
                break;
            default:
                $description = "Does not exist.";
        }
        return $description;
    }

    /**
     * Return true if install, or false else
     * @return bool
     */
    public function isInstall()
    {
        try {
            $config = $this->container->get("config");
            return (
                in_array(GatewayRouterFactory::class, $config['dependencies']['factories']) &&
                isset($config[GatewayRouterFactory::KEY]) &&
                isset($config[GatewayRouterFactory::KEY][GatewayRouterFactory::KEY_GATEWAY_MIDDLEWARE_PIPE]) &&
                isset($config[GatewayRouterFactory::KEY][GatewayRouterFactory::KEY_GATEWAY_HOST_PATTERN])
            );
        } catch (NotFoundExceptionInterface | ContainerExceptionInterface $e) {
            return false;
        }
    }
}