<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;
use AppBundle\Entity\Todo;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Goutte\Client;


class TodoController extends Controller
{
    
   
    /**
     * @Route("/", name="todo_list")
     */
    public function listAction(Request $request)
    {       
        $todos = $this->getDoctrine()->getRepository('AppBundle:Todo')->findAll();
        return $this->render('todo/index.html.twig', array(
            'todos' => $todos
        ));
           
    }
    
    /**
     * @Route("/crawl", name="crawl")
     */
    
    public function crawlAction(Request $request){
        
        $client = new Client();

        $adsFirstPage = $this->matchAds('http://www.piata-az.ro/anunturi/autoturisme-1063');
        
       // $nextUrl = $this->matchNext('http://www.piata-az.ro/anunturi/autoturisme-1063');
       //var_dump($adsFirstPage);
      // exit();
       
           
      
       // print_r($crawler);
        exit();

        
    }
    
    public function matchAds($url){
        $client = new Client();
        
        $adsArray = array();

        $crawler = $client->request('GET', $url);
     
        $crawler->filter('.link_totanunt')->each(function ($node) {
        $adsArray[] = $node->attr('href');
        
      
       });
       print_r($adsArray);
       exit();
       return $adsArray;    
        
    }
    
    public function matchNext($url){
        $client = new Client();
        
        $next = array();

        $crawler = $client->request('GET', $url);
     
       $crawler->filter('.next_page')->each(function ($node) {
        $next[] = $node->attr('href');
       });
       
        
       return $next;    
        
    }
    
    
     /**
     * @Route("/todo/create", name="todo_create")
     */
    public function createAction(Request $request)
    {       
        $todo = new Todo();
        $form = $this->createFormBuilder($todo)
                ->add('name', TextType::class, array('attr' =>array('class' => 'form-control', 'style'=>'margin-bottom:15px')))
                ->add('category', TextType::class, array('attr' =>array('class' => 'form-control', 'style'=>'margin-bottom:15px')))
                ->add('description', TextareaType::class, array('attr' =>array('class' => 'form-control', 'style'=>'margin-bottom:15px')))
                ->add('priority', ChoiceType::class, array('choices'=>array('Low'=>'Low','Normal'=>'Normal','High'=>'High'),'attr' =>array('class' => 'form-control', 'style'=>'margin-bottom:15px')))
                ->add('due_date', DateTimeType::class, array('attr' =>array('class' => 'formcontrol', 'style'=>'margin-bottom:15px')))
                ->add('save', SubmitType::class, array('label' => 'Create Todo','attr' =>array('class' => 'btn btn-primary', 'style'=>'margin-bottom:15px')))
                ->getForm();
        
        $form ->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            //get data
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $dueDate = $form['due_date']->getData();
            
            $now = new\DateTime('now');
            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($dueDate);
            $todo->setCreateDate($now);
            
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($todo);
            $em->flush();
            
            $this->addFlash(
                    'notice',
                    'Todo Added'
            );
            
            return $this->redirectToRoute('todo_list');
          
        }
        return $this->render('todo/create.html.twig', array(
            'form' => $form->createView()
        ));
           
    }
    
     /**
     * @Route("/todo/edit/{id}", name="todo_edit")
     */
    public function editAction($id, Request $request)
    {    
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
        
       /* $now = new\DateTime('now');
        
        $todo->setName($todo->getName());
        $todo->setCategory($todo->getCategory());
        $todo->setDescription($todo->getDescription());
        $todo->setPriority($todo->getPriority());
        $todo->setDueDate($todo->getDueDate());
        $todo->setCreateDate($now);*/
            
        $form = $this->createFormBuilder($todo)
                ->add('name', TextType::class, array('attr' =>array('class' => 'form-control', 'style'=>'margin-bottom:15px')))
                ->add('category', TextType::class, array('attr' =>array('class' => 'form-control', 'style'=>'margin-bottom:15px')))
                ->add('description', TextareaType::class, array('attr' =>array('class' => 'form-control', 'style'=>'margin-bottom:15px')))
                ->add('priority', ChoiceType::class, array('choices'=>array('Low'=>'Low','Normal'=>'Normal','High'=>'High'),'attr' =>array('class' => 'form-control', 'style'=>'margin-bottom:15px')))
                ->add('due_date', DateTimeType::class, array('attr' =>array('class' => 'formcontrol', 'style'=>'margin-bottom:15px')))
                ->add('save', SubmitType::class, array('label' => 'Edit Todo','attr' =>array('class' => 'btn btn-primary', 'style'=>'margin-bottom:15px')))
                ->getForm();
        
        $form ->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            //get data
            $name = $form['name']->getData();
            $category = $form['category']->getData();
            $description = $form['description']->getData();
            $priority = $form['priority']->getData();
            $dueDate = $form['due_date']->getData();
            
            $now = new\DateTime('now');
            
            $em = $this->getDoctrine()->getManager();
            $todo = $em->getRepository('AppBundle:Todo')->find($id);
            
            $todo->setName($name);
            $todo->setCategory($category);
            $todo->setDescription($description);
            $todo->setPriority($priority);
            $todo->setDueDate($dueDate);
            $todo->setCreateDate($now);

            $em->flush();
            
            $this->addFlash(
                    'notice',
                    'Todo Updated'
            );
            
            return $this->redirectToRoute('todo_list');
          
        }
               
         return $this->render('todo/edit.html.twig', array(
            'todo' => $todo,
             'form' => $form->createView()
        ));
           
    }
    
      /**
     * @Route("/todo/delete/{id}", name="todo_delete")
     */
    public function deleteAction($id)
    {              
        $em = $this->getDoctrine()->getManager();
        $todo = $em->getRepository('AppBundle:Todo')->find($id);
        $em->remove($todo);
        $em->flush();
        
        $this->addFlash(
                    'notice',
                    'Todo Removed'
            );
        
        return $this->redirectToRoute('todo_list');
     
           
    }
    
    
    
     /**
     * @Route("/todo/details/{id}", name="todo_details")
     */
    public function detailsAction($id)
    {              
        $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);
        return $this->render('todo/details.html.twig', array(
            'todo' => $todo
            
        ));    
           
    }
    
    
   
    
}
