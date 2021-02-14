<?php

namespace App\Form;

use App\Entity\Contrats;
use App\Entity\Pigistes;
use App\Entity\Tarifs;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContratsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_debut', DateType::class, [
                'label' => "Date de début :",
                'required' => true,
                'widget' => 'single_text'
            ])
            ->add('date_fin', DateType::class, [
                'label' => "Date de fin :",
                'required' => true,
                'widget' => 'single_text'])
            ->add('centre_de_cout', TextType::class, [
                'label' => 'Centre de coût :',
                'required' => true
            ])
            ->add('motif', TextType::class, [
                'label' => 'Motif :',
                'required' => true
            ])
            ->add('pigiste', EntityType::class, [
                'class' => Pigistes::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->andWhere('p.active = 1')
                        ->orderBy('p.nom', 'ASC');
                },
                'choice_label' => function ($pigiste) {
                    return $pigiste->getPrenom() . " " . $pigiste->getNom();
                },
                'multiple' => false,
                'expanded' => false,
                'placeholder' => "Choisir un pigiste"
            ])
            ->add('tarif', EntityType::class, [
                'class' => Tarifs::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.id', 'ASC');
                },
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => "Choisir un tarif"
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Générer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contrats::class,
        ]);
    }
}
