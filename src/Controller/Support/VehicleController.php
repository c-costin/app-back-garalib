<?php

namespace App\Controller\Support;

use App\Entity\User;
use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/support/vehicule")
 */
class VehicleController extends AbstractController
{
    /**
     * Update a Vehicle
     * 
     * @Route("/editer/{id<\d+>}", name="app_support_vehicle_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param VehicleRepository $vehicleRepository
     * @param Vehicle|null $vehicle
     * @return Response
     */
    public function edit(Request $request, VehicleRepository $vehicleRepository, User $user = null): Response
    {
        $vehicles = $vehicleRepository->findVehicleByUserId($user->getId());

        if ($user === null) { throw $this->createNotFoundException("Aucun véhicule n'a été trouvé"); }

        if ($request->getMethod() === 'POST') {
            dd($request->request);
            // Find Type by ID
            $vehicle = $vehicleRepository->find($request->request->get('id'));

            // Update Vehcile fields
            if ($vehicle->getType() !== $request->request->get('type')) { $vehicle->setType($request->request->get('type')); }
            if ($vehicle->getBrand() !== $request->request->get('brand')) { $vehicle->setBrand($request->request->get('brand')); }
            if ($vehicle->getModel() !== $request->request->get('model')) { $vehicle->setModel($request->request->get('model')); }
            if ($vehicle->getNumberPlate() !== $request->request->get('numberPlate')) { $vehicle->setNumberPlate($request->request->get('numberPlate')); }
            if ($vehicle->getReleaseDate() !== $request->request->get('releaseDate')) { $vehicle->setReleaseDate(new \DateTime($request->request->get('releaseDate'))); }
            if ($vehicle->getMileage() !== $request->request->get('releaseDate')) { $vehicle->setMileage((int)$request->request->get('mileage')); }

            // Save Vehicle into database
            $vehicleRepository->add($vehicle, true);

            return $this->redirectToRoute('app_support_user_browse', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('support/vehicle/edit.html.twig', [
            "user" => $user,
            "vehicles" => $vehicles
        ]);
    }
}
