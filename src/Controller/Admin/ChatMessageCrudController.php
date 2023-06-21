<?php

namespace App\Controller\Admin;

use App\Entity\ChatMessage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ChatMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ChatMessage::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::EDIT);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('text'),
            TextField::new('author.username')->setLabel('Author'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setEntityLabelInSingular('Chat Message')
            ->setEntityLabelInPlural('Chat Messages');
    }
}
