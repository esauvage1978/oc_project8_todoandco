<?php

namespace App\Validator;

use App\Entity\User;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserValidator
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ConstraintViolationListInterface
     */
    private $errors;

    /**
     * UserValidator constructor.
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return bool
     */
    public function isValid(User $user): bool
    {
        $this->errors = $this->validator->validate($user);

        return  !count($this->errors) ? true : false;
    }

    /**
     * @return string|null
     */
    public function getErrors(User $user): ?string
    {
        $this->errors = $this->validator->validate($user);

        return (string) $this->errors;
    }
}
