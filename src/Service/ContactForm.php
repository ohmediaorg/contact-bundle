<?php

namespace OHMedia\ContactBundle\Service;

use OHMedia\AntispamBundle\Form\Type\CaptchaType;
use OHMedia\ContactBundle\Repository\LocationRepository;
use OHMedia\SettingsBundle\Service\Settings;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ContactForm
{
    public const DEFAULT_SUCCESS_MESSAGE = 'Thanks for your submission. We will get back to you as soon as we can.';

    public function __construct(
        private FormFactoryInterface $formFactory,
        private LocationRepository $locationRepository,
        private Settings $settings
    ) {
    }

    public function buildForm(): ?FormInterface
    {
        $formBuilder = $this->formFactory->createBuilder(FormType::class);

        $recipients = [];

        $recipient = $this->settings->get('contact_form_recipient');

        if ($recipient) {
            $recipients['General Inquiry'] = 'general';
        }

        $locations = $this->locationRepository->findAllOrdered();

        foreach ($locations as $location) {
            if (!$location->getEmail()) {
                continue;
            }

            $recipients[(string) $location.' Location'] = 'location:'.$location->getId();
        }

        if (!$recipients) {
            return null;
        }

        $formBuilder->add('recipient', ChoiceType::class, [
            'choices' => $recipients,
        ]);

        $formBuilder->add('name', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(null, 'Please fill out your name.'),
            ],
        ]);

        $formBuilder->add('email', EmailType::class, [
            'constraints' => [
                new Assert\NotBlank(null, 'Please fill out your email.'),
                new Assert\Email(
                    null,
                    'Please enter a valid email address.'
                ),
            ],
        ]);

        $formBuilder->add('phone', TelType::class, [
            'constraints' => [
                new Assert\NotBlank(null, 'Please fill out your phone number.'),
            ],
        ]);

        $formBuilder->add('message', TextareaType::class, [
            'attr' => [
                'maxlength' => 1000,
                'rows' => 5,
            ],
            'constraints' => [
                new Assert\NotBlank(null, 'Please enter a message.'),
                new Assert\Length(
                    null,
                    null,
                    1000,
                    null,
                    null,
                    null,
                    null,
                    null,
                    'Please enter a message of 1000 characters or less.'
                ),
            ],
        ]);

        $formBuilder->add('captcha', CaptchaType::class);

        $formBuilder->add('submit', SubmitType::class);

        return $formBuilder->getForm();
    }

    public function getRecipientEmail(string $recipient): ?string
    {
        if ('general' === $recipient) {
            return $this->settings->get('contact_form_recipient');
        }

        $parts = explode(':', $recipient);

        if ('location' === $parts[0] && isset($parts[1])) {
            $location = $this->locationRepository->find($parts[1]);

            return $location ? $location->getEmail() : null;
        }
    }

    public function getSuccessMessage(): string
    {
        $successMessage = $this->settings->get('contact_form_message');

        return $successMessage ?: self::DEFAULT_SUCCESS_MESSAGE;
    }
}
