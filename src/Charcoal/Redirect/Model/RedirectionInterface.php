<?php

namespace Charcoal\Redirect\Model;

use Charcoal\Object\ContentInterface;

/**
 * Interface: RedirectionInterface
 * @package Charcoal\Redirect\Model
 */
interface RedirectionInterface extends ContentInterface
{
    /**
     * @return string
     */
    public function getPath(): ?string;

    /**
     * @return string
     */
    public function getRedirect(): ?string;

    /**
     * @return boolean
     */
    public function getRedirectChildren(): ?bool;
}
