<?php

namespace App\Validator;

use App\Entity\Task;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskValidator
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ConstraintViolationListInterface
     */
    private $errors;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return bool
     */
    public function isValid(Task $task): bool
    {
        $this->errors = $this->validator->validate($task);

        return  !count($this->errors) ? true : false;
    }

    /**
     * @return string|null
     */
    public function getErrors(Task $task): ?string
    {
        $this->errors = $this->validator->validate($task);

        return (string) $this->errors;
    }
}
