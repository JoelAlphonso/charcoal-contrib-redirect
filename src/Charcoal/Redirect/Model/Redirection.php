<?php

namespace Charcoal\Redirect\Model;

// From 'charcoal-cache'
use Charcoal\Cache\Facade\CachePoolFacade;

// From Pimple
use Pimple\Container;

// From 'charcoal-object'
use Charcoal\Object\Content;

/**
 * Object: Redirection
 */
class Redirection extends Content implements RedirectionInterface
{
    /**
     * Cache Namespace for Redirection
     */
    const CACHE_KEY = 'Charcoal/Redirect/Model/Redirection';

    /**
     * @var CachePoolFacade
     */
    private $cache;

    /**
     * @var ?string
     */
    protected ?string $path = '';

    /**
     * @var ?string
     */
    protected ?string $redirect = '';

    /**
     * @var boolean
     */
    protected ?bool $redirectChildren = false;

    /**
     * Return a new abstract section.
     *
     * @param array|null $data Dependencies.
     */
    public function __construct(array $data = null)
    {
        parent::__construct($data);

        $defaultData = $this->metadata()->defaultData();
        if ($defaultData) {
            $this->setData($defaultData);
        }
    }

    /**
     * @return string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getRedirect(): ?string
    {
        return $this->redirect;
    }

    /**
     * @return boolean
     */
    public function getRedirectChildren(): ?bool
    {
        return $this->redirectChildren;
    }

    /**
     * Inject dependencies from a DI Container.
     *
     * @param Container $container A Pimple DI service container.
     * @return void
     */
    protected function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->cache = $container['cache/facade'];
    }
    // Lifecycle events
    // ==========================================================================

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    protected function preSave()
    {
        $this->cache->delete(self::CACHE_KEY);

        return parent::preSave();
    }

    /**
     * {@inheritdoc}
     *
     * @param array $properties Optional properties to update.
     * @return boolean
     */
    protected function preUpdate(array $properties = null)
    {
        $this->cache->delete(self::CACHE_KEY);

        return parent::preUpdate($properties);
    }

    /**
     * Event called before _deleting_ the object.
     *
     * @return boolean
     * @see    \Charcoal\Model\AbstractModel::preDelete() For the "delete" Event.
     */
    protected function preDelete()
    {
        $this->cache->delete(self::CACHE_KEY);

        return parent::preDelete();
    }
}
