<?php

namespace Cac\BarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BarController extends Controller
{
    /**
     * @Route("liste-bars")
     * @Template()
     */
    public function indexAction()
    {
        return array(
                // ...
            );    
    }

    /**
     * @Route("new")
     * @Template()
     */
    public function newAction()
    {
        return array(
                // ...
            );    
    }

    /**
     * @Route("/create")
     * @Template()
     */
    public function createAction()
    {
        return array(
                // ...
            );    
    }

    /**
     * @Route("/edit")
     * @Template()
     */
    public function editAction()
    {
        return array(
                // ...
            );    
    }

    /**
     * @Route("/show")
     * @Template()
     */
    public function showAction()
    {
        return array(
                // ...
            );    
    }

    /**
     * @Route("/delete")
     * @Template()
     */
    public function deleteAction()
    {
        return array(
                // ...
            );    
    }

    /**
     * @Route("/search")
     * @Template()
     */
    public function searchAction()
    {
        return array(
                // ...
            );    
    }

    /**
     * @Route("evaluate")
     * @Template()
     */
    public function evalAction()
    {
        return array(
                // ...
            );    
    }

}
