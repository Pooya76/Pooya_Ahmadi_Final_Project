<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{


    #[Route(
        path: '/{_locale}/',
        name: 'app_home',
        requirements: ['_locale' => 'en|fa'],
        defaults: ['_locale' => 'en']
    )]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product', name: 'app_product')]
    public function products(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route(
        path: '/product/create',
        name: 'app_product_create',
    )]
    #[Route(
        path: '/{_locale}/product/create',
        name: 'app_product_create',
        requirements: ['_locale' => 'en|fa'],
        defaults: ['_locale' => 'en']
    )]
    public function createProduct(Request $request, ManagerRegistry $doctrine, CategoryRepository $categoryRepository, FileUploader $fileUploader): Response
    {
        $product = new Product();
        $categories = $categoryRepository->findAll();
        $form = $this->createForm(ProductType::class, $product, array(
            'categories' => $categories,
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $product = $form->getData();
            $cat = $form->get("categories")->getData();
            foreach ($cat as $category) {
                $product->addCategory($category);
            }
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('picture')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $product->setPictureFilename($imageFileName);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            new Response('Product added successfully');
            return $this->redirectToRoute('app_home');

        }
        return $this->renderForm('product/create.html.twig', [
            'form' => $form,
        ]);
    }

    /*---*/
    #[Route('/product/{id}', name: 'app_product_view')]
    public function editProduct(Request $request, Product $product, ManagerRegistry $doctrine, CategoryRepository $categoryRepository, FileUploader $fileUploader): Response
    {
        $categories = $categoryRepository->findAll();
        $form = $this->createForm(ProductType::class, $product, array(
            'categories' => $categories,
        ));
        $existedCategory  = $product->getCategories();
        foreach ($existedCategory as $category) {
            $product->removeCategory($category);
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $product = $form->getData();
            $newCategories = $form->get("categories")->getData();
            foreach ($newCategories as $category) {
                $product->addCategory($category);
            }
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('picture')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $product->setPictureFilename($imageFileName);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('app_product');
        }
        return $this->renderForm('product/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/delete/{id}', name: 'app_product_delete')]
    public function deleteProduct(Product $product, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute('app_product');
    }

    #[Route(
        path: '/{_locale}/product/detail/{id}',
        name: 'app_product_detail',
        requirements: ['_locale' => 'en|fa'],
        defaults: ['_locale' => 'en']
    )]
    public function productDetail(Product $product): Response
    {

        return $this->renderForm('product/detail.html.twig', [
            'product' => $product,
        ]);
    }
}
