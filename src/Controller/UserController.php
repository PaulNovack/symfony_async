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
        $users = $doctrine->getRepository(User::class)->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }
    public function getAsynch(): string
    {
        return 'SELECT * FROM users';
    }

    public function findAllAsync(): array
    {
        $context = new ZMQContext();
        $socket = $context->getSocket(ZMQ::SOCKET_REQ);
        $socket->connect("tcp://localhost:5555");

        $query = $this->getAsynch();
        $socket->send($query);

        $reply = $socket->recv();
        return json_decode($reply, true);
    }
}
