<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\ByteString;

class ContactController extends AbstractController
{

    public function __construct(private ContactRepository $contactRepository, private RequestStack $requestStack, private EntityManagerInterface $entityManager)
{
}

    #[Route('/contact', name: 'contact.index')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
            'contacts' => $this->contactRepository->findAll(),
        ]);
    }


    #[Route('/contact/form', name:'contact.form')]
    #[Route('/contact/update/{id}', name: 'contact.update')]
    public function form(int $id = null) : Response {

        //cr&ation d'un formilaire
        $entity = $id ? $this->contactRepository->find($id) : new Contact();
        $type = ContactType::class;
        
        $form = $this->createForm($type, $entity);

        // recupere la saisie precedente dans la requete http
        $form->handleRequest($this->requestStack->getMainRequest());

        if($form->isSubmitted() && $form->isValid()){
            // inserer dans la base
            $this -> entityManager->persist($entity);
            $this -> entityManager->flush();

            // message de confirmation
            $message = $id ? 'contact updated' : 'contact created';

            $this->addFlash('notice', $message);

            return $this->redirectToRoute('contact.index');
        }
        return $this->render('contact/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contact/delete/{id}', name: 'contact.delete')]
    public function delete (int $id): RedirectResponse {
        $entity = $this->contactRepository->find($id);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->addFlash('notice', 'contact deleted');

        return $this->redirectToRoute('contact.index');
    }
}
