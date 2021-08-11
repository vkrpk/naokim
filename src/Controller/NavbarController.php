<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Service;
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
        $submittedToken = $request->query->get( '_csrf_token' );
        $query = $request->query->get('q');
        if(!empty($query) && $this->isCsrfTokenValid('token-name', $submittedToken)) {
            $products = $this->getDoctrine()->getRepository(Product::class)->findByName($query);
            $services = $this->getDoctrine()->getRepository(Service::class)->findByName($query);
            $results = array_merge($products, $services);

            return $this->render('search_bar/index.html.twig', [
                'results' => $results,
            ]);
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
