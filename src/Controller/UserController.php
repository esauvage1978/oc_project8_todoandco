<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list", methods={"GET"})
     *
     * @return Response
     */
    public function listAction(UserRepository $userRepository)
    {
        return $this->render('user/list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function editAction(User $user, Request $request, UserManager $manager): Response
    {
        return $this->save(
            $user,
            $request,
            $manager,
            'L\'utilisateur a bien été modifié',
            'user/edit.html.twig'
        );
    }

    /**
     * @Route("/users/create", name="user_create", methods={"GET","POST"})
     */
    public function createAction(Request $request, UserManager $manager): Response
    {
        return $this->save(
            new User(),
            $request,
            $manager,
            'Création de l\'utilisateur effectuée',
            'user/create.html.twig'
        );
    }

    private function save(
        User $user,
        Request $request,
        UserManager $manager,
        string $message,
        string $template): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($user);

            $this->addFlash('success', $message);

            return $this->redirectToRoute('user_list');
        }

        return $this->render($template, ['form' => $form->createView(), 'user' => $user]);
    }
}
