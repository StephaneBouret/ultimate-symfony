<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use App\Entity\User;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $slugger;
    private $passwordHasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $passwordHasher)
    {
        $this->slugger = $slugger;
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User;

        $hash = $this->passwordHasher->hashPassword($admin, "password");

        $admin->setEmail("admin@gmail.com")
            ->setPassword($hash)
            ->setFullname("Admin")
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        
        for ($u=0; $u < 5; $u++) { 
            $user = new User;
            $hash = $this->passwordHasher->hashPassword($user, "password");
            $user->setEmail("user$u@gmail.com")
                ->setFullname($faker->name())
                ->setPassword($hash);
            $manager->persist($user);
        }

        for ($c=0; $c < 3; $c++) { 
            $category = new Category;
            $category
                ->setName($faker->department)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);

            for ($p=0; $p < mt_rand(15, 20); $p++) { 
                $product = new Product;
                $product
                    ->setName($faker->productName)
                    ->setPrice($faker->price(4000, 20000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setPicture($faker->imageUrl(400, 400, true));
                $manager->persist($product);
            }
        }

        // create 100 products
        // for ($i=0; $i < 100; $i++) { 
        //     $product = new Product;
        //     $product
        //         ->setName('Produit n°'.$i)
        //         ->setPrice(mt_rand(100, 200))
        //         ->setSlug('produit-n-'.$i);
        //     $manager->persist($product);
        // }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
