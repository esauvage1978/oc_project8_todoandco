<?php

namespace App\Twig;

use App\Entity\Task;
use App\Security\TaskVoter;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TaskExtension extends AbstractExtension
{
    /**
     * @var TaskVoter
     */
    private $taskVoter;

    /**
     * @var Security
     */
    private $securityContext;

    public function __construct(
        TaskVoter $taskVoter,
        Security $securityContext
    ) {
        $this->taskVoter = $taskVoter;
        $this->securityContext = $securityContext;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('taskCanDelete', [$this, 'taskCanDelete']),
            new TwigFilter('taskCanUpdate', [$this, 'taskCanUpdate']),
            new TwigFilter('taskCanCreate', [$this, 'taskCanCreate']),
        ];
    }

    public function taskCanDelete(Task $task)
    {
        $user = $this->securityContext->getToken()->getUser();

        return $this->taskVoter->canDelete($task, $user);
    }

    public function taskCanUpdate(Task $task)
    {
        $user = $this->securityContext->getToken()->getUser();

        return $this->taskVoter->canUpdate($task, $user);
    }

    public function taskCanCreate()
    {
        $user = $this->securityContext->getToken()->getUser();

        return $this->taskVoter->canCreate(null, $user);
    }
}
