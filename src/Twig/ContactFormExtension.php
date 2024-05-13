<?php

namespace OHMedia\ContactBundle\Twig;

use OHMedia\ContactBundle\Service\ContactForm;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContactFormExtension extends AbstractExtension
{
    public function __construct(
        private ContactForm $contactForm)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('contact_form', [$this, 'form'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function form(Environment $twig): string
    {
        $form = $this->contactForm->buildForm();

        if (!$form) {
            return '';
        }

        return $twig->render('@OHMediaContact/contact_form.html.twig', [
            'form' => $form->createView(),
            'success_message' => $this->contactForm->getSuccessMessage(),
        ]);
    }
}
