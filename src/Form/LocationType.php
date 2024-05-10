<?php

namespace OHMedia\ContactBundle\Form;

use OHMedia\ContactBundle\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $location = $options['data'];

        $builder->add('name');

        $builder->add('address');

        $builder->add('city');

        $builder->add('province');

        $builder->add('provinces', ChoiceType::class, [
            'label' => 'Province',
            'mapped' => false,
            'choices' => [
                'Saskatchewan' => 'SK',
                'Alberta' => 'AB',
                'British Columbia' => 'BC',
                'Manitoba' => 'MB',
                'New Brunswick' => 'NB',
                'Newfoundland and Labrador' => 'NL',
                'Northwest Territories' => 'NT',
                'Nova Scotia' => 'NS',
                'Nunavut' => 'NU',
                'Ontario' => 'ON',
                'Prince Edward Island' => 'PE',
                'Quebec' => 'QC',
                'Yukon' => 'YT',
            ],
        ]);

        $builder->add('states', ChoiceType::class, [
            'label' => 'State',
            'mapped' => false,
            'choices' => [
                'Alabama' => 'AL',
                'Alaska' => 'AK',
                'Arizona' => 'AZ',
                'Arkansas' => 'AR',
                'California' => 'CA',
                'Colorado' => 'CO',
                'Connecticut' => 'CT',
                'Delaware' => 'DE',
                'Florida' => 'FL',
                'Georgia' => 'GA',
                'Hawaii' => 'HI',
                'Idaho' => 'ID',
                'Illinois' => 'IL',
                'Indiana' => 'IN',
                'Iowa' => 'IA',
                'Kansas' => 'KS',
                'Kentucky' => 'KY',
                'Louisiana' => 'LA',
                'Maine' => 'ME',
                'Maryland' => 'MD',
                'Massachusetts' => 'MA',
                'Michigan' => 'MI',
                'Minnesota' => 'MN',
                'Mississippi' => 'MS',
                'Missouri' => 'MO',
                'Montana' => 'MT',
                'Nebraska' => 'NE',
                'Nevada' => 'NV',
                'New Hampshire' => 'NH',
                'New Jersey' => 'NJ',
                'New Mexico' => 'NM',
                'New York' => 'NY',
                'North Carolina' => 'NC',
                'North Dakota' => 'ND',
                'Ohio' => 'OH',
                'Oklahoma' => 'OK',
                'Oregon' => 'OR',
                'Pennsylvania' => 'PA',
                'Rhode Island' => 'RI',
                'South Carolina' => 'SC',
                'South Dakota' => 'SD',
                'Tennessee' => 'TN',
                'Texas' => 'TX',
                'Utah' => 'UT',
                'Vermont' => 'VT',
                'Virginia' => 'VA',
                'Washington' => 'WA',
                'West Virginia' => 'WV',
                'Wisconsin' => 'WI',
                'Wyoming' => 'WY',
            ],
        ]);

        $builder->add('country', CountryType::class, [
            'preferred_choices' => ['CA', 'US'],
        ]);

        $builder->add('postal_code');

        $builder->add('phone', TelType::class, [
            'required' => false,
        ]);

        $builder->add('email', EmailType::class, [
            'required' => false,
        ]);

        $builder->add('main', ChoiceType::class, [
            'label' => 'Is this the primary location?',
            'choices' => [
                'Yes' => true,
                'No' => false,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
