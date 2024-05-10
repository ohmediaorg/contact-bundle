<?php

namespace OHMedia\ContactBundle\Twig;

use OHMedia\ContactBundle\Entity\Location;
use OHMedia\ContactBundle\Repository\LocationRepository;
use OHMedia\SettingsBundle\Service\Settings;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocationExtension extends AbstractExtension
{
    private array $schemas = [];

    public function __construct(
        private LocationRepository $locationRepository,
        private Settings $settings
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('location_main', [$this, 'locationMain']),
            new TwigFunction('locations', [$this, 'locations']),
            new TwigFunction('locations_schema', [$this, 'locationsSchema'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('location_schema', [$this, 'locationSchema'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function locationMain(): ?Location
    {
        return $this->locationRepository
            ->createQueryBuilder('l')
            ->where('l.main = 1')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function locations(): array
    {
        return $this->locationRepository->findBy([], [
            'ordinal' => 'asc',
        ]);
    }

    public function locationsSchema(): string
    {
        $locations = $this->locationRepository->findAll();

        $output = '';

        foreach ($locations as $location) {
            $output .= $this->locationSchema($location);
        }

        return $output;
    }

    public function locationSchema(?Location $location): string
    {
        if (!$location) {
            return '';
        }

        $id = $location->getId();

        if (isset($this->schemas[$id])) {
            return '';
        }

        $this->schemas[$id] = true;

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $location->getName(),
            'openingHours' => $location->getHoursSchema(),
            'address' => [[
                '@type' => 'PostalAddress',
                'addressLocality' => $location->getCity(),
                'addressRegion' => $location->getProvince(),
                'addressCountry' => $location->getCountry(),
                'streetAddress' => $location->getAddress(),
                'postalCode' => $location->getPostalCode(),
            ]],
        ];

        if ($email = $location->getEmail()) {
            $schema['email'] = $email;
        }

        if ($phone = $location->getPhone()) {
            $schema['telephone'] = $phone;
        }

        $organizationName = $this->settings->get('schema_organization_name');

        if ($organizationName) {
            $schema['parentOrganization'] = [
                '@type' => 'Organization',
                'name' => $organizationName,
            ];
        }

        return '<script type="application/ld+json">'.json_encode($schema).'</script>';
    }
}
