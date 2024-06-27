<?php // src/Controller/ContactController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    /**
     @Route("/api/send-email", name="send_email", methods={"POST"})
     */
    public function sendEmail(Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $email = $data['email'];
        $message = $data['message'];

        $email = (new Email())
            ->from($email)
            ->to('julien45.dubois@gmail.com') // Remplacez par l'email du destinataire
            ->subject('Nouveau message de ' . $name)
            ->text($message);

        $mailer->send($email);

        return new JsonResponse(['status' => 'Email sent']);
    }
}
