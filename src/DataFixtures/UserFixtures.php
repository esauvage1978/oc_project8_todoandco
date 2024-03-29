<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Manager\UserManager;
use App\Validator\UserValidator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    const KEY = 'user';

    const DATA = [
        [
            'username' => 'Paul',
            'email' => 'paul@live.fr',
            'password' => 'u12345678',
            'roles' => ['ROLE_USER'],
        ],
        [
            'username' => 'Robert',
            'email' => 'robert@live.fr',
            'password' => 'u12345678',
            'roles' => ['ROLE_USER'],
        ],
        [
            'username' => 'Pauline',
            'email' => 'pauline@live.fr',
            'password' => 'u12345678',
            'roles' => ['ROLE_USER'],
        ],
        [
            'username' => 'Manu',
            'email' => 'emmanuel.sauvage@live.fr',
            'password' => 'u12345678',
            'roles' => ['ROLE_ADMIN'],
        ],
    ];

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var UserValidator
     */
    private $validator;

    public function __construct(UserValidator $validator, UserManager $userManager)
    {
        $this->validator = $validator;
        $this->userManager = $userManager;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < count(self::DATA); ++$i) {
            $instance = $this->initialise(new User(), self::DATA[$i]);

            $this->checkAndPersist($manager, $instance);

            $this->addReference(self::KEY.'-'.$i, $instance);
        }
        $manager->flush();
    }

    private function checkAndPersist(ObjectManager $manager, User $instance)
    {
        $this->userManager->initialiseUser($instance);

        if ($this->validator->isValid($instance)) {
            $manager->persist($instance);
        }
    }

    private function initialise(User $instance, $data): User
    {
        $instance->setUsername($data['username'])
            ->setEmail($data['email'])
            ->setPlainPassword($data['password'])
            ->setRoles($data['roles']);

        return $instance;
    }
}
