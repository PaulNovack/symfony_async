<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class UserController extends AbstractController
{
    /**
     * @Route("/listusers", name="list_users")
     */
    public function listUsers(ManagerRegistry $doctrine): Response
    {
        $users = $doctrine->getRepository(User::class)->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }
}
