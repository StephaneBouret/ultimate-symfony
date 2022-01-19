<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendEmail'
        ];
    }

    public function sendEmail(ProductViewEvent $productViewEvent)
    {
        // $email = new TemplatedEmail();
        // $email->from(new Address("contact@mail.com", "Infos de la boutique"))
        //     ->to("admin@mail.com")
        //     ->text("Un visiteur est en train de voir le produit n°" . $productViewEvent->getProduct()->getId())
        //     // ->html("<h1>Visite du produit n°{$productViewEvent->getProduct()->getId()}</h1>")
        //     // path of the Twig template to render
        //     ->htmlTemplate('emails/product_view.html.twig')
        //     // pass variables (name => value) to the template
        //     ->context([
        //         'product' => $productViewEvent->getProduct(),
        //     ])
        //     ->subject("Visite du produit n°" . $productViewEvent->getProduct()->getId());

        // $this->mailer->send($email);

        $this->logger->info("Email envoyé à l'admin pour le produit n°" . $productViewEvent->getProduct()->getId());
    }
}
