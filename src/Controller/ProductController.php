<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CustomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class ProductController extends AbstractController
{
    /**
     * @Route("/listproducts", name="list_products", methods={"GET"})
     */
    public function listProducts(ManagerRegistry $doctrine): Response
    {
        /** @var CustomRepository $repository */
        $repository = $doctrine->getRepository(Product::class);

        $repository->aFindAll();
        $products = $repository->aSyncFetch();

        return $this->render('product/list.html.twig', [
            'products' => $products
        ]);
    }
}
