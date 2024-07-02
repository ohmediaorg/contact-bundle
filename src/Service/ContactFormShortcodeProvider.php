<?php

namespace OHMedia\ContactBundle\Service;

use OHMedia\BackendBundle\Shortcodes\AbstractShortcodeProvider;
use OHMedia\BackendBundle\Shortcodes\Shortcode;

class ContactFormShortcodeProvider extends AbstractShortcodeProvider
{
    public function getTitle(): string
    {
        return 'Contact Form';
    }

    public function buildShortcodes(): void
    {
        $this->addShortcode(new Shortcode(
            'Contact Form',
            'contact_form()'
        ));
    }
}
