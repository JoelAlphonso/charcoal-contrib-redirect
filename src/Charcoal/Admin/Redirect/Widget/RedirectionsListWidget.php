<?php

namespace Charcoal\Admin\Redirect\Widget;

use Charcoal\Admin\AdminWidget;
use Charcoal\Redirect\Service\RedirectionService;
use Pimple\Container;

/**
 * Class RedirectionsListWidget
 */
class RedirectionsListWidget extends AdminWidget
{
    /**
     * @var RedirectionService
     */
    private RedirectionService $redirectionService;

    /**
     * Set common dependencies used in all admin widgets.
     *
     * @param Container $container DI Container.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->redirectionService = $container['charcoal/redirection/service'];
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return 'charcoal/admin/redirect/widget/redirections-list';
    }

    /**
     * @return string
     */
    public function redirections(): string
    {
        return json_encode($this->redirectionService->loadRedirections());
    }

    /**
     * @return string
     */
    public function objectType(): string
    {
        return $this->redirectionService->redirectionsObjType();
    }
}
