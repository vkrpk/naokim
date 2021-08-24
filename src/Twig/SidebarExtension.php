<?php

namespace App\Twig;

use App\Repository\ProductCategoryRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SidebarExtension extends AbstractExtension
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var ProductCategoryRepository
     */
    protected $productCategoryRepository;

    /**
     * @var CacheInterface
     */
    protected $cache;

    public function __construct(Environment $twig, ProductCategoryRepository $productCategoryRepository, CacheInterface $cache)
    {
        $this->twig = $twig;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->cache = $cache;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sidebar', [$this, 'getSidebar'], ['is_safe' => ['html']])
        ];
    }

    public function getSidebar(): string
    {
        // return $this->cache->get('sidebar', function (ItemInterface $item) {
            // $item->expiresAfter(3600);
            // $item->tag(['comments', 'posts']);
            return $this->renderSidebar();
        // });
    }

    private function renderSidebar(): string
    {
        $categories = $this->productCategoryRepository->findAll();
        return $this->twig->render('partials/sidebar.html.twig', [
            'categories' => $categories
        ]);
    }
}