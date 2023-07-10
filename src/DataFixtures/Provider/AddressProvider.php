<?php

namespace App\DataFixtures\Provider;

class AddressProvider
{
    private $type = [
        "allée",
        "avenue",
        "boulevard",
        "chemin",
        "impasse",
        "parvis",
        "place",
        "quai",
        "route",
        "rue",
    ];

    private $address = [
        [
            "number" => "52",
            "type" => "rue",
            "name" => "jeanne wedells",
            "town" => "tours",
            "postal code" => "37100",
            "latitude" => "47.4085722",
            "longitude" => "0.708216",
        ],
        [
            "number" => "3",
            "type" => "rue",
            "name" => "jean moulin",
            "town" => "saint-pierre-des-corps",
            "postal code" => "37700",
            "latitude" => "47.3896585",
            "longitude" => "0.725959",
        ],
        [
            "number" => "170",
            "type" => "rue",
            "name" => "de la croix de perigourd",
            "town" => "saint-cyr-sur-loire",
            "postal code" => "37540",
            "latitude" => "47.4178032",
            "longitude" => "0.6563347",
        ],
        [
            "number" => "30",
            "type" => "boulevard",
            "name" => "de peringondas",
            "town" => "châteaudun",
            "postal code" => "28200",
            "latitude" => "48.0779009",
            "longitude" => "1.3409913",
        ],
        [
            "number" => "24",
            "type" => "rue",
            "name" => "du val saint-aignan",
            "town" => "châteaudun",
            "postal code" => "28200",
            "latitude" => "48.067926",
            "longitude" => "1.3259269",
        ],
        [
            "number" => "17",
            "type" => "rue",
            "name" => "za vilsain 2",
            "town" => "châteaudun",
            "postal code" => "28200",
            "latitude" => "48.0893585",
            "longitude" => "1.3439525",
        ],
        [
            "number" => "85",
            "type" => "rue",
            "name" => "jean guéhenno",
            "town" => "rennes",
            "postal code" => "35700",
            "latitude" => "48.1185028",
            "longitude" => "-1.6692318",
        ],
        [
            "number" => "1",
            "type" => "rue",
            "name" => "de la libération",
            "town" => "bédée",
            "postal code" => "35137",
            "latitude" => "48.1807828",
            "longitude" => "-1.9339816",
        ],
        [
            "number" => "18",
            "type" => "rue",
            "name" => "des leuzières",
            "town" => "saint-erblon",
            "postal code" => "35230",
            "latitude" => "48.0241009",
            "longitude" => "-1.6551151",
        ],
        [
            "number" => "84",
            "type" => "avenue",
            "name" => "général leclerc",
            "town" => "sainte-savine",
            "postal code" => "10300",
            "latitude" => "48.289253",
            "longitude" => "3.9990986",
        ],
        [
            "number" => "4b",
            "type" => "rue",
            "name" => "edmond fariat",
            "town" => "troyes",
            "postal code" => "10000",
            "latitude" => "48.2941672",
            "longitude" => "4.096045",
        ],
        [
            "number" => "23",
            "type" => "rue",
            "name" => "de la Source",
            "town" => "dierrey-saint-pierre",
            "postal code" => "10190",
            "latitude" => "48.33138",
            "longitude" => "3.8280724",
        ],
        [
            "number" => "25",
            "type" => "rue",
            "name" => "des basses verchères",
            "town" => "lyon",
            "postal code" => "69005",
            "latitude" => "45.7568809",
            "longitude" => "4.8139989",
        ],
        [
            "number" => "51",
            "type" => "rue",
            "name" => "saint-antoine",
            "town" => "lyon",
            "postal code" => "69003",
            "latitude" => "45.7632447",
            "longitude" => "4.8692772",
        ],
        [
            "number" => "57",
            "type" => "rue",
            "name" => "des roses",
            "town" => "villeurbanne",
            "postal code" => "69100",
            "latitude" => "45.7538838",
            "longitude" => "4.9067165",
        ],
        [
            "number" => "41",
            "type" => "boulevard",
            "name" => "gambetta",
            "town" => "le puy-en-velay",
            "postal code" => "43000",
            "latitude" => "45.0449536",
            "longitude" => "3.8769324",
        ],
        [
            "number" => "2",
            "type" => "rue",
            "name" => "des artisans",
            "town" => "vals-près-le-puy",
            "postal code" => "43750",
            "latitude" => "",
            "longitude" => "",
        ],
        [
            "number" => "23",
            "type" => "avenue",
            "name" => "louis jonget",
            "town" => "le puy-en-velay",
            "postal code" => "43000",
            "latitude" => "45.0163772",
            "longitude" => "3.896071",
        ],
        [
            "number" => "110",
            "type" => "rue",
            "name" => "de blanzat",
            "town" => "clermont-ferrand",
            "postal code" => "63100",
            "latitude" => "45.7902722",
            "longitude" => "3.0842072",
        ],
        [
            "number" => "7",
            "type" => "rue",
            "name" => "pierre boulanger",
            "town" => "clermont-ferrand",
            "postal code" => "63100",
            "latitude" => "45.7824186",
            "longitude" => "3.212878",
        ],
        [
            "number" => "151",
            "type" => "boulevard",
            "name" => "lafayette",
            "town" => "clermont-ferrand",
            "postal code" => "63000",
            "latitude" => "45.7671258",
            "longitude" => "3.1067184",
        ],
        [
            "number" => "213",
            "type" => "avenue",
            "name" => "des chartreux",
            "town" => "marseille",
            "postal code" => "13004",
            "latitude" => "43.3108643",
            "longitude" => "5.4006551",
        ],
        [
            "number" => "118",
            "type" => "avenue",
            "name" => "des olives",
            "town" => "marseille",
            "postal code" => "13013",
            "latitude" => "43.3287539",
            "longitude" => "5.4319783",
        ],
        [
            "number" => "68",
            "type" => "rue",
            "name" => "châteaubriand",
            "town" => "marseille",
            "postal code" => "13007",
            "latitude" => "43.2848111",
            "longitude" => "5.3582272",
        ],
        [
            "number" => "28",
            "type" => "avenue",
            "name" => "honoré serres",
            "town" => "toulouse",
            "postal code" => "31000",
            "latitude" => "43.6136274",
            "longitude" => "1.4381688",
        ],
        [
            "number" => "4",
            "type" => "chemin",
            "name" => "de nicol",
            "town" => "toulouse",
            "postal code" => "31200",
            "latitude" => "43.6253396",
            "longitude" => "1.4748484",
        ],
        [
            "number" => "21",
            "type" => "rue",
            "name" => "devic",
            "town" => "toulouse",
            "postal code" => "31400",
            "latitude" => "43.579432",
            "longitude" => "1.4517217",
        ],
        [
            "number" => "93",
            "type" => "rue",
            "name" => "michel de montaigne",
            "town" => "marmande",
            "postal code" => "47200",
            "latitude" => "44.5145453",
            "longitude" => "0.1454125",
        ],
        [
            "number" => "47",
            "type" => "avenue",
            "name" => "du dr georges neau",
            "town" => "marmande",
            "postal code" => "47200",
            "latitude" => "44.502784",
            "longitude" => "0.1720633",
        ],
        [
            "number" => "94",
            "type" => "avenue",
            "name" => "jean jaurès",
            "town" => "marmande",
            "postal code" => "47200",
            "latitude" => "44.5018098",
            "longitude" => "0.1579905",
        ],
        [
            "number" => "4",
            "type" => "boulevard",
            "name" => "henri arnauld",
            "town" => "angers",
            "postal code" => "49100",
            "latitude" => "47.4743058",
            "longitude" => "-0.5606099",
        ],
        [
            "number" => "30",
            "type" => "boulevard",
            "name" => "du doyenné",
            "town" => "angers",
            "postal code" => "49100",
            "latitude" => "47.4909567",
            "longitude" => "-0.5336919",
        ],
        [
            "number" => "81",
            "type" => "rue",
            "name" => "volney",
            "town" => "angers",
            "postal code" => "49000",
            "latitude" => "47.4620499",
            "longitude" => "-0.5419235",
        ],
        [
            "number" => "67",
            "type" => "boulevard",
            "name" => "victor hugo",
            "town" => "nantes",
            "postal code" => "44200",
            "latitude" => "47.2018261",
            "longitude" => "-1.5473742",
        ],
        [
            "number" => "38",
            "type" => "rue",
            "name" => "des fromenteaux",
            "town" => "nantes",
            "postal code" => "44200",
            "latitude" => "47.1894997",
            "longitude" => "-1.5237162",
        ],
        [
            "number" => "41",
            "type" => "rue",
            "name" => "de la contrie",
            "town" => "nantes",
            "postal code" => "44100",
            "latitude" => "47.2204806",
            "longitude" => "-1.5959988",
        ],
        [
            "number" => "45",
            "type" => "route",
            "name" => "de locronan",
            "town" => "quimper",
            "postal code" => "29000",
            "latitude" => "48.0093402",
            "longitude" => "-4.1197439",
        ],
        [
            "number" => "32",
            "type" => "rue",
            "name" => "du cosquer",
            "town" => "quimper",
            "postal code" => "29000",
            "latitude" => "47.9788368",
            "longitude" => "-4.1732364",
        ],
        [
            "number" => "59",
            "type" => "rue",
            "name" => "charles le goffic",
            "town" => "quimper",
            "postal code" => "29000",
            "latitude" => "47.9856462",
            "longitude" => "-4.0642097",
        ],
    ];

    public function getRandPostCode()
    {
        $address = $this->address[array_rand($this->address)];

        return $address["postal code"];
    }

    public function getType()
    {
        return $this->type[array_rand($this->type)];
    }

    public function getRandAddressNumberAndName(): array
    {
        $number = mt_rand(1,9999);
        $number = "$number";

        return $address = [
            "number" => $number,
            "type" => array_rand($this->type),
        ];
    }

    public function getAdrressLength(): int
    {
        return count($this->address);
    }

    public function getAdrress()
    {
        return $this->address;
    }
}