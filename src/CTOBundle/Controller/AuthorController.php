<?php
/**
 * Created by PhpStorm.
 * User: sazan
 * Date: 1/18/2018
 * Time: 6:05 PM
 */

namespace CTOBundle\Controller;

use CTOBundle\Entity\Author;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Form\FormError;
class AuthorController extends Controller
{

    public function indexAction(Request $request)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $dql = 'SELECT a FROM \CTOBundle\Entity\Author a ORDER BY a.dateUpdate DESC';
        $query = $em->createQuery($dql);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query, $request->query->getInt('page', 1), 20);
        $pagination->setTemplate("CTOBundle:pagination:pagination.html.twig");
        return $this->render('CTOBundle:Author:index.html.twig', array('pagination' => $pagination));
    }

    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $authorById = $em->getRepository('CTOBundle:Author')->find($request->get("id"));
        $dqlUpdateNews = 'SELECT a FROM \CTOBundle\Entity\News a WHERE a.author =  '.$authorById->getId();
        $query = $em->createQuery($dqlUpdateNews);
        foreach ($query->getResult() as $news){
            $news->setAuthor(null);
        }
        $em->remove($authorById);
        $em->flush();
        return $this->redirect("/authors");
    }

    public function editAction(Request $request) {
        $author = $this->getDoctrine()->getRepository('CTOBundle:Author')->find($request->get("id"));
        $form = $this->createFormBuilder($author)
            ->add("name", TextType::class)
            ->add("email", EmailType::class)
            ->add('save', SubmitType::class,
                array(
                    'label' => 'Изменить автора',
                    'attr' => array(
                        'class' => 'btn-primary')
                )
            )->getForm();
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() )
        {
            try {
                $author = $form->getData();
                $em = $this->getDoctrine()->getEntityManager();
                $author->setDateUpdate(date_create());

                $em->flush();
                return $this->redirectToRoute("cto_authors_list");
            }
            catch (UniqueConstraintViolationException $exception) {
                $form->addError(new FormError("Автор с таким именем существует"));
            }
        }

        return $this->render('CTOBundle:Author:edit-author.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    public function createAction(Request $request)
    {
        $author = new Author();
        $author->setDateCreate(date_create());
        $author->setDateUpdate(date_create());
        $form = $this->createFormBuilder($author)
            ->add("name", TextType::class)
            ->add("email", EmailType::class)
            ->add('save', SubmitType::class,
                array(
                    'label' => 'Изменить автора',
                    'attr' => array(
                        'class' => 'btn-primary')
                )
            )->getForm();
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid() )
        {
            try {
                $author = $form->getData();
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($author);
                $em->flush();
                return $this->redirectToRoute("cto_authors_list");
            }
            catch (UniqueConstraintViolationException $exception) {
                $form->addError(new FormError("Автор с таким именем существует"));
            }
        }
        else {
            echo "Валидация не пройдена!!!";
        }

        return $this->render('CTOBundle:Author:create-author.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }
}