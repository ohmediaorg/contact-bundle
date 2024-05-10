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
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class ContactForm
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private LocationRepository $locationRepository,
        private Settings $settings
    ) {
    }

    public function buildForm(): ?FormInterface
    {
        $formBuilder = $this->formFactory->createBuilder(FormType::class);

        $formBuilder->add('name');

        $formBuilder->add('email', EmailType::class);

        $formBuilder->add('phone', TelType::class);

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

            $recipients[(string) $location] = 'location:'.$location->getId();
        }

        if (!$recipients) {
            return null;
        }

        $formBuilder->add('recipient', ChoiceType::class, [
            'choices' => $recipients,
        ]);

        $formBuilder->add('message', TextareaType::class);

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
}
