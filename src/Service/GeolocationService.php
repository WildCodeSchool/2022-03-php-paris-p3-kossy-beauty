<?php

namespace App\Service;

class GeolocationService
{
    private $ipAddr;
    private $status;
    private $continent;
    private $country;
    private $region;
    private $city;
    private $district;
    private $zip;
    private $latitude;
    private $longitude;

    public function getLocation(): array
    {
        // Language (Default is 'en' for English)
        $lang = 'fr'; // en, de, es, pt-BR, fr, ja, zh-CN, ru
        // Fields to output
        $searchFields = 'status,message,continent,country,regionName,city,district,zip,lat,lon,query';
        // Get JSON object
        $jsondata = file_get_contents(
            "http://ip-api.com/php/" . $this->getIp() .
            "?lang=" . $lang .
            "&fields=" . $searchFields
        );
        // Decode
        $data = unserialize($jsondata);

        return $data;
    }

    public function getIp(): ?string
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $this->ipAddr = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->ipAddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->ipAddr = $_SERVER['REMOTE_ADDR'];
        }

        // if no valid IP address found, we assign a fake IP address to the user
        if ($this->ipAddr === null or $this->ipAddr === '127.0.0.1') {
            $this->ipAddr = '41.205.31.234';
        }

        return $this->ipAddr;
    }

    public function getStatus(): ?string
    {
        $this->status = $this->getLocation()['status'];
        return $this->status;
    }

    public function getContinent(): ?string
    {
        $this->continent = $this->getLocation()['continent'];
        return $this->continent;
    }

    public function getCountry(): ?string
    {
        $this->country = $this->getLocation()['country'];
        return $this->country;
    }

    public function getRegion(): ?string
    {
        $this->region = $this->getLocation()['regionName'];
        return $this->region;
    }

    public function getCity(): ?string
    {
        $this->city = $this->getLocation()['city'];
        return $this->city;
    }

    public function getDistrict(): ?string
    {
        $this->district = $this->getLocation()['district'];
        return $this->district;
    }

    public function getZip(): ?string
    {
        $this->zip = $this->getLocation()['zip'];
        return $this->zip;
    }

    public function getLatitude(): ?string
    {
        $this->latitude = $this->getLocation()['latitude'];
        return $this->latitude;
    }

    public function getLongitude(): ?string
    {
        $this->longitude = $this->getLocation()['longitude'];
        return $this->longitude;
    }
}
