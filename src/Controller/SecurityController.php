<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // ÖNEMLİ: Bu satırı ekleyin
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils; // ÖNEMLİ: Bu satırı ekleyin

class SecurityController extends AbstractController
{
    #[Route('/loginIwapim', name: 'app_frontend_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('default_homepage');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/loginIwapim_check', name: 'app_frontend_login_check')]
    public function check(): void
    {
        throw new \LogicException('Bu action asla çalıştırılmamalı, security sistemi tarafından yakalanır.');
    }

    #[Route('/logoutIwapim', name: 'app_frontend_logout')]
    public function logout(): void
    {
        throw new \LogicException('Bu action asla çalıştırılmamalı, security sistemi tarafından yakalanır.');
    }
}