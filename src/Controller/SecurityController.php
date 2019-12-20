<?php


namespace App\Controller;


use Exception;
use Symfony\Bundle\FrameworkBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller\AbstractController
{
    /**
     * @Route("/{_locale}/login", requirements={"_locale": "fr|en"}, name="app_login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();
        $targetPath = $request->getSession()->get('_security.main.target_path');
        $lastUsername = $authUtils->getLastUsername();
        return $this->render('login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
            'targetPath' => $targetPath
        ));
    }
    /**
     * @Route("/{_locale}/login_check", requirements={"_locale": "fr|en"}, name="app_login_check")
     * @throws Exception
     */
    public function loginCheckAction()
    {
        throw new Exception('Unexpexted loginCheck action');
    }

    /**
     * @Route("/{_locale}/logout", requirements={"_locale": "fr|en"}, name="app_logout")
     * @throws Exception
     */
    public function logoutAction()
    {
        throw new Exception('Unexpected logout action');
    }

}