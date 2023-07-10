<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\AddressProvider;
use App\DataFixtures\Provider\PhoneProvider;
use App\DataFixtures\Provider\ScheduleProvider;
use App\DataFixtures\Provider\VehicleProvider;
use App\Entity\Address;
use App\Entity\Appointment;
use App\Entity\Garage;
use App\Entity\Review;
use App\Entity\Schedule;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\Vehicle;
use DateInterval;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;

class AppFixtures extends Fixture
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    private function truncate()
    {
        $this->connection->executeQuery('SET foreign_key_checks = 0');
        $this->connection->executeQuery('TRUNCATE TABLE address');
        $this->connection->executeQuery('TRUNCATE TABLE appointment');
        $this->connection->executeQuery('TRUNCATE TABLE garage');
        $this->connection->executeQuery('TRUNCATE TABLE review');
        $this->connection->executeQuery('TRUNCATE TABLE type');
        $this->connection->executeQuery('TRUNCATE TABLE schedule');
        $this->connection->executeQuery('TRUNCATE TABLE user');
        $this->connection->executeQuery('TRUNCATE TABLE user_garage');
        $this->connection->executeQuery('TRUNCATE TABLE vehicle');
    }

    public function load(ObjectManager $manager): void
    {
        // Truncate Database
        $this->truncate();

        // Instanciation FakerPHP
        $faker = Factory::create("fr_FR");

        // Dataset seed
        $faker->seed(428);

        // Adding dataset provider
        $faker->addProvider(new AddressProvider($faker));
        $faker->addProvider(new PhoneProvider($faker));
        $faker->addProvider(new VehicleProvider($faker));
        $faker->addProvider(new ScheduleProvider($faker));

        // ======== ADDRESS ========
        $randAddress = [];
        for ($i=0; $i < 320; $i++) {
            $address = new Address();
            $address->setNumber($faker->buildingNumber());
            $address->setType($faker->getType());
            $address->setName($faker->streetName());
            $address->setTown($faker->city());
            $address->setPostalCode($faker->getRandPostCode());
            $manager->persist($address);
            $randAddress[] = $address;
        }

        // Garage Address
        $garageAddress = [];
        for ($i=0; $i < $faker->getAdrressLength(); $i++) {
            $address = $faker->getAdrress()[$i];
            $addressPhysic = new Address();
            $addressPhysic->setNumber($address["number"]);
            $addressPhysic->setType($address["type"]);
            $addressPhysic->setName($address["name"]);
            $addressPhysic->setTown($address["town"]);
            $addressPhysic->setPostalCode($address["postal code"]);
            $addressPhysic->setLatitude($address["latitude"]);
            $addressPhysic->setLongitude($address["longitude"]);
            $manager->persist($addressPhysic);
            $garageAddress[] = $addressPhysic;
        }

        // ======== USER ========
        // ADMIN
        $userAdmin = new User();
        $userAdmin->setFirstname('admin');
        $userAdmin->setLastname('admin');
        $userAdmin->setEmail('admin@admin.com');
        $userAdmin->setPassword('$2y$13$.PJiDK3kq2C4owW5RW6Z3ukzRc14TJZRPcMfXcCy9AyhhA9OMK3Li');
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPhone($faker->getPhoneNumber());
        $userAdmin->setDateOfBirth(new \DateTime());
        $manager->persist($userAdmin);

        // MANAGER
        $managers = [];
        $userManager = new User();
        $userManager->setFirstname('manager');
        $userManager->setLastname('manager');
        $userManager->setEmail('manager@manager.com');
        $userManager->setPassword('$2y$13$/U5OgXbXusW7abJveoqeyeTZZBDrq/Lzh8Gt1RXnEDbT2xJqbv3vi');
        $userManager->setRoles(["ROLE_MANAGER"]);
        $userManager->setPhone($faker->getPhoneNumber());
        $userManager->setDateOfBirth(new \DateTime());
        $managers[] = $userManager;
        $manager->persist($userManager);

        // MEMBER
        $userMember = new User();
        $userMember->setFirstname('member');
        $userMember->setLastname('member');
        $userMember->setEmail('member@member.com');
        $userMember->setPassword('$2y$13$ODzg0zRsS66aIZu8iHbvVeHj0gRMNcCazOC6THnC0Svfq0dXXpY7K');
        $userMember->setRoles(["ROLE_MEMBER"]);
        $userMember->setPhone($faker->getPhoneNumber());
        $userMember->setDateOfBirth(new \DateTime());
        $manager->persist($userMember);

        // USER
        $users = [];
        $userUser = new User();
        $userUser->setFirstname('user');
        $userUser->setLastname('user');
        $userUser->setEmail('user@user.com');
        $userUser->setPassword('$2y$13$crh.ZxCq.eiU7AXEMLXaMuVW.h0SRA7X2IT3mRvVUcFKwOfu4/l6C');
        $userUser->setRoles(["ROLE_USER"]);
        $userUser->setPhone($faker->getPhoneNumber());
        $userUser->setDateOfBirth(new \DateTime());
        $users[] = $userUser;
        $manager->persist($userUser);

        // USER Random
        for ($i=1; $i < 200; $i++) {
            $userUser = new User();
            $firstName = mb_strtolower($faker->firstName());
            $lastName = mb_strtolower($faker->lastName());
            $userUser->setFirstname($firstName);
            $userUser->setLastname($lastName);
            $userUser->setEmail("{$firstName}.{$lastName}@mail.com");
            $userUser->setPassword('$2y$13$crh.ZxCq.eiU7AXEMLXaMuVW.h0SRA7X2IT3mRvVUcFKwOfu4/l6C');
            $userUser->setRoles(["ROLE_USER"]);
            $userUser->setPhone($faker->getPhoneNumber());
            $userUser->setAddress($randAddress[$i]);
            $userUser->setDateOfBirth($faker->dateTimeBetween('-70 years', '-18 years'));
            $manager->persist($userUser);
            $users[] = $userUser;
        }

        // MANAGER Random
        for ($i=1; $i < $faker->getAdrressLength(); $i++) {
            $key = 200 + $i;
            $userManager = new User();
            $firstName = mb_strtolower($faker->firstName());
            $lastName = mb_strtolower($faker->lastName());
            $userManager->setFirstname($firstName);
            $userManager->setLastname($lastName);
            $userManager->setEmail("{$firstName}.{$lastName}@mail.com");
            $userManager->setPassword('$2y$13$/U5OgXbXusW7abJveoqeyeTZZBDrq/Lzh8Gt1RXnEDbT2xJqbv3vi');
            $userManager->setRoles(["ROLE_MANAGER"]);
            $userManager->setPhone($faker->getPhoneNumber());
            $userManager->setDateOfBirth($faker->dateTimeBetween('-70 years', '-18 years'));
            $userManager->setAddress($randAddress[$key]);
            $manager->persist($userManager);
            $managers[] = $userManager;
        }

        // MANAGER Random
        $members = [];
        for ($i=0; $i < ($faker->getAdrressLength()*2); $i++) {
            $key = 240 + $i;
            $userMember = new User();
            $firstName = mb_strtolower($faker->firstName());
            $lastName = mb_strtolower($faker->lastName());
            $userMember->setFirstname($firstName);
            $userMember->setLastname($lastName);
            $userMember->setEmail("{$firstName}.{$lastName}@mail.com");
            $userMember->setPassword('$2y$13$/U5OgXbXusW7abJveoqeyeTZZBDrq/Lzh8Gt1RXnEDbT2xJqbv3vi');
            $userMember->setRoles(["ROLE_MENBER"]);
            $userMember->setPhone($faker->getPhoneNumber());
            $userMember->setDateOfBirth($faker->dateTimeBetween('-70 years', '-18 years'));
            $userMember->setAddress($randAddress[$key]);
            $manager->persist($userMember);
            $members[] = $userMember;
        }

        // ======== GARAGE =======
        $garages = [];
        for ($i=0; $i < $faker->getAdrressLength(); $i++) {
            $garage = new Garage();
            $name = $faker->company();
            $email = str_replace(".", "", $name);
            $email = mb_strtolower(str_replace(" ", "-", $email));
            $garage->setName($name);
            $siret = intval($faker->siret());
            $garage->setRegisterNumber($siret);
            $garage->setPhone($faker->getPhoneNumber());
            $garage->setEmail("{$email}@mail.com");
            $garage->addUser($managers[$i]);
            if (mt_rand(0,1) === 0) {
                $garage->addUser($members[array_rand($members)]);
            } else {
                $garage->addUser($members[array_rand($members)]);
                $garage->addUser($members[array_rand($members)]);
            }
            $garage->setAddress($garageAddress[$i]);
            $garage->setRating($faker->randomFloat(1, 1, 5));
            $manager->persist($garage);
            $garages[] = $garage;
        }


        // ======== SCHEDULE ========
        for ($i=0; $i < $faker->getAdrressLength(); $i++) {
            $garage = $garages[$i];
            for ($am=0; $am < $faker->getScheduleDaysLength(); $am++) {
                $schedule = new Schedule();
                $schedule->setDay($faker->getScheduleDays()[$am]);
                $schedule->setStartTime("07:00");
                $schedule->setEndTime("12:00");
                $schedule->setGarage($garage);
                $manager->persist($schedule);
            }
            for ($pm=0; $pm < ($faker->getScheduleDaysLength() - 1); $pm++) {
                $schedule = new Schedule();
                $schedule->setDay($faker->getScheduleDays()[$pm]);
                $schedule->setStartTime("14:00");
                $schedule->setEndTime("18:00");
                $schedule->setGarage($garage);
                $manager->persist($schedule);
            }
        }

        // ======== TYPE ========
        $types = [];
        for ($i=0; $i < $faker->getAdrressLength(); $i++) {
            $typeList = [
                "contrôle technique",
                "contrôle annuel",
                "vidange"
            ];

            for ($t=0; $t < 3; $t++) {
                $type = new Type();
                $type->setName($typeList[$t]);
                $rendDuration = mt_rand(0,2);
                if ($rendDuration === 0) {
                    $type->setDuration(60);
                } 
                if ($rendDuration === 1) {
                    $type->setDuration(45);
                }
                if ($rendDuration === 2) {
                    $type->setDuration(30);
                }
                $type->setGarage($garages[$i]);
                $manager->persist($type);
                $types[] = $type;
            }
        }

        // ======== APPOINTMENT ========
        for ($i=0; $i < count($types); $i++) {
            for ($a=0; $a < 3; $a++) {
                $type = $types[$i];
                $garage = $types[$i]->getGarage();
                $user = $users[array_rand($users)];
                $appointment = new Appointment();
                $appointment->setTitle("{$user->getFirstname()} {$user->getLastname()} - {$garage->getName()} - {$type->getName()}");
                $datetime = $faker->dateTimeBetween('-4 years', '+4 years');
                if (mt_rand(0,1) === 1) {
                    $datetime->setTime(mt_rand(8, 11),0);
                } else {
                    $datetime->setTime(mt_rand(14, 17),0);
                }
                $datetimeImmutable = \DateTimeImmutable::createFromMutable($datetime);
                $appointment->setStartDate($datetimeImmutable);
                $appointment->setEndDate($datetimeImmutable->add(new \DateInterval("PT{$type->getDuration()}M")));
                $appointment->setUser($user);
                $appointment->setGarage($garage);
                $appointment->setType($type);
                $manager->persist($appointment);
            }
        }

        // ======== VEHICLE ========
        for ($i=0; $i <= 250; $i++) { 
            $vehicle = new Vehicle();
            $vehicle->setBrand($faker->getVehicleBrand());
            $vehicle->setModel($faker->getVehicleModel());
            $vehicle->setReleaseDate($faker->dateTimeBetween('-30 years'));
            $vehicle->setMileage(mt_rand(1000, 440000));
            $vehicle->setType($faker->getVehicleType());
            $vehicle->setNumberPlate($faker->bothify('??-###-??'));
            if (mt_rand(0,100) === 1) {
                $vehicle->setUser($users[0]);
            } else {
                $vehicle->setUser($users[array_rand($users)]);
            }
            $manager->persist($vehicle);
        }

        // ======== REVIEW ========
        for ($i=0; $i <= 300; $i++) { 
            $review = new Review();
            $review->setTitle($faker->sentence());
            $review->setRating($faker->randomFloat(1, 1, 5));
            if (mt_rand(0,30) === 1) {
                $review->setUser($users[0]);
            } else {
                $review->setUser($users[array_rand($users)]);
            }
            $review->setGarage($garages[array_rand($garages)]);
            $manager->persist($review);
        }

        $manager->flush();
    }
}
