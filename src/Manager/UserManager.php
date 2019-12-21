<?php

namespace App\Manager;

use App\Entity\User;
use App\Validator\UserValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        EntityManagerInterface $manager,
        UserValidator $validator,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->manager = $manager;
        $this->validator = $validator;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function save(User $user): bool
    {
        $this->initialiseUser($user);

        if (!$this->validator->isValid($user)) {
            return false;
        }

        $this->manager->persist($user);
        $this->manager->flush();

        return true;
    }

    public function initialiseUser(User $user)
    {
        if (empty($user->getId()) and empty($user->getRoles())) {
            $user->setRoles(['ROLE_USER']);
        }
        $this->encodePassword($user);
    }

    public function encodePassword(User $user)
    {
        $plainPassword = $user->getPlainPassword();

        if (!empty($plainPassword)) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $plainPassword
                ));
        }
    }

    public function getErrors(User $entity)
    {
        return $this->validator->getErrors($entity);
    }
}
