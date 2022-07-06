<?php

namespace App\Service;

/**
 * phpmd
 * @SuppressWarnings("unused")
 */
class GeolocationService
{
    // Vars used for http://ip-api.com
    // User location will be retrieved by its IP
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

    // Var used for the local json file
    // User can find a service for a specific location
    private $jsonFile = '../locationData.json';

    public function getIpLocation(): array
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
        if (
            empty($this->ipAddr) ||
            $this->ipAddr === null ||
            $this->ipAddr === '127.0.0.1'
        ) {
            $this->ipAddr = '41.205.31.234';
        }

        return $this->ipAddr;
    }

    public function getStatus(): ?string
    {
        $this->status = $this->getIpLocation()['status'];
        return $this->status;
    }

    public function getContinent(): ?string
    {
        $this->continent = $this->getIpLocation()['continent'];
        return $this->continent;
    }

    public function getCountry(): ?string
    {
        $this->country = $this->getIpLocation()['country'];
        return $this->country;
    }

    public function getRegion(): ?string
    {
        $this->region = $this->getIpLocation()['regionName'];
        return $this->region;
    }

    public function getCity(): ?string
    {
        $this->city = $this->getIpLocation()['city'];
        return $this->city;
    }

    public function getDistrict(): ?string
    {
        $this->district = $this->getIpLocation()['district'];
        return $this->district;
    }

    public function getZip(): ?string
    {
        $this->zip = $this->getIpLocation()['zip'];
        return $this->zip;
    }

    public function getLatitude(): ?string
    {
        $this->latitude = $this->getIpLocation()['latitude'];
        return $this->latitude;
    }

    public function getLongitude(): ?string
    {
        $this->longitude = $this->getIpLocation()['longitude'];
        return $this->longitude;
    }

    /**
     * User can find a service for a specific location
     */
    public function getJsonContent()
    {
        $jsonContent = file_get_contents($this->jsonFile);
        $datas = json_decode($jsonContent, true);
        return $datas;
    }

    public function getCountries(): array
    {
        $countries = [];
        $datas = $this->getJsonContent();

        foreach ($datas as $country) {
            $countries[] = $country;
        }

        return $countries;
    }

    public function getCities(string $country): array|string
    {
        $cities = [];
        $datas = $this->getCountries();

        foreach ($datas as $key => $city) {
            if (array_key_exists($country, $datas[$key])) {
                $cities[] = $datas[$key][$country]['Cities'];
            }
        }

        if (!empty($cities)) {
            return $cities;
        } else {
            return 'Aucunes villes trouvées';
        }
    }

    public function getDistricts(string $country, string $citySearch): array|string
    {
        $districts = [];
        $cities = $this->getCities($country);

        foreach ($cities as $key => $city) {
            if ($cities[$key]['Name'] === $citySearch) {
                $districts = $cities[$key]['Districts'];
            }
        }

        if (!empty($districts)) {
            return $districts;
        } else {
            return 'Aucun quartiers trouvés';
        }
    }
}
