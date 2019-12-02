<?php

namespace App\Manager;

use App\Entity\User;
use App\Validator\UserValidator;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var UserValidator
     */
    private $validator;

    public function __construct(EntityManagerInterface $manager,
                                UserValidator $validator)
    {
        $this->manager = $manager;
        $this->validator = $validator;
    }

    public function update(User $user): bool
    {
        if (!$this->validator->isValid($user)) {
            return false;
        }

        $this->manager->persist($user);
        $this->manager->flush();

        return true;
    }

    public function getErrors(User $entity)
    {
        return $this->validator->getErrors($entity);
    }
}
