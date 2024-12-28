<?php

namespace App\Controller;

use App\Entity\User;
use ZMQContext;
use ZMQSocket;
use ZMQ;
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
        $repository = $doctrine->getRepository(User::class, 'App\Repository\CustomRepository');
        $users = $repository->findAsync(1); // Example usage of the custom method

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }
}
