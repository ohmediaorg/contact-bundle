# Installation

Update `composer.json` by adding this to the `repositories` array:

```json
{
    "type": "vcs",
    "url": "https://github.com/ohmediaorg/contact-bundle"
}
```

Then run `composer require ohmediaorg/contact-bundle:dev-main`.

Import the routes in `config/routes.yaml`:

```yaml
oh_media_contact:
    resource: '@OHMediaContactBundle/config/routes.yaml'
```

Run `php bin/console make:migration` then run the subsequent migration.
