<?php

namespace Charcoal\Redirect;

use Charcoal\App\Module\AbstractModule;
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

        array_map(
            fn($redirect) => $this->app()->redirect(...array_values($redirect)),
            $redirectionService->redirectionsAsRedirect()
        );

        return $this;
    }
}
