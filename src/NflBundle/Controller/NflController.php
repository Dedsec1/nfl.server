<?php

namespace NflBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Get;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use NflBundle\Lib\NflHandler;

/**
 * @Route(service="nfl.controller.nfl")
 */
class NflController extends FOSRestController
{
    protected $nflHandler;

    public function __construct(NflHandler $nflHandler)
    {
        $this->nflHandler = $nflHandler;
    }

    /**
     * Some test action
     * @Get("/test/{year}/{type}{week}", requirements={"year" = "\d+", "type" = "(pre|reg|post|pro)", "week" = "\d{1,2}"})
     *
     * @param int $year
     * @param string $type
     * @param int $week
     * @param Request $request
     * @return View view instance
     *
     * @ApiDoc()
     */
    public function testAction($year, $type, $week, Request $request)
    {
        $this->nflHandler->init($year, $week, $type, 1, 3000);

        $view = $this->view($this->nflHandler->getSchedule(), 200);
        return $this->handleView($view);

        //return $this->render('NflBundle:Default:index.html.twig', array('name' => $name));
    }
}
