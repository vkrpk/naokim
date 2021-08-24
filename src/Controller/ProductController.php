<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductCategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/product/all", name="product_all", methods={"GET"})
     */
    public function all(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('card/all.html.twig', [
            "results" => $products,
            "title" => "Les produits",
        ]);
    }

    /**
     * @Route("/product/{category_slug}/{slug}", name="product_show", methods={"GET"})
     */
    public function show($slug, ProductRepository $productRepository, ProductCategoryRepository $productCategoryRepository, $category_slug)
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);
        $category = $productCategoryRepository->findOneBy(['slug' => $category_slug]);
        if (!$product || !$category) {
            throw new NotFoundHttpException('Le produit demandé n\'existe pas');
        }
        return $this->render("card/show.html.twig", [
            'result' => $product,
            "title" => $product->getName(),
        ]);
    }

    /**
     * @Route("/admin/product/index", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('product/index.html.twig', [
            "products" => $products,
        ]);
    }

    /**
     * @Route("/admin/product/new", name="product_new", methods={"GET","POST"})
     */
    function new (Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute("product_show", [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug'          => $product->getSlug(),
            ]);
        }

        return $this->render('product/new.html.twig', [
            'formView' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Product $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->flush();
            return $this->redirectToRoute("product_show", [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug'          => $product->getSlug(),
            ]);
        }
        $formView = $form->createView();

        return $this->render("product/edit.html.twig", [
            "formView" => $formView,
            'product'  => $product,
        ]);
    }

    /**
     * @Route("/admin/product/{id}", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
