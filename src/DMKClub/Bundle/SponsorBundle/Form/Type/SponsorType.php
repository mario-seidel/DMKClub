<?php
namespace DMKClub\Bundle\SponsorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SponsorType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildPlainFields($builder, $options);
        $this->buildRelationFields($builder, $options);
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected function buildPlainFields(FormBuilderInterface $builder, array $options) {
    	$builder

        ->add('name', 'text', array('required' => true, 'label' => 'dmkclub.member.name.label'))

    	->add('isActive')
    	->add('owner')
    	->add('organization')
    	;
        $builder->add(
            'contact',
            'orocrm_contact_select',
            [
                'label'    => 'orocrm.sales.b2bcustomer.contact.label',
                'required' => true,
            ]
        );
        $builder->add(
            'account',
            'orocrm_account_select',
            [
                'label'    => 'orocrm.sales.b2bcustomer.account.label',
                'required' => true,
            ]
        );
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildRelationFields(FormBuilderInterface $builder, array $options){
        // tags removed in 1.9
        //$builder->add('tags', 'oro_tag_select', array('label' => 'oro.tag.entity_plural_label'));

        $builder->add(
        		'dataChannel',
        		'orocrm_channel_select_type',
        		[
        		'required' => true,
        		'label'    => 'orocrm.sales.b2bcustomer.data_channel.label',
        		'entities' => [
	        		'DMKClub\\Bundle\\SponsorBundle\\Entity\\Sponsor'
	        		],
        		]
        );

        // sponsor categories
        $builder->add(
        		'category',
        		'dmkclub_sponsorcategory_select',
        		array(
                   'label'    => 'dmkclub.sponsor.category.entity_label',
                   'required' => false,
        		)
        );
        $builder->add(
        		'billingAddress',
        		'oro_address',
        		[
        		'cascade_validation' => true,
        		'required'           => false
        		]
        );
        $builder->add(
        		'postalAddress',
        		'oro_address',
        		[
        				'cascade_validation' => true,
        				'required'           => false
        		]
        );

    }
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'DMKClub\Bundle\SponsorBundle\Entity\Sponsor',
                'cascade_validation' => true,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dmkclub_sponsor_sponsor';
    }
}
