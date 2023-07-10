<?php

namespace App\Controller\Support;

use App\Entity\Type;
use App\Entity\Garage;
use App\Repository\TypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/support/type")
 */
class TypeController extends AbstractController
{
    /**
     * Update an Type
     * 
     * @Route("/editer/{id<\d+>}", name="app_support_type_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param TypeRepository $typeRepository
     * @param Garage|null $garage
     * @return Response
     */
    public function edit(Request $request, TypeRepository $typeRepository, Garage $garage = null): Response
    {
        // Find all Type by Garage Id
        $types = $typeRepository->findTypesByGarageId($garage->getId());

        if ($garage === null) { throw $this->createNotFoundException("Aucun type n'a été trouvé"); }

        if ($request->getMethod() === 'POST') {

            // Find Type by ID
            $type = $typeRepository->find($request->request->get('id'));

            // Update Type fields
            if ($type->getName() !== $request->request->get('name')) { $type->setName($request->request->get('name')); }
            if ($type->getDescription() !== $request->request->get('description')) { $type->setDescription($request->request->get('description')); }
            if ($type->getDuration() !== $request->request->get('duration')) { $type->setDuration($request->request->get('duration')); }

            // Save Type into database
            $typeRepository->add($type, true);

            return $this->redirectToRoute('app_support_user_browse', ["id" => $type->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('support/type/edit.html.twig', [
            "types" => $types,
            "garage" => $garage
        ]);
    }

    /**
     * Remove an Type
     * 
     * @Route("/{id<\d+>}", name="app_support_type_delete", methods={"POST"})
     *
     * @param Request $request
     * @param TypeRepository $typeRepository
     * @param Type $type
     * @return Response
     */
    public function delete(Request $request, TypeRepository $typeRepository, Type $type): Response
    {
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-type', $submittedToken)) {
            $typeRepository->remove($type, true);
        }

        return $this->redirectToRoute('app_support_type_edit');
    }
}
