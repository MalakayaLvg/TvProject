<?php

namespace App\DataFixtures;

use App\Service\RequestService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{ public function __construct(RequestService $service)
{
    $this->service = $service;
}
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $films = $this->service->getFilms();
        $allSeries = $this->service->getSeries();


        $manager->flush();
    }
}
