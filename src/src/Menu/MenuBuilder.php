<?php

namespace App\Menu;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MenuBuilder
{
    private $factory;
    private $user;

    /**
     * Add any other dependency you need...
     */
    public function __construct(FactoryInterface $factory, TokenStorageInterface $tokenStorage)
    {
        $this->factory = $factory;
        if(!is_null($tokenStorage->getToken()))
            $this->user = $tokenStorage->getToken()->getUser()->eraseCredentials();
    }

    public function createMainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array('class' => 'nav navbar-nav navbar-right'));
        $menu->addChild('Home', ['route' => 'app_home', 'class' =>'nav-link']);
        if(!is_null($this->user) and in_array('ROLE_ADMIN', $this->user->getRoles(), true)){
            $menu->addChild('Create Product', ['route' => 'app_product_create', 'class' =>'nav-link']);
            $menu->addChild('Create Category', ['route' => 'app_category_create', 'class' =>'nav-link']);
        }
        if(!is_null($this->user) and in_array('ROLE_SUPER_ADMIN', $this->user->getRoles(), true)){
            $menu->addChild('Admin Dashboard', ['route' => 'admin', 'class' =>'nav-link']);
        }
        return $menu;
    }
}