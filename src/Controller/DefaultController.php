<?php

namespace AcMarche\Icar\Controller;

use AcMarche\Icar\Repository\IcarRemoteRepository;
use AcMarche\Icar\Utils\CoordonateUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DefaultController extends AbstractController
{
    public function __construct(private readonly IcarRemoteRepository $icarRemoteRepository)
    {
    }

    #[Route(path: '/', name: 'icar_home')]
    public function index(): Response
    {
        $communes = json_decode($this->icarRemoteRepository->searchCommunes('marche-en-famenne'), null, 512, JSON_THROW_ON_ERROR);
        $communes = $communes->communes;
        $utils = new CoordonateUtils();
        dump($communes[0]);
        dump($utils->lambertI($communes[0]->xMin, $communes[0]->yMin));
        dump($utils->lambertII($communes[0]->xMin, $communes[0]->yMin));
        dump($utils->lambertIII($communes[0]->xMin, $communes[0]->yMin));
        dump($utils->lambertIIExtend($communes[0]->xMin, $communes[0]->yMin));
        dump($utils->lamberIV($communes[0]->xMin, $communes[0]->yMin));
        dump($utils->lambert93($communes[0]->xMin, $communes[0]->yMin));
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
        $rues = json_decode($this->icarRemoteRepository->getListeRuesByCp(6900), null, 512, JSON_THROW_ON_ERROR);
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
        $localites = json_decode($this->icarRemoteRepository->getListeLocalitesByCp(6900), null, 512, JSON_THROW_ON_ERROR);
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
        $localites = json_decode($this->icarRemoteRepository->getListeLocalitesByCp(6900), null, 512, JSON_THROW_ON_ERROR);

        return $this->render(
            '@AcMarcheIcar/map/map.html.twig',
            [
                'urlExecuted' => $urlExecuted,
                'rues' => $localites->localites,
            ]
        );
    }
}
