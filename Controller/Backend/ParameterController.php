<?php

namespace WH\MainBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/admin/parameter")
 */
class ParameterController extends Controller
{


    /**
     * @Route("/", name="wh_admin_parameters" )
     * @return mixed
     */
    public function indexAction(Request $request)
    {

        $file = $this->get('kernel')->getRootDir() . '/config/parameters.yml';

        $form = $this->createFormBuilder()->getForm();

        $array = Yaml::parse($file);

        $tab = array();

        foreach ($array['parameters'] as $k => $v) {

            $tab[] = array(
                'name'  => $k,
                'value' => $v
            );

        }

        if ($request->getMethod() == 'POST') {

            $params['parameters'] = $request->request->get('parameters');

            $dumper = new Dumper();

            $yaml = $dumper->dump($params, 2);

            file_put_contents($file, $yaml);

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render(
            'WHMainBundle:Backend:Parameter/index.html.twig',
            array(
                'parameters' => $tab,
                'form'       => $form->createView()
            )
        );
    }


}
