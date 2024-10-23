<?php

namespace AcMarche\Icar\Controller;

use AcMarche\Icar\Repository\IcarRepository;
use AcMarche\Icar\Utils\CoordonateUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class DefaultController extends AbstractController
{
    public function __construct(
        private readonly IcarRepository $icarRepository,
    ) {}

    #[Route(path: '/', name: 'icar_home')]
    public function index(): Response
    {
        try {
            $communes = $this->icarRepository->getCommune();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('home');
        }

        $communes = $communes->communes;
        $marche = $communes[0];
        $urlExecuted = $this->icarRepository->urlExecuted();

        return $this->render(
            '@AcMarcheIcar/default/index.html.twig',
            [
                'urlExecuted' => $urlExecuted,
                'marche' => $marche,
            ],
        );
    }

    #[Route(path: '/rues/{localite}', name: 'icar_rues')]
    public function rues(?string $localite = null): Response
    {
        try {
            $localites = $this->icarRepository->findLocalitesByCp();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('home');
        }

        try {
            $rues = $this->icarRepository->findRuesByLocalite($localite);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('home');
        }

        $urlExecuted = $this->icarRepository->urlExecuted();

        return $this->render(
            '@AcMarcheIcar/default/rues.html.twig',
            [
                'urlExecuted' => $urlExecuted,
                'rues' => $rues,
                'localites' => $localites,
                'localiteSelected' => $localite,
            ],
        );
    }

    #[Route(path: '/localites', name: 'icar_localites')]
    public function localites(): Response
    {
        try {
            $localites = $this->icarRepository->findLocalitesByCp();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('home');
        }

        $urlExecuted = $this->icarRepository->urlExecuted();

        return $this->render(
            '@AcMarcheIcar/default/localites.html.twig',
            [
                'urlExecuted' => $urlExecuted,
                'localites' => $localites,
            ],
        );
    }

    #[Route(path: '/map/{localite}', name: 'icar_map')]
    public function map(?string $localite = null): Response
    {
        $latitude = "50.2283495";
        $longitude = "5.3413478";

        try {
            $localites = $this->icarRepository->findLocalitesByCp();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());

            return $this->redirectToRoute('home');
        }

        $localiteObject = null;
        if ($localite) {
            foreach ($localites as $item) {
                if ($item->nom === $localite) {
                    $localiteObject = $item;
                    break;
                }
            }
        }
        if (!$localiteObject) {
            try {
                $communes = $this->icarRepository->getCommune();
                $communes = $communes->communes;
                $localiteObject = $communes[0];
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());

                return $this->redirectToRoute('home');
            }
        }

        $urlExecuted = $this->icarRepository->urlExecuted();

        $point1 = CoordonateUtils::convertToGeolocalisation($localiteObject->xMin, $localiteObject->yMin);
        $point2 = CoordonateUtils::convertToGeolocalisation($localiteObject->xMax, $localiteObject->yMax);

        return $this->render(
            '@AcMarcheIcar/default/map.html.twig',
            [
                'urlExecuted' => $urlExecuted,
                'localite' => $localiteObject,
                'rues' => $localites,
                'point1' => $point1,
                'point2' => $point2,
            ],
        );
    }
}
