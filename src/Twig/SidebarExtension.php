<?php

namespace App\Twig;

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
     * @var CacheInterface
     */
    protected $cache;

    public function __construct(Environment $twig, CacheInterface $cache)
    {
        $this->twig = $twig;
        $this->cache = $cache;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sidebar', [$this, 'renderSidebar'], ['is_safe' => ['html']])
        ];
    }

    // public function getSidebar(): string
    // {
    //     return $this->cache->get('sidebar', function (ItemInterface $item) {
    //         $item->expiresAfter(3600);
    //         return $this->renderSidebar();
    //     });
    // }

    public function renderSidebar(): string
    {
        return $this->twig->render('partials/sidebar.html.twig', []);
    }
}