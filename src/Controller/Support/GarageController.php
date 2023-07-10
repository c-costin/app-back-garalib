<?php

namespace App\Controller\Support;

use App\Entity\Garage;
use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Repository\GarageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/support/garage")
 */
class GarageController extends AbstractController
{
    /**
     * Retrieve the collection of Garage
     *
     * @Route("/", name="app_support_garage_browse", methods={"GET"})
     *
     * @param GarageRepository $garageRepository
     * @return Response
     */
    public function browse(GarageRepository $garageRepository): Response
    {
        $garages = $garageRepository->findAll();

        return $this->render('support/garage/browse.html.twig', [
            "currentPage" => "garage",
            "garages" => $garages,
        ]);
    }

    /**
     * Retrieve the collection of Garage
     *
     * @Route("/membres/{id<\d+>}", name="app_support_garage_browse_member", methods={"GET"})
     *
     * @param GarageRepository $garageRepository
     * @return Response
     */
    public function browseMember(Garage $garage = null, GarageRepository $garageRepository): Response
    {
        return $this->render('support/garage/member.html.twig', [
            "currentPage" => "garage",
            "garage" => $garage,
        ]);
    }

    /**
     * Retrieve a Garage by id
     * 
     * @Route("/{id<\d+>}", name="app_support_garage_read", methods={"GET"})
     *
     * @param Garage|null $garage
     * @return Response
     */
    public function read(Garage $garage = null): Response
    {
        if ($garage === null) {
            throw $this->createNotFoundException("Aucun garage n'a été trouvé");
        }

        return $this->render('support/garage/read.html.twig', [
            "currentPage" => "garage",
            "garage" => $garage,
        ]);
    }

    /**
     * Update a Garage by id
     * 
     * @Route("/editer/{id<\d+>}", name="app_support_garage_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Garage|null $garage
     * @param GarageRepository $garageRepository
     * @return Response
     */
    public function edit(Request $request, Garage $garage = null, GarageRepository $garageRepository): Response
    {
        if ($garage === null) { throw $this->createNotFoundException("Aucun garage n'a été trouvé"); }

        // Check form is submited
        if ($request->getMethod() === 'POST') {

            // Update Garage fields
            if ($garage->getName() !== $request->request->get('garage_name')) { $garage->setName($request->request->get('garage_name')); }
            if ($garage->getRegisterNumber() !== $request->request->get('registerNumber')) { $garage->setRegisterNumber($request->request->get('registerNumber')); }
            if ($garage->getPhone() !== $request->request->get('phone')) { $garage->setPhone($request->request->get('phone')); }
            if ($garage->getEmail() !== $request->request->get('email')) { $garage->setEmail($request->request->get('email')); }

            // Save Garage into database
            $garageRepository->add($garage, true);

            return $this->redirectToRoute(
                'app_support_garage_browse',
                ["id" => $garage->getId()],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('support/garage/edit.html.twig', [
            "currentPage" => "garage",
            'garage' => $garage
        ]);
    }

    /**
     * Add a Garage
     * 
     * @Route("/ajouter", name="app_support_garage_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param GarageRepository $garageRepository
     * @return Response
     */
    public function add(Request $request, GarageRepository $garageRepository): Response
    {
        // Check form is submited
        if ($request->getMethod() === 'POST') {

            // Create new Adress
            $address = new Address();

            // Set Address fields
            if ($address->getNumber() !== $request->request->get('number')) { $address->setNumber($request->request->get('number')); }
            if ($address->getName() !== $request->request->get('address_name')) { $address->setName($request->request->get('address_name')); }
            if ($address->getType() !== $request->request->get('type')) { $address->setType($request->request->get('type')); }
            if ($address->getTown() !== $request->request->get('town')) { $address->setTown($request->request->get('town')); }
            if ($address->getPostalCode() !== $request->request->get('postalCode')) { $address->setPostalCode($request->request->get('postalCode')); }

            // Create new Garage
            $garage = new Garage();

            // Set Garage fields
            if ($garage->getName() !== $request->request->get('garage_name')) { $garage->setName($request->request->get('garage_name')); }
            if ($garage->getRegisterNumber() !== $request->request->get('registerNumber')) { $garage->setRegisterNumber($request->request->get('registerNumber')); }
            if ($garage->getPhone() !== $request->request->get('phone')) { $garage->setPhone($request->request->get('phone')); }
            if ($garage->getEmail() !== $request->request->get('email')) { $garage->setEmail($request->request->get('email')); }

            // Set Address for Garage entity
            $garage->setAddress($address);

            // Save Garage into database
            $garageRepository->add($garage, true);

            return $this->redirectToRoute('app_support_garage_browse');
        }

        return $this->render('support/garage/add.html.twig', [
            "currentPage" => "garage",
        ]);
    }

    /**
     * Delete a Garage
     *
     * @Route("/{id<\d+>}", name="app_support_garage_delete", methods={"POST"})
     *
     * @param Request $request
     * @param Garage $garage
     * @param GarageRepository $garageRepository
     * @param AddressRepository $addressRepository
     * @return Response
     */
    public function delete(Request $request, Garage $garage, GarageRepository $garageRepository, AddressRepository $addressRepository): Response
    {
        // Check is CSRF Token is valid
        if ($this->isCsrfTokenValid('delete' . $garage->getId(), $request->request->get('_token'))) {

            // Find Garage Address
            $garageAddress = $addressRepository->find($garage->getAddress());

            // Remove Garage Adress into database
            $addressRepository->remove($garageAddress, true);

             // Remove Schedule into database
            $garageRepository->remove($garage, true);
        }

        return $this->redirectToRoute('app_support_garage_browse');
    }
}
