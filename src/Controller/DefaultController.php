<?php

namespace AcMarche\Icar\Controller;

use AcMarche\Icar\Repository\IcarRemoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(private IcarRemoteRepository $icarRemoteRepository)
    {
    }

    #[Route(path: '/', name: 'icar_home')]
    public function index(): Response
    {
        dump($this->icarRemoteRepository->getListeRuesByCp(6900));
        $urlExecuted = $this->icarRemoteRepository->urlExecuted;
        $rues = json_decode($this->icarRemoteRepository->getListeRuesByCp(6900));

        return $this->render(
            '@AcMarcheIcar/default/index.html.twig',
            [
                'urlExecuted' => $urlExecuted,
                'rues' => $rues,
            ]
        );
    }
}

