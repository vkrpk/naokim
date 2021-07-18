<?php

namespace App\Controller;

use App\Repository\PictureRepository;
use App\Repository\ProductRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(ProductRepository $productRepository, ServiceRepository $serviceRepository, PictureRepository $pictureRepository): Response
    {
        $products = $productRepository->findBy([], ['date' => 'DESC'], 12);
        $services = $serviceRepository->findBy([], ['date' => 'DESC'], 12);
        $pictures = $pictureRepository->findBy([], ['id' => 'ASC'], 3);

        return $this->render('home.html.twig', [
            'products' => $products,
            'services' => $services,
            'pictures' => $pictures,
        ]);
    }

}
