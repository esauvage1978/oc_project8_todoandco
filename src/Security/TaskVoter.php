<?php

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TaskVoter extends Voter
{
    const UPDATE = 'update';
    const DELETE = 'delete';
    const CREATE = 'create';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::CREATE, self::UPDATE, self::DELETE])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (null !== $subject and !$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;

        switch ($attribute) {
            case self::UPDATE:
                return $this->canUpdate($task, $user);
            case self::CREATE:
                return $this->canCreate($task, $user);
            case self::DELETE:
                return $this->canDelete($task, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    public function canUpdate(Task $task, User $user)
    {
        if ($this->security->isGranted('ROLE_USER')) {
            return true;
        }

        return false;
    }

    public function canCreate(?Task $task, User $user)
    {
        if ($this->security->isGranted('ROLE_USER')) {
            return true;
        }

        return false;
    }

    public function canDelete(Task $task, User $user)
    {
        if ($user === $task->getUser()) {
            return true;
        }

        if ($this->security->isGranted('ROLE_ADMIN') && empty($task->getUser())) {
            return true;
        }

        return false;
    }
}
