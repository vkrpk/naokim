<?php

namespace App\Controller;

use App\Entity\ServiceCategory;
use App\Form\ServiceCategoryType;
use App\Repository\ServiceCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ServiceCategoryController extends AbstractController
{
    /**
     * @Route("/admin/service-category/index", name="service_category_index", methods={"GET"}, priority=1)
     */
    public function index(ServiceCategoryRepository $serviceCategoryRepository): Response
    {
        return $this->render('service_category/index.html.twig', [
            'service_categories' => $serviceCategoryRepository->findAll(),
        ]);
    }

     /**
     * @Route("/service-category/{slug}", name="service_category_all", methods={"GET"})
     */
    public function all($slug, ServiceCategoryRepository $serviceCategoryRepository): Response
    {
        $category = $serviceCategoryRepository->findOneBy(['slug' => $slug]);
        if (!$category) {
            $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render("card/all.html.twig", [
            'results' => $category->getServices(),
            "title" => $category->getName(),
        ]);
    }

    /**
     * @Route("/admin/service-category/new", name="service_category_new", methods={"GET","POST"})
     */
    function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response {
        $serviceCategory = new ServiceCategory();
        $form = $this->createForm(ServiceCategoryType::class, $serviceCategory);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $serviceCategory->setSlug(strtolower($slugger->slug($serviceCategory->getName())));
            $em->persist($serviceCategory);
            $em->flush();
            return $this->redirectToRoute('service_category_index');
        }

        return $this->render('service_category/new.html.twig', [
            'service_category' => $serviceCategory,
            'form'             => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/service-category/{id}/edit", name="service_category_edit", methods={"GET","POST"})
     */
    public function edit(ServiceCategory $serviceCategory, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ServiceCategoryType::class, $serviceCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $serviceCategory->setSlug(strtolower($slugger->slug($serviceCategory->getName())));
            $em->flush();
            return $this->redirectToRoute('service_category_index');
        }

        return $this->render('service_category/edit.html.twig', [
            'service_category' => $serviceCategory,
            'form'             => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/service-category/{id}", name="service_category_delete", methods={"POST"})
     */
    public function delete(Request $request, ServiceCategory $serviceCategory): Response
    {
        if ($this->isCsrfTokenValid('delete' . $serviceCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($serviceCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_category_index');
    }
}
