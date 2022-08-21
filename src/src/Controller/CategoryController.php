<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/category/create', name: 'app_category_create')]
    public function createCategory(Request $request, ManagerRegistry $doctrine): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $category = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return new Response('Category added successfully');
        }
        return $this->renderForm('category/create.html.twig', [
            'form' => $form,
        ]);
    }

    /*-----*/

    #[Route('/category/{id}', name: 'app_category_view')]
    public function editCategory(Request $request, Category $category,  ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $category = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return new Response('Category updated successfully');
        }
        return $this->renderForm('category/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/category/delete/{id}', name: 'app_category_delete')]
    public function deleteCategory(Category $category,  ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($category);
        $entityManager->flush();
        $this->get('session')->getFlashBag()->add(
            'notice',
            'Customer Added!'
        );
        return $this->redirectToRoute('app_category');
    }

    #[Route('/category/{id}/products', name: 'app_category_products')]
    public function categoryProducts(Request $request, Category $category,  ManagerRegistry $doctrine): Response
    {
        $products = $category->getProducts();
        return $this->renderForm('category/products.html.twig', [
            'products' => $products,
        ]);
    }


    public function categories(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/categories.html.twig', [
            'categories' => $categories
        ]);
    }
}
