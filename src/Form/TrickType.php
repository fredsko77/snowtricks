<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\Trick;
use App\Repository\GroupRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{

    /**
     * @var GroupRepository $groupRepository
     */
    private $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('poster', FileType::class, [
                'required' => false,
                'multiple' => false,
                'label' => 'Télécharger une image à la une',
                'mapped' => false,
                'attr' => [
                    'class' => 'hidden',
                ],
            ])
            ->add('name', TextType::class, [
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => ['rows' => 8],
            ])
            ->add('group', EntityType::class, [
                'class' => Group::class,
                'choices' => $this->getChoices(),
                'choice_label' => 'name',
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'multiple' => true,
                'label' => 'Télécharger une image',
                'mapped' => false,
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'entry_options' => ['label' => false],
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }

    private function getChoices()
    {
        return $this->groupRepository->findAll();
    }
}
