<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $post = new Post();
        $post -> setTitle('First Post');
        $post -> setDate('12 October 2024');
        $post -> setImage('build/images/bg1.jpg');
        $post -> setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus iaculis lacus finibus nisi
                                feugiat, efficitur tempor orci imperdiet. Integer eget augue pharetra, blandit augue ac,
                                tristique massa. Etiam fermentum dui quis dolor malesuada, molestie finibus enim venenatis. Nam
                                quis velit enim.');

        $manager -> persist($post);
        $manager -> flush();
    }
}
