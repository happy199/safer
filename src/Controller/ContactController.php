<?php

namespace App\Controller;
use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{

    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        // Enregistrement du message et Envoie du mail de contact

        if($form->isSubmitted() && $form->isValid()) {
            $contact->setType('receive');
            $entityManager->persist($contact);
            $entityManager->flush();
            $this->addFlash('success', 'Votre message a bien été envoyé');
            $contactFormData = $form->getData();
            $email = (new Email())
                    ->from('contact@safer.com')
                    ->to($contactFormData->getEmail())
                    ->subject($contactFormData->getSubject())
                    ->html('<h2>Corp du message : </h2><br><br><p>'.$contactFormData->getMessage().'</p>');                
                $mailer->send($email);  
        }
        return $this->render('contact/index.html.twig', [
            'contactform' => $form->createView(),
        ]);
    }
}
