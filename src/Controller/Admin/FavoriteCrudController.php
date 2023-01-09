<?php

namespace App\Controller\Admin;

use App\Entity\Favorite;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class FavoriteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Favorite::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->add(Crud::PAGE_INDEX, Action::DETAIL)//ajouter l'action de visualisation
        ->disable(Action::EDIT); //cacher l'action d'édition
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('user', 'Cet utilisateur')->setCrudController(UserController::class),
            AssociationField::new('property', 'A ajouter cette propriété à ces favoris')->setCrudController(PropertyController::class),
            DateTimeField::new('created_at', 'A la date du')->setFormat(DateTimeField::FORMAT_LONG)->setTimezone('Europe/Paris'),
        ];
    }

}
