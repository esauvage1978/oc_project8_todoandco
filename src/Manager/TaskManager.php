<?php

namespace App\Manager;

use App\Entity\Task;
use App\Validator\TaskValidator;
use Doctrine\ORM\EntityManagerInterface;

class TaskManager
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var TaskValidator
     */
    private $validator;

    public function __construct(
        EntityManagerInterface $manager,
        TaskValidator $validator
    ) {
        $this->manager = $manager;
        $this->validator = $validator;
    }

    public function save(Task $task): bool
    {
        $this->initialise($task);

        if (!$this->validator->isValid($task)) {
            return false;
        }

        $this->manager->persist($task);
        $this->manager->flush();

        return true;
    }

    public function initialise(Task $task)
    {
        if (empty($task->getId())) {
            $task->setCreatedAt(new \DateTime());
            $task->setIsDone(false);
        }
    }

    public function getErrors(Task $task)
    {
        return $this->validator->getErrors($task);
    }

    public function remove(Task $task)
    {
        $this->manager->remove($task);
        $this->manager->flush();
    }
}
