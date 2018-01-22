<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 22/01/18
 * Time: 11:26
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HomeController
 *
 *
 * @Route("/")
 */
class HomeController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="homepage")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository(Article::class)->findBy([], ['updatedDate'=>'DESC'], 10);

        return $this->render('default/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/article/{slug}", name="one_article")
     *
     * @Method({"GET", "POST"})
     */
    public function showOneAction(Request $request, Article $article)
    {
        $comment = new Comment();
        $form = $this->createForm('AppBundle\Form\CommentType', $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $comment->setArticle($article);
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('one_article', [
                'slug' => $article->getSlug()
            ]);
        }

        return $this->render('default/article.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

}