<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Service;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavbarController extends AbstractController
{
    /**
     * @Route("/search", name="navbar_search_bar")
     */
    public function search_bar(Request $request, EntityManagerInterface $em): Response
    {
        $submittedToken = $request->request->get( '_csrf_token' );
        $query = $request->request->get('q');
        if(!empty($query) && $this->isCsrfTokenValid('token-name', $submittedToken)) {
            $products = $this->getDoctrine()->getRepository(Product::class)->findByName($query);
            $services = $this->getDoctrine()->getRepository(Service::class)->findByName($query);
            $results = array_merge($products, $services);

            return $this->render('card/all.html.twig', [
                'results' => $results,
                'title' => 'Recherche : ' . $query
            ]);
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
