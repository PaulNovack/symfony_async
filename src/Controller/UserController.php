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
        if ($searchTerm) {
            $repository->aSearchByName($searchTerm);
            $users = $repository->aSyncFetch();
        } else {
            $repository->aFindAll();
            $users = $repository->aSyncFetch();
        }
        return $this->render('user/list.html.twig', [
            'users' => $users
        ]);
    }
}
