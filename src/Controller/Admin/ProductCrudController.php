<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Proxies\__CG__\App\Entity\ProductCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $now = new \DateTime('now');
        $now->format('Y-m-d H:i:s');
        return [
            TextField::new ('name'),
            MoneyField::new ('price')->setCurrency('EUR'),
            DateTimeField::new('date')->hideOnForm()->setFormattedValue('d/m/y')->setCssClass('text-nowrap'),
            IntegerField::new ('stock'),
            ChoiceField::new ('status')->setChoices([Product::STATUS_AVAILABLE => Product::STATUS_AVAILABLE, Product::STATUS_UNAVAILABLE => Product::STATUS_UNAVAILABLE])->hideOnIndex(),
            TextField::new('status')->hideOnForm(),
            TextField::new ('mainPicture'),
            AssociationField::new ('category'),
            TextField::new ('slug')->hideOnForm(),
            TextEditorField::new ('description'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['date' => 'DESC'])
            ->setDateTimeFormat("dd/LL/yy à hh'h'mm")
            
            ;
            // ->setTimeFormat('d/m/y à H:i');
            // ->setTimezone('Europe/Paris');
    }

}
