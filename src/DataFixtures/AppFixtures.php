<?php

namespace App\DataFixtures;

use App\Entity\Film;
use App\Entity\Season;
use App\Entity\Series;
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
        foreach ($films as $filmData){
            $film = new Film();
            $film->setDescription($filmData['description']);
            $film->setTitle($filmData['title']);
            $film->setPublishDate($filmData['publish_date']);
            $film->setCriticalRate($filmData['critical_rate']);
            $film->setRuntime($filmData['runtime']);
            $manager->persist($film);
        }
        $allSeries = $this->service->getSeries();
        foreach($allSeries as $serieData){
            $series= new Series();
            $series->setTitle($serieData['title']);
            $series->setDescription($serieData['description']);
            $series->setSeen($serieData['seen']);
            $series->setPublishDate($serieData['publish_date']);
            $series->setCriticalRate($serieData['critical_rate']);
            $manager->persist($series);
            $manager->flush();

            foreach ($serieData['description'] as $seasonData){
                $season = new Season();
                $season->setTitle($seasonData["title"]);
                $season->setDescription($seasonData["description"]);
                $season->setPublishDate($seasonData["publish_date"]);
                $season->setNumber($seasonData["number"]);
                $season->setSeries($series);
                $manager->persist($season);
                $manager->flush();

                
                foreach ($seasonData['episodes'] as $episodeData){
                        $episode = new Episode();
                        $episode->setTitle($episodeData['title']);
                        $episode->setDescription($episodeData['description']);
                        $episode->setNumber($episodeData['number']);
                        $episode->setPublishDate($episodeData["publish_date"]);
                        $episode->setSeason($season);
                        $manager->persist($episode);
                        $manager->flush();
                }
            }

        }

        $manager->flush();
    }
}
