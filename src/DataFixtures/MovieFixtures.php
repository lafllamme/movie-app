<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setTitle('The Dark Knight');
        $movie->setReleaseYear(2008);
        $movie->setDescription('This is a description');
        $movie->setImagePath('https://images.pexels.com/photos/11791435/pexels-photo-11791435.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2');

        //add reference to pivot table
        $movie->addActor($this->getReference('actor_1'));
        $movie->addActor($this->getReference('actor_2'));

        $manager->persist($movie);

        $movie2 = new Movie();
        $movie2->setTitle('Avengers: Endgame');
        $movie2->setReleaseYear(2019);
        $movie2->setDescription('This is a description');
        $movie2->setImagePath('https://images.pexels.com/photos/3180273/pexels-photo-3180273.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2');

        //add reference to pivot table
        $movie2->addActor($this->getReference('actor_3'));
        $movie2->addActor($this->getReference('actor_4'));
        $manager->persist($movie2);

        $manager->flush();
    }
}
