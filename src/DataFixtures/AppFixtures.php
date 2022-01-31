<?php

namespace App\DataFixtures;

use App\Entity\Page;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setEmail('admin@email.local')
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        $page = (new Page())
            ->setSlug('default')
            ->setTitle('Default Page')
            ->setText('Hello :)');

        $manager->persist($page);

        $manager->flush();
    }
}
