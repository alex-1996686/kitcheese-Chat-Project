<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{

  #[Route('/signup', name: 'signup')]
  public function signup(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, UserAuthenticatorInterface $authenticator, LoginFormAuthenticator $loginForm, MailerInterface $mailer)
  {
    $user = new User();
    $userForm = $this->createForm(UserType::class, $user);
    $userForm->handleRequest($request);
    if ($userForm->isSubmitted() && $userForm->isValid()) {
      $hash = $passwordHasher->hashPassword($user, $user->getPassword());
      $user->setPassword($hash);
      $em->persist($user);
      $em->flush();
      $this->addFlash('success', 'Bienvenue sur kitcheesechat !');
      $email = new TemplatedEmail();
      $email->to($user->getEmail())
        ->subject('Bienvenue sur kitcheesechat')
        ->htmlTemplate('@email_template/welcome.html.twig')
        ->context([
          'username' => $user->getFirstname()
        ]);
      $mailer->send($email);
      return $authenticator->authenticateUser(
        $user,
        $loginForm,
        $request
      );
    }
    return $this->render('security/signup.html.twig', ['form' => $userForm->createView()]);
  }

  #[Route("/login", name: "login")]
  public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
  {
    
    if ($security->getUser()) {
      return $this->redirectToRoute('app_home');
    }
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
  }

  #[Route("/logout", name: "logout")]
  public function logout()
  {
  }
}