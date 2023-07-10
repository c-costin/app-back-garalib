<?php

namespace App\DataFixtures\Provider;

class VehicleProvider
{
    private $vehicleType = [
        "citadine-polyvalente",
        "compacte",
        "berline",
        "monospace",
        "suv-4x4-pickup",
        "coupe",
        "utilitaire",
        "moto-quad-scooter",
        "break",
        "cabriolet",
        "collection-youngtimer",
        "camping car",
    ];

    private $brands = [
        "audi",
        "peaugeot",
        "skoda",
        "seat",
        "volvo",
        "renault",
        "mazada",
        "alpine",
        "austin",
        "alfa romeo",
        "bmw",
        "porsche",
        "toyota",
        "jeep",
        "kia",
        "mercedes",
        "opel",
        "nissan",
        "volkswagen"
    ];

    private $models = [
        "serie 1",
        "serie 2",
        "serie 3",
        "106",
        "206",
        "306",
        "406",
        "108",
        "208",
        "308",
        "408",
        "1008",
        "2008",
        "3008",
        "5008",
        "touran",
        "golf",
        "touareg",
        "tiguan",
        "t-roc",
        "991",
        "718",
        "V60",
        "V90",
        "C40",
        "S60",
        "XC90",
        "XC60",
        "XC40",
        "auris",
        "aygo",
        "yaris",
        "c-hr",
        "corolla",
        "hilux",
        "rav 4",
        "supra",
        "clio",
        "captur",
        "espace",
        "kadjar",
        "kangoo",
        "megane",
        "r5",
        "r21",
        "twingo",
        "talisman",
        "scenic",
        "master",
        "trafic",
        "transporter",
        "boxer",
        "rifter",
        "astra",
        "corsa",
        "mokka"
    ];

    public function getVehicleType()
    {
        return $this->vehicleType[array_rand($this->vehicleType)];
    }

    public function getVehicleBrand()
    {
        return $this->brands[array_rand($this->brands)];
    }

    public function getVehicleModel()
    {
        return $this->models[array_rand($this->models)];
    }
}