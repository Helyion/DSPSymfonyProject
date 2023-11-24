<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\Query\Expr\Math;
use Doctrine\Persistence\ObjectManager;
use PhpParser\Node\Expr\Cast\Object_;

class ProductFixtures extends Fixture
{
    private const NB_PRODUCTS = 20;

    public function load(ObjectManager $manager): void
    {
        for($i = 0 ; $i < self::NB_PRODUCTS; $i++){
            $entity = new Product();
            $entity
                ->setName("Product $i")
                ->setPrice(mt_rand(1, 999))
                ->setDescription("Desc Produit $i")
                ->setImage('image.jpg');
            
                $manager->persist($entity);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
