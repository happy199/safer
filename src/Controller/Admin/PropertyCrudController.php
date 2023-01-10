<?php

namespace App\Controller\Admin;

use App\Entity\Property;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class PropertyCrudController extends AbstractCrudController
{

    public const PROPERTY_BASE_PATH = 'uploads/images/properties';
    public const PROPERTY_UPLOAD_DIR = 'public/uploads/images/properties';
    public const ACTION_DUPLICATE = 'duplicate';

    public static function getEntityFqcn(): string
    {
        return Property::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $duplicate = Action::new(self::ACTION_DUPLICATE)// action pour dupliquer une propriété
        ->linkToCrudAction('duplicateProperty')
        ->setCssClass('btn btn-info');
        return $actions
        ->add(Crud::PAGE_INDEX, $duplicate)
        ->add(Crud::PAGE_EDIT, $duplicate);
    }
    
    public function configureFields(string $pageName): iterable
    {
       
        return [
            TextField::new('title', 'Titre'),
            SlugField::new('slug', 'Permalien')->setTargetFieldName('title'),
            AssociationField::new('category', 'Catégorie')->setCrudController(CategoryController::class),
            TextEditorField::new('description', 'Description'),
            NumberField::new('surface', 'Superficie'),
            ChoiceField::new('status', 'Statut')->setChoices([
                'A vendre' => 'sell',
                'A louer' => 'rent',
            ])->renderExpanded(true),
            MoneyField::new('price', 'Prix')->setCurrency('EUR'),
            NumberField::new('postalcode', 'Code Postal'),
            TextField::new('address', 'Adresse'),
            TextField::new('city', 'Ville'),
            TextField::new('department', 'Département'),
            ImageField::new('images', 'Image en avant')
                ->setBasePath('uploads/images/properties')
                ->setUploadDir('public/uploads/images/properties')
                ->setSortable(false)
                ->setUploadedFileNamePattern('[slug].[extension]'),

        ];
    }


    public function duplicateProperty(AdminContext $context,AdminUrlGenerator $adminUrlGenerator,EntityManagerInterface $em): Response 
    {
        $property = $context->getEntity()->getInstance();

        $duplicatedProperty = clone $property; // générer le clone du property reçu en paramêtre

        parent::persistEntity($em, $duplicatedProperty);

        $url = $adminUrlGenerator->setController(self::class)
            ->setAction(Action::DETAIL)
            ->setEntityId($duplicatedProperty->getId()) // faire appel a la fonction duplicateProperty 
            ->generateUrl();

        return $this->redirect($url);
    }

}
