<?php
namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Picture;
use App\Entity\Product;
use App\Entity\Service;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\ProductCategory;
use App\Entity\ServiceCategory;
use App\Entity\PurchaseDeliveryAddress;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Bezhanov\Faker\ProviderCollectionHelper;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $slugger;
    private $encoder;
    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $status = ['Disponible', 'En approvisionnement', 'Rupture de stock'];
        $faker = Factory::create("fr_FR");
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));
        \Bezhanov\Faker\ProviderCollectionHelper::addAllProvidersTo($faker);

        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $user
                ->setEmail("user$u@gmail.com")
                ->setPseudo($faker->name())
                ->setPassword($this->encoder->hashPassword($user, "password"));
            $this->addReference("user-".$u, $user);
            $manager->persist($user);

        }

        $admin = new User();
        $admin
            ->setEmail("admin@gmail.com")
            ->setPseudo("Admin")
            ->setPassword($this->encoder->hashPassword($admin, "password"))
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $products = [];

        for ($i = 0; $i < 5; $i++) {
            $category = new ProductCategory();
            $category
                ->setName($faker->department())
                ->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);
            for ($p = 0; $p < mt_rand(5, 10); $p++) {
                $product = new Product();
                $product
                    ->setName($faker->productName())
                    ->setPrice(mt_rand(4000, 20000))
                    ->setDescription($faker->paragraph())
                    ->setDate($faker->dateTimeBetween($startDate = '-30 days', $endDate = 'now', $timezone = 'Europe/Paris'))
                    ->setStock(mt_rand(0, 100))
                    ->setStatus($status[array_rand($status)])
                    ->setStatus(Product::STATUS_AVAILABLE)
                    ->setMainPicture($faker->imageUrl(400, 400, true))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category);

                    $products[] = $product;

                $manager->persist($product);
            }
        }

        $services = [];
        $totalOfnumberOfServices = 0;

        for ($i = 0; $i < 5; $i++) {
            $category = new ServiceCategory();
            $category
                ->setName($faker->department())
                ->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);
            $numberOfServices = mt_rand(5, 10);
            for ($s = 0; $s < $numberOfServices; $s++) {
                $service = new Service();
                $service
                    ->setName("Service numéro ". ($s + $totalOfnumberOfServices))
                    ->setSlug(strtolower($this->slugger->slug($service->getName())))
                    ->setDate($faker->dateTimeBetween($startDate = '-30 days', $endDate = 'now', $timezone = 'Europe/Paris'))
                    ->setStatus(Service::STATUS_AVAILABLE)
                    ->setDescription($faker->paragraph())
                    ->setPrice(mt_rand(5000, 25000))
                    ->setMainPicture($faker->imageUrl(400, 400, true))
                    ->setCategory($category);
                    $services[] = $service;

                    $manager->persist($service);
                }
                $totalOfnumberOfServices += $numberOfServices;
        }

        for ($p=0; $p < mt_rand(25, 35); $p++) {
            $deliveryAddress = new PurchaseDeliveryAddress();
            $deliveryAddress
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setAddress($faker->streetAddress())
                ->setPostalCode((string)$faker->postcode())
                ->setCity($faker->city())
                ->setPhone($faker->phoneNumber())
                ->setUser($this->getReference("user-".rand(0,4)))
                ;
            $this->addReference("deliveryAddress-" . $p, $deliveryAddress);
            $manager->persist($deliveryAddress);

            $purchase = new Purchase;
            $purchase
                ->setReference(uniqid('', false))
                ->setTotal(mt_rand(2000, 30000))
                ->setPurchasedAt($faker->dateTimeBetween('-6 months'))
                ->setDeliveryAddress($this->getReference("deliveryAddress-" . $p))
                ->setUser($this->getReference("user-".rand(0,4)));

            $selectedProducts = $faker->randomElements($products, mt_rand(2, 3));
            foreach ($selectedProducts as $product) {
                $purchaseItem = new PurchaseItem();
                $purchaseItem->setProduct($product)
                    ->setQuantity(mt_rand(1, 3))
                    ->setItemName($product->getName())
                    ->setItemPrice($product->getPrice())
                    ->setTotal($purchaseItem->getItemPrice() * $purchaseItem->getQuantity())
                    ->setPurchase($purchase);
                $manager->persist($purchaseItem);
            }

            $selectedServices = $faker->randomElements($services, mt_rand(1, 2));
            foreach ($selectedServices as $service) {
                $purchaseItem = new PurchaseItem();
                $purchaseItem->setService($service)
                    ->setItemName($service->getName())
                    ->setItemPrice($service->getPrice())
                    ->setTotal($purchaseItem->getItemPrice() * $purchaseItem->getQuantity())
                    ->setPurchase($purchase);
                $manager->persist($purchaseItem);
            }

            if ($faker->boolean(90)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }
            $manager->persist($purchase);
        }

        for ($i=0; $i < 3; $i++) {
            $picture = new Picture();
            $picture->setMainPicture('https://picsum.photos/1300/400?image=' . mt_rand(1, 100));
            $manager->persist($picture);
        }

        $manager->flush();
    }
}
