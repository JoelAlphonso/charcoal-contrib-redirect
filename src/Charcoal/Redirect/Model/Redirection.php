<?php

namespace Charcoal\Redirect\Model;

// From 'charcoal-cache'
use Charcoal\Cache\Facade\CachePoolFacade;

// From Pimple
use Charcoal\Redirect\Service\RedirectionService;
use Charcoal\Validator\ValidatorInterface;
use GuzzleHttp\Exception\ClientException;
use Pimple\Container;

// From 'charcoal-object'
use Charcoal\Object\Content;

/**
 * Object: Redirection
 */
class Redirection extends Content implements RedirectionInterface
{
    public const CACHE_KEY = 'Charcoal/Redirect/Model/Redirection';

    private CachePoolFacade $cache;
    protected ?string       $path             = '';
    protected ?string       $redirect         = '';
    protected ?bool         $redirectChildren = false;
    private string          $baseUrl;

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

        $this->baseUrl = $container['base-url'];
        $this->cache   = $container['cache/facade'];
    }

    /**
     * @param ValidatorInterface|null $v Optional. A custom validator object to use for validation.
     *                                   If null, use object's.
     * @return boolean
     * @throws ClientException When guzzle fails.
     */
    public function validate(ValidatorInterface &$v = null): bool
    {
        if ($this->id()) {
            if ((clone $this)->load($this->id())->getPath() !== $this->getPath()) {
                if (!$this->validateRouteDuplicates()) {
                    return false;
                }
            }
        } else {
            if (!$this->validateRouteDuplicates()) {
                return false;
            }
        }

        return parent::validate($v);
    }

    /**
     * @return boolean
     * @throws ClientException When guzzle fails.
     */
    private function validateRouteDuplicates(): bool
    {
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('GET', $this->baseUrl.trim($this->getPath(), '/[]'));
            $status   = $response->getStatusCode();
        } catch (ClientException $e) {
            $status = $e->getResponse()->getStatusCode();

            if ($status < 200 || $status >= 500) {
                throw $e;
            }
        }

        if (200 <= $status && $status < 400) {
            $this->validator()->error(
                (string)$this->translator()->translation([
                    'fr' => sprintf('Le CHEMIN [ %s ] existe déjà et ne peut être redirigé', $this->getPath()),
                    'en' => sprintf('The PATH [ %s ] already exist and cannot be redirected.', $this->getPath()),
                ])
            );

            return false;
        }

        return true;
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
