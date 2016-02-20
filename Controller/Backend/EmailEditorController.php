<?php

namespace WH\MainBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/admin/email-editor")
 */
class EmailEditorController extends Controller
{

    /**
     * Todo : Variabiliser la source email
     * Il faut que se soit l'appel au template car il ira chercher celui de APP si il existe
     *
     */
    private $folder = '/../src/CL/MainBundle/Resources/views/Emails';

    /**
     * @Route("/", name="wh_emaileditor")
     */
    public function indexAction()
    {

        $emails = array();

        $dir = $this->get('kernel')->getRootDir() . $this->folder;

        if($dossier = opendir( $dir ))
        {

            while(false !== ($fichier = readdir($dossier)))
            {

                if(preg_match('#^([a-zA-Z0-9]+)\.html\.twig#', $fichier))
                {

                    $emails[] = preg_replace('#([a-zA-Z0-9]+)\.html\.twig#', '$1', $fichier);

                }

            }

        }

        return $this->render('WHMainBundle:Backend:EmailEditor/index.html.twig', array('emails' => $emails));

    }

    /**
     * @Route("/update/{Email}", name="wh_emaileditor_update")
     */
    public function updateAction($Email, Request $request)
    {


        $file      = $this->get('kernel')->getRootDir() . $this->folder.'/'.$Email.'.html.twig';
        $content   = file_get_contents($file);

        $form = $this->createFormBuilder(array('Content' => $content))

            ->add('Content', 'textarea', array(
                'required'      => false,
            ))

            ->getForm();

        if ($request->getMethod() == 'POST') {

            $form->handleRequest($request);

            $data = $form->getData();


            $fp = fopen($file, 'r+');
            fseek($fp,0);
            fputs($fp,$data['Content']);
            fclose($fp);

            exec('php '.$this->get('kernel')->getRootDir().'/console cache:clear > /dev/null 2>&1 &');
            exec('php '.$this->get('kernel')->getRootDir().'/console cache:clear --env=prod > /dev/null 2>&1 &');


            $request->getSession()->getFlashBag()->add('success', 'Votre demande sera effective dans quelques minutes');


            return $this->redirect($this->generateUrl('whad_email_editor'));

        }

        return $this->render('WHSmartAdminBundle:EmailEditor:update.html.twig', array(
            'form' => $form->createView(),
            'Email' => $Email
        ));

    }

    public function testAction($Email, Request $request)
    {


        return $this->render('CLMainBundle:Emails:'.$Email.'.html.twig', array());

    }



}
