<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\ByteString;

#[Route('/admin')]
class ProductController extends AbstractController
{
public function __construct(private ProductRepository $productRepository, private RequestStack $requestStack, private EntityManagerInterface $entityManager)
{
}

    #[Route('/product', name: 'admin.product.index')]
    public function index(): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'products' => $this->productRepository->findAll(),
        ]);
    }

    #[Route('/product/form', name:'admin.product.form')]
    #[Route('/product/update/{id}', name: 'admin.product.update')]
    public function form(int $id = null) : Response {

        //cr&ation d'un formilaire
        $entity = $id ? $this->productRepository->find($id) : new Product();
        $type = ProductType::class;
        
        $entity->prevImage = $entity->getImage();

        $form = $this->createForm($type, $entity);

        // recupere la saisie precedente dans la requete http
        $form->handleRequest($this->requestStack->getMainRequest());

        if($form->isSubmitted() && $form->isValid()){
            // gestion de l'image
            $filename = ByteString::fromRandom(32)->lower();
            $file = $entity->getImage();

            // si une image a ete selectionnee
            if ($file instanceof UploadedFile){
                $fileExtension = $file->guessClientExtension();

                $file->move('img', "$filename.$fileExtension");

                $entity->setImage("$filename.$fileExtension");
            
                if ($id) unlink("img/{$entity->prevImage}");
            }

            else{
                $entity->setImage($entity->prevImage);
            }

            // inserer dans la base
            $this -> entityManager->persist($entity);
            $this -> entityManager->flush();

            // message de confirmation
            $message = $id ? 'Product updated' : 'Product created';

            $this->addFlash('notice', $message);

            return $this->redirectToRoute('admin.product.index');
        }
        return $this->render('admin/product/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/delete/{id}', name: 'admin.product.delete')]
    public function delete (int $id): RedirectResponse {
        $entity = $this->productRepository->find($id);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        unlink("img/{$entity->getImage()}");

        $this->addFlash('notice', 'Product deleted');

        return $this->redirectToRoute('admin.product.index');
    }
}
