<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            EmailField::new('email'),
            ArrayField::new('roles'),
            TextField::new('username')->hideOnIndex(),
            TextField::new('firstname'),
            TextField::new('Lastname'),
            BooleanField::new('isVerified'),
            // TextField::new('password')->setFormType(PasswordType::class)->hideOnIndex(),
            // TextField::new('authCode'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->addFormTheme('@FOSCKEditor/form/ckeditor_widget.html.twig')
            /**
         * ->setEntityPermission('ROLE_EDITEUR')
         * par défaut Symfony a leur rôle admin + editeur
         * nous avons la possibilité de hiérarchiser les rôles dans le fichier security.yaml
         * nous pouvons donner des rôles spécifiques en créant des "voter"
         * en général, les "voter" sont un sous-dossier de security
         * https://symfony.com/bundles/EasyAdminBundle/current/crud.html
         */
        ;
    }
}