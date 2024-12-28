<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

class UserController extends AbstractController
{
    /**
     * @Route("/listusers", name="list_users")
     */
    public function listUsers(Request $request, ManagerRegistry $doctrine): Response
    {
        /** @var CustomRepository $repository */
        $repository = $doctrine->getRepository(User::class);

        $searchTerm = $request->query->get('search', '');
        $sql = null;
        if ($searchTerm) {
            $users = $repository->searchByName($searchTerm);
            die();
            $repository->aSyncGet();
            $data = $repository->aSyncFetch();
            var_dump($data);


        } else {
            $users = $repository->findAll();
        }

        return $this->render('user/list.html.twig', [
            'users' => $users,
            'sql' => $sql,
        ]);
    }
}
