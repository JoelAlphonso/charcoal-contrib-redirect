<?php

namespace Charcoal\Redirect;

use Charcoal\App\Module\AbstractModule;

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

        return $this;
    }
}
