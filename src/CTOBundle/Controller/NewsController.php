<?php

namespace CTOBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use CTOBundle\Entity\News;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
class NewsController extends Controller
{
    public function indexAction(Request $request)
    {
        $em    = $this->get('doctrine')->getEntityManager();
        $dql   = 'SELECT a FROM \CTOBundle\Entity\News a ORDER BY a.dateUpdate DESC';

        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            20/*limit per page*/
        );
        $pagination->setTemplate("CTOBundle:pagination:pagination.html.twig");
        return $this->render('CTOBundle:News:index.html.twig', array('pagination' => $pagination));
    }

    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $newsById = $em->getRepository('CTOBundle:News')->find($request->get("id"));

        $em->remove($newsById);
        $em->flush();
        return $this->redirect("/news");
    }

    public function createAction(Request $request)
    {
        $news = new News();
        $news->setDateCreate(date_create());
        $news->setDateUpdate(date_create());
        $news->setUrlArticle("http://lenta.ru");
        $news->setUrlImage("http://mediad.publicbroadcasting.net/p/wvik/files/styles/x_large/public/201702/fake_news_fb_event_cover.png");
        $form = $this->createFormBuilder($news)
            ->add("name", TextType::class)
            ->add("description", TextareaType::class)
            ->add('save', SubmitType::class,
                array(
                    'label' => 'Сохранить новость',
                    'attr' => array(
                        'class' => 'btn-primary')
                )
            )->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try {
                $news = $form->getData();
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($news);
                $em->flush();
                return $this->redirectToRoute("cto_list_news");
            }
            catch (UniqueConstraintViolationException $exception) {
                $form->addError(new FormError("Новость уже существут"));
            }
        }
        return $this->render('CTOBundle:News:create-news.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, $id){
        $news = $this->getDoctrine()->getRepository(News::class)->find($id);
        $form = $this->createFormBuilder($news)
            ->add("name", TextType::class)
            ->add("description", TextareaType::class)
            ->add('save', SubmitType::class,
                array(
                    'label' => 'Изменить новость',
                    'attr' => array(
                        'class' => 'btn-primary')
                )
            )->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            try {
                $news = $form->getData();
                $em = $this->getDoctrine()->getEntityManager();
                $news->setDateUpdate(date_create());

                $em->flush();
                return $this->redirectToRoute("cto_list_news");
            }
            catch (UniqueConstraintViolationException $exception) {
                $form->addError(new FormError("Новость уже существут"));
            }
        }
        return $this->render('CTOBundle:News:edit-news.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}
