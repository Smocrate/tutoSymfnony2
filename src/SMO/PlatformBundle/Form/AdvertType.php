<?php
// src/SMO/PlatformBundle/Form/AdvertType.php

namespace SMO\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdvertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date',       'datetime')
            ->add('title',      'text')
            ->add('author',     'text')
            ->add('content',    'textarea')
#            ->add('content',    'ckeditor')
            ->add('image',      new ImageType(), array('required' => false))
            ->add('categories', 'entity',   array(
                'class'     => 'SMOPlatformBundle:Category',
                'property'  => 'name',
                'multiple'  => true,
                'expanded'  => false,
            ))
            ->add('save',       'submit')
        ;
        
        // on va écouter un évènement
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event)
            {
                $advert = $event->getData();
                if(null===$advert)
                {
                    return;
                }
                if(!$advert->getPublished() || null === $advert->getId())
                {
                    $event
                        ->getForm()
                        ->add('published', 'checkbox', array('required' => false))
                    ;
                } else 
                {
                    $event
                        ->getForm()
                        ->remove('published')
                    ;
                }
            }
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SMO\PlatformBundle\Entity\Advert'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'smo_platformbundle_advert';
    }
}
