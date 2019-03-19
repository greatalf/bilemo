<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Company;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class AppFixtures extends Fixture
{
    private $encoder;

	public function __construct(UserPasswordEncoderInterface $encoder)
	{
		$this->encoder = $encoder;
    }
    
    
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        
        for($i = 1; $i <= 6; $i++)
        {
            $company = new Company();
            $company->setName('Société ' . ucfirst($faker->word))
            ->setAddress($faker->address)
            ;
            $manager->persist($company);
            //Gestion des users
            $users = [];
            for($i = 1; $i <= 20; $i++)
            {
                $user = new User();
                $password = $this->encoder->encodePassword($user, 'azerty');
                $user->setFirstName($faker->firstname)
                    ->setLastName($faker->lastname)
                    ->setEmail($faker->email)
                    ->setUsername($user->fullname())
                    ->setPassword($password)
                    ->setRoles('ROLE_USER')
                    ->setCompany($company)
                    ->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')))
                    ;
                    
                $manager->persist($user);
                $users[] = $user;
            }
        }
        // dd($users);
        for($i = 1; $i <= 6; $i++)
        {
            $company = new Company();
            $company->setName('Société ' . ucfirst($faker->word))
            ->setAddress($faker->address)
            ->addUser($users[mt_rand(0, count($users) - 1)])
            ;
            $manager->persist($company);
        }
        

        for($i = 1; $i <= 10; $i++)
        {
            $product = new Product();
            $product->setName('Modèle ' . $faker->word)
                    ->setBrand('Marque ' . $faker->word)
                    ->setColor($faker->safeColorName)
                    ->setDescription($faker->paragraph($nbSentences = 3, $variableNbSentences = true))
                    ->setPrice(rand(90, 750))
                    ->setSerialNumber(rand(1000000, 10000000))
                    ->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')))
                    ; 
            $manager->persist($product);
        }
        $manager->flush();
    }
}
