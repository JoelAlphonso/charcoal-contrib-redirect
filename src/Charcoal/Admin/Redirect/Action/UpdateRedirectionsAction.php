<?php

namespace Charcoal\Admin\Redirect\Action;

use Charcoal\Admin\AdminAction;
use Charcoal\Redirect\Service\RedirectionService;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Update Redirections Action
 */
class UpdateRedirectionsAction extends AdminAction
{
    /**
     * @var RedirectionService
     */
    protected RedirectionService $redirectionService;

    /**
     * Set common dependencies used in all admin actions.
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
     * Gets a psr7 request and response and returns a response.
     *
     * Called from `__invoke()` as the first thing.
     *
     * @param RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParams();

        try {
            $validationArray = $this->redirectionService->validateRedirectionsForUpdate($data);

            if (!empty($validationArray)) {
                array_map(fn($r) => $this->addFeedback($r->level(), $r->message()), $validationArray);
                $this->setSuccess(false);

                return $response->withStatus(500);
            }

            $result = $this->redirectionService->updateRedirections($data);

            if (is_array($result)) {
                array_map(fn($r) => $this->addFeedback($r->level(), $r->message()), $result);
                $this->setSuccess(false);

                return $response->withStatus(500);
            } elseif ($result === false) {
                $this->addFeedback('error', 'Could not update redirections');
                $this->setSuccess(false);

                return $response->withStatus(500);
            }
        } catch (\Exception $exception) {
            $this->setSuccess(false);
            $this->addFeedback('error', 'Could not update redirections');

            return $response->withStatus(500);
        }

        $this->setSuccess(true);
        $this->addFeedback('success', 'Redirections updated successfully');

        return $response;
    }

    /**
     * Default response stub.
     *
     * @return array
     */
    public function results(): array
    {
        $results = parent::results();

        if ($this->success()) {
            $results['redirections'] = $this->redirectionService->loadRedirections();
        }

        return $results;
    }
}
