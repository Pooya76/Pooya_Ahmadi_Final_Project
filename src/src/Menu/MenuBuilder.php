<?php

namespace App\Menu;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuBuilder
{
    private $factory;
    private $user;
    private $tokenStorage;
    private $translator;


    /**
     * Add any other dependency you need...
     */
    public function __construct(FactoryInterface $factory, TokenStorageInterface $tokenStorage, TranslatorInterface $translator)
    {
        $this->factory = $factory;
        if (!is_null($tokenStorage->getToken()))
            $this->user = $tokenStorage->getToken()->getUser();
        $this->tokenStorage = $tokenStorage;
        $this->translator = $translator;
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $homeLabel = $this->translator->trans('menu.home');
        $createProductLabel = $this->translator->trans('menu.createProduct');
        $createCategoryLabel = $this->translator->trans('menu.createCategory');
        $loginLabel = $this->translator->trans('menu.login');
        $registerLabel = $this->translator->trans('menu.register');
        $logoutLabel = $this->translator->trans('menu.logout');
        $adminDashboardLabel = $this->translator->trans('menu.adminDashboard');


        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array('class' => 'nav navbar-nav navbar-right nav-item nav-link'));
        $menu->addChild($homeLabel, ['route' => 'app_home'])->setAttribute('class', 'nav-item nav-link');

        if (!is_null($this->user) and in_array('ROLE_ADMIN', $this->user->getRoles(), true)) {
            $menu->addChild($createProductLabel, ['route' => 'app_product_create'])->setAttribute('class', 'nav-item nav-link');
            $menu->addChild($createCategoryLabel, ['route' => 'app_category_create'])->setAttribute('class', 'nav-item nav-link');
        }
        if (!is_null($this->user) and in_array('ROLE_SUPER_ADMIN', $this->user->getRoles(), true)) {
            $menu->addChild($adminDashboardLabel, ['route' => 'admin', 'class' => 'nav-link']);
        }

        if (!is_null($this->user) and in_array('ROLE_USER', $this->user->getRoles(), true)) {
            $menu->addChild( $logoutLabel, ['route' => 'app_logout'])->setAttribute('class', 'nav-item nav-link');
        }else {
            $menu->addChild($loginLabel, ['route' => 'app_login'])->setAttribute('class', 'nav-item nav-link');
            $menu->addChild( $registerLabel, ['route' => 'app_register'])->setAttribute('class', 'nav-item nav-link');
        }


        return $menu;
    }
}