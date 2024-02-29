<?php

namespace App\Service;

use DI\Attribute\Inject;
use Symfony\Component\HttpFoundation\Session\Session;

class FlashMessageService
{
    #[Inject()]
    private Session $session;

    /**
     * addSuccessMessage
     *
     * Adds a success (green) message to the Session global's flash bag.
     *
     * @link https://symfony.com/doc/current/session.html#flash-messages
     *
     * @param string $message
     * @return self
     */
    public function addSuccessMessage(string $message): self
    {
        $this->session->getFlashbag()->add('success', $message);

        return $this;
    }

    /**
     * addMessage
     *
     * Adds a message to the Session global's flash bag.
     *
     * @link https://symfony.com/doc/current/session.html#flash-messages
     *
     * @param string $message
     * @return self
     */
    public function addMessage(string $message): self
    {
        $this->session->getFlashbag()->add('info', $message);

        return $this;
    }

    /**
     * addErrorMessage
     *
     * Adds a success (green) message to the Session global's flash bag.
     *
     * @link https://symfony.com/doc/current/session.html#flash-messages
     *
     * @param string $message
     * @return self
     */
    public function addErrorMessage(string $message): self
    {
        $this->session->getFlashbag()->add('danger', $message);
        return $this;
    }

}
