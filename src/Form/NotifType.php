<?php

namespace App\Form;

use App\Entity\Notif;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('consulted')
            ->add('message')
            ->add('date')
            ->add('type')
            ->add('recipient')
            ->add('sender')
            ->add('reservation')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Notif::class,
        ]);
    }
}
