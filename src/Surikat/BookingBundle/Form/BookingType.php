<?php
namespace Surikat\BookingBundle\Form;
use Surikat\BookingBundle\Repository\TicketRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;


class BookingType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('bookingFor',  DateType::class, array(
    //              'input' => 'datetime',
                  'label'       => 'Date de Naissance: ',
                  'widget'      => 'choice',
                  'data'        => new \DateTime("tomorrow"),
    //              'format' => 'mm-dd-yyyy',
                  // do not render as type="date", to avoid HTML5 date pickers
    //              'html5'       => true,
                  // add a class that can be selected in JavaScript
              //    'attr' => ['class' => 'js-datepicker', 'placeholder'=>'Choisissez une date'],
                  'required'=>true,
                ))
                ->add('type', ChoiceType::class, array(
                  'choices' => array('Demi-Journée' => 'halfDay', 'Journée' => 'day'),
                               'expanded' => true,
                               'multiple' => false
                ))
                ->add('email',  EmailType::class, array(
                  'required'    => true
                ))
                ->add('tickets', CollectionType::class, array (
                  'entry_type'    => TicketType::class,
                  'allow_add'     => true,
                  'allow_delete'  => true
                  ))
                ->add('valider',    SubmitType::class, array('label' => '.'));
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Surikat\BookingBundle\Entity\Booking'
        ));
    }
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'surikat_bookingbundle_booking';
    }
}
