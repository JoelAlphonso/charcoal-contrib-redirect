<?php

namespace Charcoal\Admin\Redirect\Template\System;

use Charcoal\Admin\AdminTemplate;
use Charcoal\Admin\Redirect\Widget\RedirectionsListWidget;
use Charcoal\Translator\Translation;
use Psr\Http\Message\RequestInterface;

/**
 * Redirect Template
 *
 * Admin template to configure site redirections
 */
class RedirectTemplate extends AdminTemplate
{
    /**
     * Retrieve the title of the page.
     *
     * @return Translation|string|null
     */
    public function title()
    {
        if ($this->title === null) {
            $this->setTitle($this->translator()->translation('Redirections'));
        }

        return $this->title;
    }

    /**
     * @return mixed
     * @throws \Exception When the widget factory is not defined.
     */
    public function redirectionsListWidget()
    {
        return $this->widgetFactory()->create(RedirectionsListWidget::class);
    }
}
