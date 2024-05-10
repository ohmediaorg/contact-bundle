<?php

namespace OHMedia\ContactBundle\Controller;

use OHMedia\ContactBundle\Security\Voter\SettingVoter;
use OHMedia\SettingsBundle\Entity\Setting;
use OHMedia\SettingsBundle\Service\Settings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactFormController extends AbstractController
{
    #[Route('/admin/settings/contact-form', name: 'settings_contact_form')]
    public function scripts(Request $request, Settings $settings): Response
    {
        $this->denyAccessUnlessGranted(
            SettingVoter::CONTACT_FORM,
            new Setting()
        );

        $formBuilder = $this->createFormBuilder();

        $formBuilder->add('recipient', EmailType::class, [
            'data' => $settings->get('contact_form_recipient'),
        ]);

        $formBuilder->add('message', TextareaType::class, [
            'data' => $settings->get('contact_form_message'),
        ]);

        $formBuilder->add('save', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            $settings->set('contact_form_recipient', $formData['recipient']);

            $settings->set('contact_form_message', $formData['message']);

            $this->addFlash('notice', 'Contact form settings updated successfully');

            return $this->redirectToRoute('settings_contact_form');
        }

        return $this->render('@OHMediaContact/settings/settings_contact_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
