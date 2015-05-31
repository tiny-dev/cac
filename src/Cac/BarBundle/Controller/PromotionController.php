<?php

namespace Cac\BarBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Cac\BarBundle\Entity\Bar;
use Cac\BarBundle\Entity\Promotion;
use Cac\BarBundle\Entity\PromotionDummy;
use Cac\BarBundle\Form\Type\BarType;
use Cac\BarBundle\Form\Type\PromotionType;
use Cac\BarBundle\Form\Type\PromotionDummyType;
/**
 * Bar controller.
 *
 * @Route("/bars/promotion")
 */
class PromotionController extends Controller
{
	/**
     * Displays the promotion and happy-hours interface for an existing Bar entity.
     *
     * @Route("/{id}/new-part3", name="promotion_create")
     * @Method("GET")
     * @Template("CacBarBundle:Promotion:createPromo.html.twig")
     */
    public function createPromoAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $plan = $em->getRepository('CacPaymentBundle:Payment')->findOneByUser($user)->getPlan();
        $entity = $em->getRepository('CacBarBundle:Bar')->find($id);
        $promotions = $entity->getPromotions();
        $promotionDummy = new PromotionDummy();

        $restriction = $em->getRepository('CacBarBundle:PromotionOptionCategory')->findOneByShortcode('restriction');
        $restrictions = $em->getRepository('CacBarBundle:PromotionOption')->findByCategory($restriction);

        if (!$entity) {
            throw $this->createNotFoundException('Le bar demandé n\'existe pas.');
        }

        $editForm = $this->createEditForm($promotionDummy, $entity);

        return array(
            'promotions'        => $promotions,
            'promotionDummy'    => $promotionDummy,
            'form'              => $editForm->createView(),
            'restrictions'      => $restrictions,
            'id'                => $entity->getId(),
            'bar'               => $entity,
            'plan'              => $plan
        );
    }

    /**
    * Creates a form to edit a Bar entity.
    *
    * @param Bar $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PromotionDummy $promotionDummy, Bar $entity)
    {
        $form = $this->createForm(new PromotionDummyType(), $promotionDummy, array(
            'action' => $this->generateUrl('promotion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 
            'submit', 
            array(
                'label' => 'Valider'
            )
        );

        return $form;
    }

    /**
     * Edits an existing Bar entity.
     *
     * @Route("/{id}", name="promotion_update")
     * @Method("PUT")
     * @Template("CacBarBundle:Promotion:createPromo.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $pm = $this->get('cac_bar.promotion_manager');

        $entity = $em->getRepository('CacBarBundle:Bar')->find($id);
        $promotions = $entity->getPromotions();

        $promotionDummy = new PromotionDummy();
        $dummyJSON = $pm->toDummyJSON($promotions);
        $promotionDummy->setPromotion($dummyJSON);

        if (!$entity) {
            throw $this->createNotFoundException('Le bar demandé n\'existe pas.');
        }

        $editForm = $this->createEditForm($promotionDummy, $entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $newPromotions = json_decode($request->request->get('cac_barbundle_promotion_dummy')['promotion'], true);
            foreach($promotions as $promotion) {
                $options = $promotion->getOptions();
                foreach($options as $option) {
                    $category = $option->getCategoryShortcode();
                    if($category == 'restriction') $category = 'condition';
                    $option->setValue($newPromotions[$promotion->getDay()][$promotion->getCategory()][$category]);
                    $em->persist($option);
                }
            }

            $em->flush();

            $user = $this->get('security.context')->getToken()->getUser();
            $plan = $em->getRepository('CacPaymentBundle:Payment')->findOneByUser($user)->getPlan();
            if ($plan === "free") {
                return $this->redirect($this->generateUrl('bars_abonnement', array('id' => $entity->getId())));
            }
            else
            {
                return $this->redirect($this->generateUrl('bars_offer', array('id' => $entity->getId())));
            }
        }

        return array(
            'bar'  => $entity,
            'form' => $editForm->createView()
        );
    }

    /**
     * Finds and displays a Bar entity.
     *
     * @Route("/{id}", name="promotion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $pm = $this->get('cac_bar.promotion_manager');
        $entity = $em->getRepository('CacBarBundle:Bar')->find($id);
        $restrictions = $em->getRepository('CacBarBundle:Restriction')->findAll();

        $promotions = $entity->getPromotions();
        $promotionDummy = new PromotionDummy();
        $dummyJSON = $pm->toDummyJSON($promotions);
        $promotionDummy->setPromotion($dummyJSON);

        $editForm = $this->createEditForm($promotionDummy, $entity);

        return array(
            'bar'      => $entity,
            'promotions' => json_decode($dummyJSON, true),
            'form'   => $editForm->createView(),
            'restrictions' => $restrictions
        );
    }
}
