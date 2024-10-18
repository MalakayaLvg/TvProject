<?php

// src/Form/SeasonType.php

namespace App\Form;

use App\Entity\Season;
use App\Entity\Series;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre de la saison',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', null, [
                'label' => 'Description de la saison',
                'attr' => ['class' => 'form-control']
            ])
            ->add('publish_date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de publication',
                'attr' => ['class' => 'form-control']
            ])
            ->add('number', null, [
                'label' => 'Numéro de la saison',
                'attr' => ['class' => 'form-control']
            ])
            ->add('series', EntityType::class, [
                'class' => Series::class,
                'choice_label' => 'title',
                'label' => 'Série associée',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
