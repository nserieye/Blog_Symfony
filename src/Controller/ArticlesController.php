<?php


namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Service\AntiSpam;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/{_locale}/blog", requirements={"_locale": "fr|en"})
 * @IsGranted("ROLE_USER")
 */
class ArticlesController extends AbstractController
{

    //function qui permet de lister les articles avec nombre predefinis d'article par page
    /**
     * @Route ("/list/{page}", name="list_article", defaults ={"page":1}, requirements={"page": "\d+"})
     */
    public function listAction($page, EntityManagerInterface $em, TranslatorInterface $translator, Request $request){

        $currentPath= 'list_article';

        $nbPerPage = $this->getParameter("nbPerPage");
        $articles = $em->getRepository('App:Article')->findOnlyPublishedWithPaging($page, $nbPerPage);
        $nbTotalPages = intval(ceil(count($articles)/$nbPerPage));
        $locale = $request->getLocale();

        if($page<1 || $page>$nbTotalPages){
            throw new NotFoundHttpException($translator->trans('controller.errorPage'));
        }
        return $this->render('article/list.html.twig',[
            'articles' => $articles, 'nbPage' => $nbTotalPages, 'page' => $page, 'locale' => $locale, 'currentPath' => $currentPath
        ]);
    }

    //function qui "affiche" un article précis et augmente le nb de vue
    /**
     * @Route ("/article/{id}", name="view_article", requirements={"id": "\d+"})
     */
    public function viewAction(Article $article, EntityManagerInterface $em,TranslatorInterface $translator, Security $security)
    {
        if (!$security->isGranted('view', $article)){
            throw new NotFoundHttpException($translator->trans('controller.errorView'));
        }

        if($article){
            $article ->setNbViews($article->getNbViews()+1);
            $em->flush();
            return $this->render('article/view.html.twig',['article' => $article] );
        }

        throw new NotFoundHttpException($translator->trans('controller.errorArticle'));
    }

    //function qui permet d'ajouter un article
    /**
     * @Route ("/article/add", name="add_article", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function addAction(EntityManagerInterface $em,Request $request, AntiSpam $antiSpam, TranslatorInterface $translator){
        $article = new Article();
        $article ->setNbViews(1);
        $form=$this->createForm(ArticleType::class, $article);
        $form->add('send',SubmitType::class, ['label'=>'controller.form.buttonAdd', 'attr' =>['class'=> 'btn btn-outline-primary'] ]);
        $form->handleRequest($request);
        $article->forceAuthor();

        if($form->isSubmitted() && $form->isValid()){
            if ($antiSpam->isSpam($article->getContent())) {
                $this->addFlash('danger',$translator->trans('controller.spam.error'));
                return $this->render('article/add.html.twig', array('form'=>$form->createView()));
            }

            $em->persist($article);
            $em->flush();
            $this->addFlash('success',$translator->trans('controller.success.add'));
            return $this->redirectToRoute('list_article');
        }
        return $this->render('article/add.html.twig', array('form'=>$form->createView()));
    }

    //Edition d'un article
    /**
     * @Route ("/article/edit/{id}", name="edit_article", requirements={"id":"\d+"}, methods={"GET", "POST"})
     */
    public function editAction(EntityManagerInterface $em, Request $request, int $id,AntiSpam $antiSpam, TranslatorInterface $translator, Security $security){
        $article = $em->getRepository('App:Article')->find($id);
        if (!$security->isGranted('edit', $article)){
            throw new NotFoundHttpException($translator->trans('controller.errorGranted'));
        }
        $form=$this->createForm(ArticleType::class, $article);
        $form->add('send',SubmitType::class, ['label'=>'controller.form.buttonEdit', 'attr' =>['class'=> 'btn btn-outline-primary']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if ($antiSpam->isSpam($article->getContent())) {
                $this->addFlash('danger',$translator->trans('controller.spam.error'));
                return $this->render('article/edit.html.twig', array('form'=>$form->createView()));
            }
            $article ->setUpdatedAt(new \DateTime());
            $em->flush();
            $this->addFlash('success', $translator->trans('controller.success.edit'));
            return $this->redirectToRoute('view_article', ['id' => $id]);
        }
        return $this->render('article/edit.html.twig', ['form'=>$form->createView()]);
    }

    //suppression d'un article
    /**
     * @Route ("/article/delete/{id}", name="delete_article", requirements={"id":"\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAction($id, EntityManagerInterface $em, TranslatorInterface $translator){


        $article = $em->getRepository('App:Article')->find($id);

        if(!$article){
            throw new NotFoundHttpException($translator->trans('controller.errorArticle'));
        }
        $em->remove($article);
        $em->flush();
        $this->addFlash('success',$translator->trans('controller.success.delete'));
        return $this->redirectToRoute('list_article');
    }

    //affichage des derniers articles
    /**
     * @Route ("/article/recent", name="recent_article")
     */
    public function recentArticlesAction($nbArticles, EntityManagerInterface $em){  //gerer le fait de prendre les nbarticles derniers articles
        $articles = $em->getRepository('App:Article')->findBy(
            array('published'=>true),
            array('createdAt' => 'DESC'),
            $nbArticles,
            0
        );
        $categories = $em->getRepository('App:Category')->findAll();

        return $this->render('last_articles.html.twig',['articles' => $articles, 'categories' => $categories] );
    }

    //affichage des articles pour une catégorie précise
    /**
     * @Route ("/category/{id}/{page}", name="category", requirements={"page": "\d+", "id": "\d+"}, defaults={"page"=1}))
     */
    public function categoryAction ($page, $id, EntityManagerInterface $em, TranslatorInterface $translator,  Request $request){
        $currentPath= 'category';
        $nbPerPage = $this->getParameter("nbPerPage");

        if($id){
            $category = $em->getRepository('App:Category')->find($id);
            $articles = $em->getRepository('App:Article')->findOnlyPublishedByCategoryWithPading($category,$page, $nbPerPage);
            $nbTotalPages = intval(ceil(count($articles)/$nbPerPage));
            return $this->render('article/list.html.twig', ['articles' => $articles, 'nbPage' => $nbTotalPages, 'page' => $page, 'currentPath' => $currentPath, 'id' => $id]);
        }else {
            throw new NotFoundHttpException($translator->trans('controller.errorPage'));
        }
    }
}