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
        if ($searchTerm) {
            $repository->aSearchByName($searchTerm);
            $products = $repository->aSyncFetch();
        } else {
            $repository->aFindAll();
            $products = $repository->aSyncFetch();
        }

        return $this->render('product/list.html.twig', [
            'products' => $products
        ]);
    }
}
