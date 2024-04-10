<?php

namespace App\Consumer;

use App\Domain\User\Data\PasswordResetToken;
use Dotenv\Dotenv;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\Mime\BodyRenderer;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Mailer\EventListener\MessageListener;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Twig\Environment;
use Twig\Extra\CssInliner\CssInlinerExtension;
use Twig\Loader\FilesystemLoader;

class EmailNotificationConsumer
{
    private Environment $twig;
    private Mailer $mailer;
    private string $fromAddress;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__)."/../");
        $dotenv->load();
        $dotenv->required(['APP_SECRET'])->notEmpty();
        $dotenv->required(['APP_URL'])->notEmpty();
        $settings = require(dirname(__DIR__)."/../app/settings.php");
        $this->twig = $this->setUpTwig($settings);
        $this->mailer = $this->setUpMailer(
            $settings['mail']['dsn']
        );
        $this->fromAddress = $settings['mail']['fromAddress'];
    }

    public function consume(AMQPMessage $message)
    {
        $message->getRoutingKey();
        $data = unserialize($message->getBody());
        switch ($message->getRoutingKey()) {
            case 'email.resetPassword':
                $this->sendPasswordResetEmail($data);
                break;
        }
    }

    private function sendPasswordResetEmail(PasswordResetToken $token)
    {
        $this->sendTemplateEmail(
            $token->getUser()->getEmail(),
            'Reset your IMS password',
            'resetPassword.html.twig',
            ['token' => $token->getClearTextToken()]
        );
    }

    private function sendTemplateEmail(string $to, string $subject, string $template, array $context)
    {
        $email = (new TemplatedEmail())
            ->to(self::createAddress($to))
            ->from($this->getFromAddress())
            ->subject($subject)
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

    private function setUpTwig(array $settings): Environment
    {
        $loader = new FilesystemLoader(dirname(__DIR__)."/../templates/email");
        $twig = new Environment($loader, [
            'cache_path' => dirname(__DIR__)."/../tmp/twig",
            'cache_enabled' => $settings['debug']
        ]);
        $twig->getExtension(\Twig\Extension\CoreExtension::class)->setDateFormat(
            $settings['application']['date_format'],
            $settings['application']['interval_format']
        );
        $twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone($settings['application']['timezone']);
        $twig->addExtension(new CssInlinerExtension());
        $twig->addGlobal('app_url', $settings['url']);
        return $twig;
    }

    private function setUpMailer(string $dsn): Mailer
    {
        $messageListener = new MessageListener(null, new BodyRenderer($this->twig));
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($messageListener);
        return new Mailer(
            Transport::fromDsn($dsn, $eventDispatcher),
            null,
            $eventDispatcher
        );
    }

}
