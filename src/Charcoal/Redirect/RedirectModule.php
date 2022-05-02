<?php

namespace Charcoal\Redirect;

use Charcoal\App\Module\AbstractModule;
use Charcoal\App\Route\RouteManager;
use Charcoal\Redirect\Service\RedirectionService;

/**
 * Redirect Module
 */
class RedirectModule extends AbstractModule
{
    const ADMIN_CONFIG = 'vendor/locomotivemtl/charcoal-contrib-redirect/config/admin.json';
    const APP_CONFIG = 'vendor/locomotivemtl/charcoal-contrib-redirect/config/config.json';

    /**
     * Setup the module's dependencies.
     *
     * @return self
     */
    public function setup(): self
    {
        $container = $this->app()->getContainer();

        $provider = new RedirectServiceProvider();
        $container->register($provider);

        $this->setupRoutes();

        return $this;
    }

    /**
     * Set up the module's routes, via a RouteManager
     *
     * @return self
     */
    public function setupRoutes(): self
    {
        $container = $this->app()->getContainer();
        /** @var RedirectionService $redirectionService */
        $redirectionService = $container['charcoal/redirection/service'];

        if (!isset($this->routeManager)) {
            $this->routeManager = new RouteManager([
                'config' => [
                    'templates' => $redirectionService->redirectionsAsRoutes()
                ],
                'app'    => $this->app(),
                'logger' => $this->logger
            ]);

            $this->routeManager->setupRoutes();
        }

        return $this;
    }
}
