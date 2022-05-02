<?php

namespace Charcoal\Redirect;

use Charcoal\Redirect\Model\Redirection;
use Charcoal\Redirect\Service\RedirectionService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Redirect Service Provider
 */
class RedirectServiceProvider implements ServiceProviderInterface
{

    /**
     * @param Container $container Pimple DI container.
     * @return void
     */
    public function register(Container $container)
    {
        $container['charcoal/redirection/service'] = function (Container $container) {
            $service = new RedirectionService([
                'redirections/class' => Redirection::class
            ]);
            return $service->setDependencies($container);
        };
    }
}
