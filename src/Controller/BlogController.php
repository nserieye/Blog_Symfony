<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/{_locale}", name="home", requirements={"_locale": "fr|en"}, defaults={"_locale":"fr"})
     */
    public function blogAction() {
        return $this->render('base.html.twig');
    }
}