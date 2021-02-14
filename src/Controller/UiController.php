<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UiController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('ui/index.html.twig', [
            'active_menu' => null
        ]);
    }

   /*
    public function addFirstUser(UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User(true, true);
        $user->setEmail('cedric.lang-roth@radiofrance.com');
        $user->setName('Lang-Roth');
        $user->setSurname('Cédric');
        $user->setPigiste(false);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($passwordEncoder->encodePassword($user, 'hastalavista1'));
        $this->getDoctrine()->getManager()->persist($user);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('info', "L'utilisateur a bien été crée.");
        return $this->redirectToRoute('index');
    }
   */

    /**
     * @Route("/landing", name="landing_page")
     */
    public function index(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('email', TextType::class)
            ->add('submit', SubmitType::class, [
                'label' => 'Se connecter'
            ])
            ->add('account_creation', SubmitType::class, [
                'label' => 'Créer un compte'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy([
                    'email' => $email
                ]);
            if (substr_compare($email, '@radiofrance.com', -strlen('@radiofrance.com'))) {
                $this->addFlash('danger', 'Merci de saisir un e-mail Radio France.');
                return $this->redirectToRoute('landing_page');
            }
            if ($form->get('account_creation')->isClicked()) {
                if ($user) {
                    $this->addFlash('danger', 'Il existe déjà un compte pour cet e-mail.');
                } else {
                    $user = new User();
                    $user->setEmail($email);
                    $this->getDoctrine()->getManager()->persist($user);
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('info', 'La demande de création de compte a bien été envoyée.');
                }
            } else {
                if (!$user) {
                    $this->addFlash('danger', 'Aucun compte enregistré pour cette adresse. Merci de faire une demande de création.');
                } else {
                    return $this->redirectToRoute('app_login');
                }
            }
        }
        return $this->render('security/landing.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
