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
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 25;
        $offset = ($page - 1) * $limit;

        if ($searchTerm) {
            $repository->aSearchByName($searchTerm);
        }
        $totalUsers = count($repository->aSyncFetch());
        $repository->aFindAll($limit, $offset);
        $users = $repository->aSyncFetch();

        return $this->render('user/list.html.twig', [
            'users' => $users,
            'page' => $page,
            'totalPages' => ceil($totalUsers / $limit)
        ]);
    }
}
