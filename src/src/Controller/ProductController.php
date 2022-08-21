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

    #[Route('/', name: 'app_home')]
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

    #[Route('/product/create', name: 'app_product_create')]
    public function createProduct(Request $request, ManagerRegistry $doctrine, CategoryRepository $categoryRepository, FileUploader $fileUploader): Response
    {
        $product = new Product();
        $categories = $categoryRepository->findAll();
        $form = $this->createForm(ProductType::class, $product, array(
            'categories'=>$categories,
        ));

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $product = $form->getData();
            $cat =$form->get("categories")->getData();
            foreach ($cat as $category){
               $product->addCategory($category);
            }
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form->get('brochure')->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $product->setBrochureFilename($brochureFileName);
            }

            $entityManager = $doctrine->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            return new Response('Product added successfully');
        }
        return $this->renderForm('product/create.html.twig', [
            'form' => $form,
        ]);
    }

    /*---*/
    #[Route('/product/{id}', name: 'app_product_view')]
    public function editProduct(Request $request, Product $product,  ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $product = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            return new Response('Hotel updated successfully');
        }
        return $this->renderForm('product/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/product/delete/{id}', name: 'app_product_delete')]
    public function deleteProduct(Product $product,  ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute('app_product');
    }
}
