<?php

namespace OHMedia\ContactBundle\Service;

use OHMedia\ContactBundle\Repository\LocationRepository;
use OHMedia\SettingsBundle\Service\Settings;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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

        $subjects = [];

        $recipient = $this->settings->get('contact_form_recipient');

        if ($recipient) {
            $subjects['General Inquiry'] = 'general';
        }

        $locations = $this->locationRepository->findAllOrdered();

        foreach ($locations as $location) {
            if (!$location->getEmail()) {
                continue;
            }

            $subjects[(string) $location] = 'location:'.$location->getId();
        }

        if (!$subjects) {
            return null;
        }

        $formBuilder->add('subject', ChoiceType::class, [
            'choices' => $subjects,
        ]);

        $formBuilder->add('message', TextareaType::class);

        return $formBuilder->getForm();
    }

    public function getSubjectEmail(string $subject): ?string
    {
        if ('general' === $subject) {
            return $this->settings->get('contact_form_recipient');
        }

        $parts = explode(':', $subject);

        if ('location' === $parts[0] && isset($parts[1])) {
            $location = $this->locationRepository->find($parts[1]);

            return $location ? $location->getEmail() : null;
        }
    }
}
