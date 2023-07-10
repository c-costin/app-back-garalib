<?php

namespace App\Controller\Support;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/support/address")
 */
class AddressController extends AbstractController
{
    /**
     * Update an Address
     * 
     * @Route("/editer/{id<\d+>}", name="app_support_address_edit")
     * 
     * @param Request $request
     * @param AddressRepository $addressRepository
     * @param Address|null $address
     * @return Response
     */
    public function edit(Request $request, AddressRepository $addressRepository, Address $address = null): Response
    {
        if ($address === null) { throw $this->createNotFoundException("Aucune adresse n'a été trouvée"); }

        if ($request->getMethod() === 'POST') {

            // Update Address fields
            if ($address->getNumber() !== $request->request->get('number')) { $address->setNumber($request->request->get('number')); }
            if ($address->getName() !== $request->request->get('address_name')) { $address->setName($request->request->get('address_name')); }
            if ($address->getType() !== $request->request->get('type')) { $address->setType($request->request->get('type')); }
            if ($address->getTown() !== $request->request->get('town')) { $address->setTown($request->request->get('town')); }
            if ($address->getPostalCode() !== $request->request->get('postalCode')) { $address->setPostalCode($request->request->get('postalCode')); }

            // Save Address into database
            $addressRepository->add($address, true);

            return $this->redirectToRoute('app_support_dashboard_default', ["id" => $address->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('support/address/edit.html.twig', [
            "address" => $address
        ]);
    }

    /**
     * Delete an Address
     *
     * @Route("/supprimer/{id<\d+>}", name="app_support_address_delete")
     * 
     * @param Request $request
     * @param Address $address
     * @param AddressRepository $addressRepository
     * @return Response
     */
    public function delete(Request $request, Address $address, AddressRepository $addressRepository): Response
    {

        // Check is CSRF Token is valid
        if ($this->isCsrfTokenValid('delete' . $address->getId(), $request->request->get('_token'))) {

             // Remove Address into database
            $addressRepository->remove($address, true);
        }

        return $this->redirectToRoute('app_support_dashboard_defautl');
        // if ($request->request->get('_routeName') === "user") {
        //     return $this->redirectToRoute('app_support_user_browse');
        // } else {
        //     return $this->redirectToRoute('app_support_garage_browse');
        // }
    }
}
