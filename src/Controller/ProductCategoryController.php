<?php

namespace App\Controller;

use App\Entity\ProductCategory;
use App\Form\ProductCategoryType;
use App\Repository\ProductCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductCategoryController extends AbstractController
{
     /**
     * @Route("/product-category/{slug}", name="product_category_show", methods={"GET"})
     */
    public function show($slug, ProductCategoryRepository $productCategoryRepository)
    {
        $category = $productCategoryRepository->findOneBy(['slug' => $slug]);
        if (!$category) {
            $this->createNotFoundException("La catégorie demandée n'existe pas");
        }
        $colors = ['white', 'yellow', 'red', 'blue', 'green'];

        return $this->render('product_category/show.html.twig', [
            'category' => $category,
            'colors'   => $colors]);
    }

    /**
     * @Route("/admin/product-category/index", name="product_category_index", methods={"GET"})
     */
    public function index(ProductCategoryRepository $productCategoryRepository): Response
    {
        return $this->render('product_category/index.html.twig', [
            'product_categories' => $productCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/product-category/new", name="product_category_new", methods={"GET","POST"})
     */
    function new (Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response {
        $productCategory = new ProductCategory();
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $productCategory->setSlug(strtolower($slugger->slug($productCategory->getName())));
            $em->persist($productCategory);
            $em->persist($productCategory);
            $em->flush();
            return $this->redirectToRoute('product_category_index');
        }

        return $this->render('product_category/new.html.twig', [
            'product_category' => $productCategory,
            'form'             => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/product-category/{id}/edit", name="product_category_edit", methods={"GET","POST"})
     */
    public function edit(ProductCategory $productCategory, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductCategoryType::class, $productCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productCategory->setSlug(strtolower($slugger->slug($productCategory->getName())));
            $em->flush();
            return $this->redirectToRoute('product_category_index');
        }

        return $this->render('product_category/edit.html.twig', [
            'product_category' => $productCategory,
            'form'             => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/product-category/{id}", name="product_category_delete", methods={"POST"})
     */
    public function delete(Request $request, ProductCategory $productCategory): Response
    {
        if ($this->isCsrfTokenValid('delete' . $productCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($productCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_category_index');
    }
}
