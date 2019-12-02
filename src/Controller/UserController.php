<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     */
    public function listAction()
    {
    }

    /**
     * @Route("/users/create", name="user_create", methods={"GET","POST"})
     */
    public function createAction(Request $request, UserManager $manager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($manager->update($user)) {
                $this->addFlash('success', 'Création de l\'utilisateur effectuée');

                return $this->redirectToRoute('user_list');
            }
            $this->addFlash('danger', 'La création a echoué. En voici les raisons : '.$manager->getErrors($user));
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
