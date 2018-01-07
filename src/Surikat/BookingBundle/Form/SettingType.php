<?php

namespace Surikat\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class SettingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
     public function buildForm(FormBuilderInterface $builder, array $options)
     {
         $builder
         ->add('configName',  TextType::class, array(
           'label' => 'Nom de la Configuration: '
         ))
         ->add('dailyTicketLimit',  NumberType::class, array(
           'label' => 'Limite Journalière: '
         ))
         ->add('dayTicketHourLimit', TimeType::class, array(
           'label'       => 'Demi-journée: A partir de '
         ))
         ->add('freePrice', MoneyType::class, array(
           'label'       => 'Prix Gratuit: '
         ))
         ->add('freePriceCondition', NumberType::class, array(
           'label'       => 'Condition pour Prix Gratuit: '
         ))
         ->add('childPrice', MoneyType::class, array(
           'label'       => 'Prix Enfant: '
         ))
         ->add('childPriceCondition', NumberType::class, array(
           'label'       => 'Condition pour Prix Enfant: '
         ))
         ->add('normalPrice', MoneyType::class, array(
           'label'       => 'Prix Normal: '
         ))
         ->add('normalPriceCondition', NumberType::class, array(
           'label'       => 'Condition pour Prix Normal: '
         ))
         ->add('seniorPrice', MoneyType::class, array(
           'label'       => 'Prix Senior: '
         ))
         ->add('seniorPriceCondition', NumberType::class, array(
           'label'       => 'Condition pour Prix Senior: '
         ))
         ->add('specialPrice',  MoneyType::class, array(
           'label'       => 'Tarif Réduit: '
         ))
      //   ->add('closedDays', TextType::class, array(
      //   ))
         ->add('closedDays', CollectionType::class, array(
           'entry_type' => DateType::class,
           'entry_options'  => array(
             'input' => 'string',
           ),
           'allow_add' => true,
           'prototype' => true,
           'label_attr'  => array(
             'class'     => 'col-sm-4'
         )
         ))

         ->add('closedWeekDays', ChoiceType::class, array(
           'choices'  => array(
             'Lundi' => 1,
             'Mardi' => 2,
             'Mercredi' => 3,
             'Jeudi' => 4,
             'Vendredi' => 5,
             'Samedi' => 6,
             'Dimanche' => 7,
           ),
           'label'       => 'Jour de Fermeture dans la Semaine: ',
           'required'=>false,
           'expanded'=>true,
           'multiple'=>true,
         ))
         ->add('valider',    SubmitType::class, array('label' => 'Valider'));

     }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Surikat\BookingBundle\Entity\Setting'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'surikat_bookingbundle_setting';
    }


}
