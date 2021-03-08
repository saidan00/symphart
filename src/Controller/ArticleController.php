<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use App\Entity\Article;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleController extends AbstractController {
  /**
   * @Route("/", name="article_list")
   * @Method({"GET"})
   */
  public function index() {
    // return $this->render("articles/index.html.twig");
    // $articles = ["Article 1", "Article 2"];
    $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

    return $this->render('articles/index.html.twig', array('articles' => $articles));
  }

  /**
   * @Route("article/new", name="article_new")
   * @Method({"GET", "POST"})
   */
  public function new(Request $request) {
    $article = new Article();

    $form = $this->createFormBuilder($article)
      ->add('title', TextType::class, ['attr' => ['class' => 'form-control']])
      ->add('body', TextareaType::class, ['attr' => ['class' => 'form-control']])
      ->add('save', SubmitType::class, ['attr' => ['label' => 'create', 'class' => 'btn btn-primary mt-3']])
      ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $article = $form->getData();

      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($article);
      $entityManager->flush();

      return $this->redirectToRoute('article_list');
    }

    return $this->render('articles/new.html.twig', ['form' => $form->createView()]);
  }

  /**
   * @Route("article/edit/{id}", name="article_edit")
   * @Method({"GET", "POST"})
   */
  public function edit(Request $request, $id) {
    $article =
      $this->getDoctrine()->getRepository(Article::class)->find($id);

    $form = $this->createFormBuilder($article)
      ->add('title', TextType::class, ['attr' => ['class' => 'form-control']])
      ->add('body', TextareaType::class, ['attr' => ['class' => 'form-control']])
      ->add('save', SubmitType::class, ['attr' => ['label' => 'create', 'class' => 'btn btn-primary mt-3']])
      ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->flush();

      return $this->redirectToRoute('article_list');
    }

    return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
  }

  /**
   * @Route("article/delete/{id}")
   * @Method({"DELETE"})
   */
  public function delete($id) {
    $article =
      $this->getDoctrine()->getRepository(Article::class)->find($id);

    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($article);
    $entityManager->flush();

    $response = new Response();
    $response->send();
  }

  /**
   * @Route("/article/{id}", name="article_show")
   */
  public function show($id) {
    $article =
      $this->getDoctrine()->getRepository(Article::class)->find($id);

    return $this->render('articles/show.html.twig', array('article' => $article));
  }

  /**
   * @Route("/about")
   * @Method({"GET"})
   */
  public function about() {
    return new Response('about');
  }

  // /**
  //  * @Route("/article/save")
  //  */
  // public function save() {
  //   $entityManager = $this->getDoctrine()->getManager();

  //   $article = new Article();
  //   $article->setTitle("Article Two");
  //   $article->setBody("This is the body for article two");

  //   $entityManager->persist($article);

  //   $entityManager->flush();

  //   return new Response("Saved an article with the id of " . $article->getId());
  // }
}
