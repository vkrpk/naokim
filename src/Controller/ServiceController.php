<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceCategoryRepository;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ServiceController extends AbstractController
{
    /**
     * @Route("/admin/service/index", name="service_index", methods={"GET"})
     */
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/service/all", name="service_all", methods={"GET"})
     */
    public function all(ServiceRepository $serviceRepository): Response
    {
        $services = $serviceRepository->findAll();
        return $this->render('service/all.html.twig', [
            "services" => $services,
        ]);
    }

    /**
     * @Route("/admin/service/new", name="service_new", methods={"GET","POST"})
     */
    function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->setSlug(strtolower($slugger->slug($service->getName())));
            $em->persist($service);
            $em->flush();
            return $this->redirectToRoute("service_show", [
                'category_slug' => $service->getCategory()->getSlug(),
                'slug'          => $service->getSlug(),
            ]);
        }
        return $this->render('service/new.html.twig', [
            'service'  => $service,
            'formView' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/service/{id}/edit", name="service_edit", methods={"GET","POST"})
     */
    public function edit(Service $service, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->setSlug(strtolower($slugger->slug($service->getName())));
            $em->flush();
            return $this->redirectToRoute("service_show", [
                'category_slug' => $service->getCategory()->getSlug(),
                'slug'          => $service->getSlug(),
            ]);
        }
        $formView = $form->createView();
        return $this->render("service/edit.html.twig", [
            "formView" => $formView,
            'service'  => $service,
        ]);

    }

    /**
     * @Route("/admin/service/{id}", name="service_delete", methods={"POST"})
     */
    public function delete(Request $request, Service $service): Response
    {
        if ($this->isCsrfTokenValid('delete' . $service->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($service);
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_index');
    }

     /**
     * @Route("/service/{category_slug}/{slug}", name="service_show", methods={"GET"})
     */
    public function show($slug, ServiceRepository $serviceRepository, ServiceCategoryRepository $serviceCategoryRepository, $category_slug)
    {
        $service = $serviceRepository->findOneBy(['slug' => $slug]);
        $category = $serviceCategoryRepository->findOneBy(['slug' => $category_slug]);
        if (!$service || !$category) {
            throw new NotFoundHttpException('Le produit demandé n\'existe pas');
        }
        return $this->render("service/show.html.twig", [
            'service' => $service,
        ]);
    }

}
