<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {

        $category = [
            1 => [
                'name' => 'Homme',
                'description' => "catégorie Homme",
                'picture' => 'man.jpg',
            ],
            2 => [
                'name' => 'Femme',
                'description' => "catégorie Femme",
                'picture' => 'woman.jpg',
            ],
            3 => [
                'name' => 'Enfant',
                'description' => "catégorie Enfant",
                'picture' => 'child.jpg',
            ],


        ];
        foreach ($category as $key => $value) {
            $cat = new Categories();
            $cat->setName($value['name']);
            $cat->setDescription($value['description']);
            $cat->setImage($value['picture']);
            $this->addReference('category_' . $key, $cat);
            $manager->persist($cat);
        }
        $manager->flush();
    }
}