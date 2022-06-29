<?php

namespace AcMarche\Icar\Controller;

use AcMarche\Icar\Repository\IcarRemoteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted(data: 'ROLE_USER')]
class DefaultController extends AbstractController
{
    public function __construct(private IcarRemoteRepository $icarRemoteRepository)
    {
    }

    #[Route(path: '/', name: 'icar_home')]
    public function index(): Response
    {
        //dump($this->icarRemoteRepository->getListeRuesByCp(6900));
        $urlExecuted = $this->icarRemoteRepository->urlExecuted;

        return $this->render(
            '@AcMarcheIcar/default/index.html.twig',
            [
                'urlExecuted' => $urlExecuted,
            ]
        );
    }

    #[Route(path: '/rues', name: 'icar_rues')]
    public function rues(): Response
    {
        $rues = json_decode($this->icarRemoteRepository->getListeRuesByCp(6900));
        $urlExecuted = $this->icarRemoteRepository->urlExecuted;

        return $this->render(
            '@AcMarcheIcar/default/rues.html.twig',
            [
                'urlExecuted' => $urlExecuted,
                'rues' => $rues,
            ]
        );
    }

    #[Route(path: '/localites', name: 'icar_localites')]
    public function localites(): Response
    {
        $localites = json_decode($this->icarRemoteRepository->getListeLocalitesByCp(6900));
        $urlExecuted = $this->icarRemoteRepository->urlExecuted;

        return $this->render(
            '@AcMarcheIcar/default/localites.html.twig',
            [
                'urlExecuted' => $urlExecuted,
                'localites' => $localites,
            ]
        );
    }

    #[Route(path: '/map', name: 'icar_map')]
    public function map(): Response
    {
        $latitude = "50.2283495";
        $longitude = "5.3413478";

        $urlExecuted = $this->icarRemoteRepository->urlExecuted;
        $localites = json_decode($this->icarRemoteRepository->getListeLocalitesByCp(6900));

        return $this->render(
            '@AcMarcheIcar/map/map.html.twig',
            [
                'urlExecuted' => $urlExecuted,
                'rues' => $localites->localites,
            ]
        );
    }
}
