<?php

namespace App\Domain\Notification\Email\Service;

use Slim\Views\Twig;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\EventListener\MessageListener;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Twig\Extra\CssInliner\CssInlinerExtension;

class SendEmailNotificationService
{
    private Mailer $mailer;

    public function __construct(
        private string $dsn,
        private string $fromAddress,
        private Twig $twig,
    ) {
        $this->twig->addExtension(new CssInlinerExtension());
        $messageListener = new MessageListener(null, new BodyRenderer($this->twig->getEnvironment()));
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($messageListener);
        $this->mailer = new Mailer(Transport::fromDsn($this->dsn, $eventDispatcher), null, $eventDispatcher);
    }

    public function sendTemplateEmail(string $to, string $template, array $context)
    {
        $email = (new TemplatedEmail())
            ->to(self::createAddress($to))
            ->from($this->getFromAddress())
            ->htmlTemplate($template)
            ->context($context);
        $this->mailer->send($email);
    }

    private static function createAddress(string $address): Address
    {
        return new Address($address);
    }

    private function getFromAddress(): Address
    {
        return new Address($this->fromAddress);
    }

}
