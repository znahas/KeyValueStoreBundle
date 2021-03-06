<?php

namespace Elcweb\KeyValueStoreBundle\Controller;

use Elcweb\KeyValueStoreBundle\Entity\KeyValue;
use Elcweb\KeyValueStoreBundle\Form\KeyValueType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @Route("/admin/keyvalue")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Show soft deleted users
        $filters = $em->getFilters();
        $filters->disable('softdeleteable');

        $items = $em->getRepository('ElcwebKeyValueStoreBundle:KeyValue')->findAll();

        return array('items' => $items);
    }

    /**
     * @Route("/create")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $keyvalue = new KeyValue();
        $form     = $this->createForm(new KeyValueType(), $keyvalue);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $dispatcher = $this->container->get('event_dispatcher');

                $dispatcher->dispatch('elcweb.keyvalue.created', new GenericEvent('', array('keyValue' => $keyvalue)));


                $this->get('session')->getFlashBag()->add('success', 'Saved.');

                return $this->redirect($this->generateUrl('elcweb_keyvaluestore_default_index'));
            }
        }

        return array('form' => $form->createView(), 'actionName' => 'Create');
    }

    /**
     * @Route("/edit/{key}")
     * @Template("ElcwebKeyValueStoreBundle:Default:create.html.twig")
     */
    public function editAction(Request $request, KeyValue $keyValue)
    {

        $form = $this->createForm(new KeyValueType(), $keyValue);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $keyValue = $form->getData();

                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch('elcweb.keyvalue.updated', new GenericEvent('', array('keyValue' => $keyValue)));

                $this->get('session')->getFlashBag()->add('success', 'Saved.');

                return $this->redirect($this->generateUrl('elcweb_keyvaluestore_default_index'));
            }
        }

        return array('form' => $form->createView(), 'actionName' => 'Edit');
    }

    /**
     * @Route("/delete/{key}")
     */
    public function deleteAction($key)
    {
        // TODO: soft delete
        $this->get('session')->getFlashBag()->add('error', "Function not implemented. Can't delete {$key}");

        return $this->redirect($this->generateUrl('elcweb_keyvaluestore_default_index'));
    }

}

