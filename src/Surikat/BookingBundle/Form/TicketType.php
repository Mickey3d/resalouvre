<?php
namespace Surikat\BookingBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DatetimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;


class TicketType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name',  TextType::class, array(
          'label'       => 'Prénom: ',
          'label_attr'  => array(
            'class'     => 'md-form-control col-sm-3'
        )))
        ->add('surname',  TextType::class, array(
          'label'       => 'Nom: ',
          'label_attr'  => array(
            'class'     => 'md-form-control col-sm-3'
        )))
        ->add('birthdate',  DateType::class, array(
          'label'       => 'Date de Naissance: ',
          'label_attr'  => array(
            'class'     => 'col-sm-3'),
          'widget'      => 'single_text',
          'format' => 'dd-mm-yyyy',
          // do not render as type="date", to avoid HTML5 date pickers
          'html5'       => false,
          // add a class that can be selected in JavaScript
          'attr' => ['class' => 'js-datepicker'],
          'required'=>true,
        ))
        ->add('country',  CountryType::class, array(
          'label'       => 'Nationalité: ',
          'label_attr'  => array(
            'class'     => 'col-sm-3'),
          'placeholder' => '',
          'multiple'    => false,
          'required'    => true,
          'preferred_choices' => array('FR','DE','GB','IT','ES','PT','RU','CN','JP','CA','US','BR','AU')
        ))
        /*
        ->add('email',  EmailType::class, array(
          'required'    => false
        ))
        ->add('info',  TextAreaType::class, array(
          'required'    => false
        ))
        */
        ->add('specialPrice',  CheckboxType::class, array(
          'required'    => false,
           'label'       => false
        ));

    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Surikat\BookingBundle\Entity\Ticket'
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'surikat_bookingbundle_ticket';
    }
}
