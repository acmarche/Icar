<?php

namespace AcMarche\Icar\Repository;

/**
 * http://geoservices.wallonie.be/geolocalisation/doc/ws/index.xhtml
 */
class IcarRemoteRepository
{
    use ConnectionTrait;

    public function __construct()
    {
        $this->connect();
    }

    public function getVersion(): ?string
    {
        return $this->executeRequest($this->base_uri.'/getVersion');
    }

    public function getListeCommunes(): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeCommunes');
    }

    public function searchCommunes(string $name): ?string
    {
        return $this->executeRequest($this->base_uri.'/searchCommunes/'.$name);
    }

    public function getListeLocalites(): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeLocalites');
    }

    public function getListeLocalitesByCp(int $postalCode): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeLocalitesByCp/'.$postalCode);
    }

    public function getListeLocalitesByIns(int $ins): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeLocalitesByIns/'.$ins);
    }

    public function searchLocalites(string $name): ?string
    {
        return $this->executeRequest($this->base_uri.'/searchLocalites/'.$name);
    }

    public function getListeRuesByCp(int $postalCode): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeRuesByCp/'.$postalCode);
    }

    public function getListeRuesOfAllCpsOfCommuneByCp(int $postalCode): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeRuesOfAllCpsOfCommuneByCp/'.$postalCode);
    }

    public function getListeRuesByLocalite(string $name): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeRuesByLocalite/'.$name);
    }

    public function getListeRuesByCpAndNomApprox(int $postalCode, string $name): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeRuesByCpAndNomApprox/'.$postalCode.'/'.$name);
    }

    public function getListeRuesByLocaliteAndNomApprox(string $locality, string $street): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeRuesByCpAndNomApprox/'.$locality.'/'.$street);
    }

    public function getListeRuesOfAllCpsOfCommuneByCpAndNomApprox(int $postalCode, string $name): ?string
    {
        return $this->executeRequest(
            $this->base_uri.'/getListeRuesOfAllCpsOfCommuneByCpAndNomApprox/'.$postalCode.'/'.$name
        );
    }

    public function getListeRuesByCommune(string $name): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeRuesByCommune/'.$name);
    }

    public function searchRues(string $name): ?string
    {
        return $this->executeRequest($this->base_uri.'/searchRues/'.$name);
    }

    public function getNearestRue(int $x, int $y): ?string
    {
        return $this->executeRequest($this->base_uri.'/getNearestRue/'.$x.'/'.$y);
    }

    public function getListPositionsByCpAndRue(int $postalCode, string $street): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListPositionsByCpAndRue/'.$postalCode.'/'.$street);
    }

    public function getPositionByCpAndRue(int $postalCode, string $street): ?string
    {
        return $this->executeRequest($this->base_uri.'/getPositionByCpAndRue/'.$postalCode.'/'.$street);
    }

    public function getListPositionsByCpRueAndNumero(int $postalCode, string $street, string $number): ?string
    {
        return $this->executeRequest(
            $this->base_uri.'/getListPositionsByCpRueAndNumero/'.$postalCode.'/'.$street.'/'.$number
        );
    }

    public function getPositionByCpRueAndNumero(int $postalCode, string $street, string $number): ?string
    {
        return $this->executeRequest(
            $this->base_uri.'/getPositionByCpRueAndNumero/'.$postalCode.'/'.$street.'/'.$number
        );
    }

    public function getListPositionsByCpLocaliteRueAndNumero(
        int $postalCode,
        string $locality,
        string $street,
        string $number
    ): ?string {
        return $this->executeRequest(
            $this->base_uri.'/getListPositionsByCpLocaliteRueAndNumero/'.$postalCode.'/'.$locality.'/'.$street.'/'.$number
        );
    }

    public function getPositionByCpLocaliteRueAndNumero(
        int $postalCode,
        string $locality,
        string $street,
        string $number
    ): ?string {
        return $this->executeRequest(
            $this->base_uri.'/getPositionByCpLocaliteRueAndNumero/'.$postalCode.'/'.$locality.'/'.$street.'/'.$number
        );
    }

    public function searchPosition(string $address): ?string
    {
        return $this->executeRequest($this->base_uri.'/searchPosition/'.$address);
    }

    public function searchPositionScored(string $address): ?string
    {
        return $this->executeRequest($this->base_uri.'/searchPositionScored/'.$address);
    }

    public function getNearestPosition(int $x, int $y): ?string
    {
        return $this->executeRequest($this->base_uri.'/getNearestPosition/'.$x.'/'.$y);
    }

    public function searchAll($address): ?string
    {
        return $this->executeRequest($this->base_uri.'/searchAll/'.$address);
    }

    public function getProvinceByCp(int $postalCode): ?string
    {
        return $this->executeRequest($this->base_uri.'/getProvinceByCp/'.$postalCode);
    }

    public function getListeProvincesRW(): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeProvincesRW/');
    }

    public function getListeLocalitesByProvince(string $name): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeLocalitesByProvince/'.$name);
    }

    public function getListeCpsByProvince(string $name): ?string
    {
        return $this->executeRequest($this->base_uri.'/getListeCpsByProvince/'.$name);
    }

    public function processBatchGeolocalisation(array $addresses): ?string
    {
        $req = json_encode($addresses);

        return $this->executeRequest($this->base_uri.'/processBatchGeolocalisation/'.$req);
    }

    public function getListPositionsBySmartGeocoding(
        int $postalCode,
        string $locality,
        string $street,
        string $number
    ): ?string {
        return $this->executeRequest(
            $this->base_uri.'/getListPositionsBySmartGeocoding/'.$postalCode.'/'.$locality.'/'.$street.'/'.$number
        );
    }

    public function getPositionBySmartGeocoding(
        int $postalCode,
        string $locality,
        string $street,
        string $number
    ): ?string {
        return $this->executeRequest(
            $this->base_uri.'/getPositionBySmartGeocoding/'.$postalCode.'/'.$locality.'/'.$street.'/'.$number
        );
    }
}