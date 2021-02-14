<?php

namespace App\Form;

use App\Entity\Pigistes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PigistesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom :',
                'required' => true
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom :',
                'required' => true
            ])
            ->add('date_de_naissance', BirthdayType::class, [
                'label' => 'Date de naissance :',
                'required' => true,
                'widget' => 'single_text'
            ])
            ->add('lieu_de_naissace', TextType::class, [
                'label' => 'Lieu de naissance :',
                'required' => true
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse :',
                'required' => true
            ])
            ->add('code_postal', TextType::class, [
                'label' => 'Code postal :',
                'required' => true
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville :',
                'required' => true
            ])
            ->add('securite_sociale', TextType::class, [
                'label' => "Numéro S.S. :",
                'required' => true
            ])
            ->add('matricule', TextType::class, [
                'label' => "Numéro de matricule RF :",
                'required' => true
            ])
            ->add('carte_de_presse', TextType::class, [
                'label' => 'Numéro de carte de presse :',
                'required' => false,
                'help' => "Laisser libre si le pigiste n'a pas encore de carte de presse."
            ])
            ->add('date_carte_presse', DateType::class, [
                'label' => "Date d'obtention de la carte de presse :",
                'required' => false,
                'widget' => 'single_text',
                'help' => "Laisser libre si le pigiste n'a pas encore de carte de presse."
            ])
            ->add('code_emploi', TextType::class, [
                'label' => 'Code emploi :',
                'required' => true
            ])
            ->add('nationalite', TextType::class, [
                'label' => 'Nationalité :',
                'required' => true
            ])
            ->add('libelle', TextType::class, [
                'label' => 'Libellé emploi :',
                'required' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pigistes::class,
        ]);
    }
}
