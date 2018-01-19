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
class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('CTOBundle:Default:index.html.twig');
    }
}
