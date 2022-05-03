<?php

namespace Charcoal\Redirect\Service;

use Charcoal\Loader\CollectionLoaderAwareTrait;
use Charcoal\Model\ModelFactoryTrait;
use Charcoal\Redirect\Model\RedirectionInterface;
use Pimple\Container;
use Psr\Log\LoggerAwareTrait;
use RuntimeException;

/**
 * Redirection Service
 */
class RedirectionService
{
    use ModelFactoryTrait;
    use CollectionLoaderAwareTrait;
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private string $redirectionsClass;
    /**
     * @var mixed
     */

    private RedirectionInterface $redirectionsProto;

    /**
     * @param array $data Init data array.
     */
    public function __construct(array $data)
    {
        $this->redirectionsClass = $data['redirections/class'];
    }

    /**
     * @param Container $container Pimple DI container.
     * @return self
     */
    public function setDependencies(Container $container): self
    {
        $this->setModelFactory($container['model/factory']);
        $this->setCollectionLoader($container['model/collection/loader']);
        $this->setLogger($container['logger']);

        return $this;
    }

    /**
     * Load redirection objects and return prepared array for APP routing.
     *
     * @return array
     */
    public function redirectionsAsRoutes(): array
    {
        $redirections = $this->loadRedirections();
        $out = [];

        foreach ($redirections as $redirection) {
            $path = '/'.trim($redirection['path'], '/');
            $redirect = '/'.ltrim($redirection['redirect'], '/');

            // Catchall children
            if ($redirection['redirectChildren']) {
                $path.='/{path:.*}';
            } else {
                $path.='[/]';
            }

            $out = array_replace($out, [
                $path => [
                    'methods'  => ['GET'],
                    'redirect' => $redirect,
                ]
            ]);
        }

        return $out;
    }

    /**
     * Load redirections
     *
     * This method doesn't check for an existing table to prevent overhead on the routing system.
     * Therefore, it will silently fail if no table exists which probably means that no redirections were created in
     * the first place.
     *
     * @return array
     */
    public function loadRedirections(): array
    {
        try {
            return $this->collectionLoader()->setModel($this->redirectionsClass)->load()->values();
        } catch (RuntimeException $e) {
            // log error.
            $this->logger->warning(
                sprintf('Unable to load redirections from database: %s', $e->getMessage())
            );

            return [];
        }
    }

    /**
     * @param array $redirections Redirections data structure array.
     * @return void
     */
    public function updateRedirections(array $redirections)
    {
        $this->createRedirectionsTable();

        foreach ($redirections as $data) {
            $model = clone $this->redirectionsProto();
            $model->setData($data);
            $id = $model->id();

            if ($id) {
                $model->load($id);

                if (!$model->id()) {
                    $this->logger->error(sprintf('Could no load redirection id: %s', $id));
                    continue;
                }
                $model->setData($data);
                $model->update();
            } else {
                $model->save();
            }
        }
    }

    /**
     * Create redirections table only if nonexistent.
     * To prevent some overhead in the routing system, this should only be called when trying to write redirections.
     *
     * @return void
     */
    protected function createRedirectionsTable()
    {
        $this->redirectionsProto()->source()->createTable();
    }

    /**
     * @return RedirectionInterface
     */
    protected function redirectionsProto(): RedirectionInterface
    {
        if (isset($this->redirectionsProto)) {
            return $this->redirectionsProto;
        }

        return $this->redirectionsProto = $this->modelFactory()->create($this->redirectionsClass);
    }
}
