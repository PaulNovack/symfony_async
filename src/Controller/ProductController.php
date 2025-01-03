<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CustomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

class ProductController extends AbstractController
{
    /**
     * @Route("/listproducts", name="list_products", methods={"GET"})
     */
    public function listProducts(Request $request, ManagerRegistry $doctrine): Response
    {
        /** @var CustomRepository $repository */
        $repository = $doctrine->getRepository(Product::class);

        $searchTerm = $request->query->get('search', '');
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        if ($searchTerm) {
            $repository->aSearchByName($searchTerm);
            $allProducts = $repository->aSyncFetch();
            $totalProducts = count($allProducts);
            $repository->aSearchByName($searchTerm);
        } else {
            $repository->aFindAll(10000, 0); // Fetch all to count total
            $allProducts = $repository->aSyncFetch();
            $totalProducts = count($allProducts);
        }
        $repository->aFindAll($limit, $offset);
        $products = $repository->aSyncFetch();
        return $this->render('product/list.html.twig', [
            'totalProducts' => $totalProducts,
            'products' => $products,
            'page' => $page,
            'totalPages' => ceil($totalProducts / $limit)
        ]);
    }
}
