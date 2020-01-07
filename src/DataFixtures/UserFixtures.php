<?php


namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public const USERS = ['mary', 'john'];

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user2 = new User();
        $user1->setUsername('mary');
        $user1->setPassword($this->encoder->encodePassword($user1,'doe'));
        $user1->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $user2->setUsername('john');
        $user2->setPassword($this->encoder->encodePassword($user2,'doe'));
        $user2->setRoles(['ROLE_USER']);

        $manager->persist($user1);
        $manager->persist($user2);

        $this->addReference('mary', $user1);
        $this->addReference('john', $user2);

        $manager->flush();
    }
}
