<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Manager\TaskManager;
use App\Repository\TaskRepository;
use App\Security\TaskVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
     * @Route("/tasks", name="task_list", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function listAction(TaskRepository $taskRepository): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['isDone' => false])]);
    }

    /**
     * @Route("/tasks/toogleoff", name="task_list_toogle_off", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function listToggleOffAction(TaskRepository $taskRepository): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findBy(['isDone' => true])]);
    }

    /**
     * @Route("/tasks/create", name="task_create", methods={"GET","POST"})
     */
    public function createAction(Request $request, TaskManager $taskManager): Response
    {
        return $this->save(
            $request,
            new Task(),
            $taskManager,
            true
        );
    }

    public function save(Request $request, Task $task, TaskManager $taskManager, bool $creation): Response
    {
        $this->denyAccessUnlessGranted(
            ($creation ? TaskVoter::CREATE : TaskVoter::UPDATE),
            $task);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskManager->save($task);

            $this->addFlash('success',
                ($creation ? 'La tâche a été bien été ajoutée.' : 'La tâche a bien été modifiée.'));

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/'.($creation ? 'create' : 'edit').'.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit",  methods={"GET","POST"})
     */
    public function editAction(Request $request, Task $task, TaskManager $taskManager): Response
    {
        return $this->save(
            $request,
            $task,
            $taskManager,
            false
        );
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task, TaskManager $taskManager): Response
    {
        /** @var bool $state */
        $state = $task->getIsDone();
        $taskManager->toogle($task);
        $taskManager->save($task);

        if (!$state) {
            $this->addFlash('success', sprintf('La tâche <<%s>> a bien été marquée comme faite.', $task->getTitle()));

            return $this->redirectToRoute('task_list');
        }

        $this->addFlash('success', sprintf('La tâche <<%s>> a bien été marquée comme non faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list_toogle_off');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Request $request, Task $task, TaskManager $taskManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $this->addFlash('success', 'La tâche a bien été supprimée.');
            $taskManager->remove($task);
        }

        return $this->redirectToRoute('task_list');
    }
}
