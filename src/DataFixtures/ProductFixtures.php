<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(2332); // même jeu de données à chaque fois en utilisant la méthode seed()
        for ($i = 1; $i <= 20; $i++) {

            $product = new Product();
            $product->setname('produit ' . $faker->sentence($nbWords = 1, $variableNbWords = true));
            $product->setDescription($faker->sentence(4));
            $product->setMoreInformation($faker->paragraph());
            $product->setPrice($faker->randomFloat(2, 5, 9900)); // 2 decimales et nb entre 5 et 99
            $product->setQuantity($faker->numberBetween(1, 100));
            $product->setTags($faker->sentence(4));
            $product->setSlug($this->slugger->slug($product->getName()));
            $product->setImage('image.jpg');
            $category = $this->getReference('category_' . rand(1, 3));
            $product->addCategory($category);
            $manager->persist($product);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoriesFixtures::class
        ];
    }
}