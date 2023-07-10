<?php

namespace App\Controller\Api;

use App\Entity\Garage;
use App\Models\ApiError;
use App\Repository\AddressRepository;
use App\Repository\GarageRepository;
use App\Service\GeolocalizerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
 * @Route("/api/garage")
 * @OA\Tag(name="Garage")
*/
class GarageController extends AbstractController
{
    /**
     * Retrieve the collection of Garage
     * 
     * @Route("/", name="app_api_garage_browse", methods={"GET"})
     * 
     * @OA\Get(
     *      summary="Retrieve the collection of Garage resources",
     *      description="Retrieve the collection of Garage or identifie a Garage with parameters",
     *      @OA\Parameter(
     *          name="name",
     *          in="query",
     *          description="Retrieve the collection of Garage resources based on Garage name property",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="address",
     *          in="query",
     *          description="Retrieve the collection of Garage resources based on address via geolocalization",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Garage::class, groups={"read:Garage:item"})))
     *      ),
     *      @OA\Response(
     *          response=404, description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="404"),
     *              @OA\Property(property="message", example="No Garage was found"),
     *          )
     *      ),
     * )
     * @Security(name=null)
     * 
     * @param Request $request
     * @param GarageRepository $garageRepository
     * @return JsonResponse
     */
    public function browse(Request $request, GarageRepository $garageRepository, GeolocalizerService $geolocalizerService): JsonResponse
    {
        // Parameters into query
        if ($request->query->all() !== []) {

            // Parameter "name"
            if (array_key_exists('name', $request->query->all())) {

                // Find all Garage by name
                $result = $garageRepository->findGarageByName($request->query->all()['name']);

                if ($result !== []) {
                    return $this->json($result, Response::HTTP_OK, [], ["groups" => ["read:Garage:item"]]);
                } else {
                    return $this->json(["code" => 404, "message" => "No Garage was found"], Response::HTTP_NOT_FOUND);
                }
            }

            // Parameter "address"
            if (array_key_exists('address', $request->query->all())) {

                // Formatting string to URL string
                $addressSearch = urlencode($request->query->all()['address']);

                // Find all Garage by address and radiate via GeolocalizerService
                $result = $geolocalizerService->findGarageByAddress($addressSearch, 100);

                if ($result !== []) {
                    return $this->json($result, Response::HTTP_OK, []);
                } else {
                    return $this->json(["code" => 404, "message" => "No Garage was found"], Response::HTTP_NOT_FOUND);
                }
            }
        }

        return $this->json($garageRepository->findAll(), Response::HTTP_OK, [], ["groups" => ["read:Garage:item"]]);
    }

    /**
     * Retrieve a Garage
     * 
     * @Route("/{id<\d+>}", name="app_api_garage_read", methods={"GET"})
     * 
     * @OA\Get(
     *      summary="Retrieve a Garage resource",
     *      description="Retrieve a Garage resource based on the Garage Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path", required=true,
     *          description="Garage resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Garage")
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="404"),
     *              @OA\Property(property="message", example="No Garage was found"),
     *          )
     *      ),
     * )
     * @Security(name=null)
     * 
     * @param Garage|null $garage
     * @return JsonResponse
     */
    public function read(Garage $garage = null): JsonResponse
    {
        if ($garage === null) { return $this->json(["code" => 404, "message" => "No Garage was found"], Response::HTTP_NOT_FOUND);}

        return $this->json($garage, Response::HTTP_OK, [], ["groups" => ["read:Garage:item"]]);
    }

    /**
     * Update a Garage
     * 
     * @Route("/edit/{id<\d+>}", name="app_api_garage_edit", methods={"PATCH"})
     * 
     * @OA\Patch(
     *      summary="Update a Garage resource",
     *      description="Update a Garage resource based on the Garage Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Garage resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="AutoGarage2000"
     *                  ),
     *                  @OA\Property(
     *                      property="registerNumber",
     *                      type="integer",
     *                      example="231 190 987 12315"
     *                  ),
     *                  @OA\Property(
     *                      property="phone",
     *                      type="string",
     *                      example="0412258462"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      example="john.doe@mail.com"
     *                  ),
     *                  @OA\Property(
     *                      property="rating",
     *                      type="integer",
     *                      example="3.9"
     *                  ),
     *                  @OA\Property(
     *                      property="address",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="users",
     *                      type="array",
     *                      @OA\Items(example="1")
     *                  ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Garage")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="401"),
     *              @OA\Property(property="message", example="JWT Token not found"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="403"),
     *              @OA\Property(property="message", example="Access Denied"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="404"),
     *              @OA\Property(property="message", example="No Garage was found"),
     *          )
     *      ),
     * )
     * 
     * @param Garage|null $garage
     * @param Request $request
     * @param SerializerInterface $serializerInterface
     * @param ValidatorInterface $validatorInterface
     * @param GarageRepository $garageRepository
     * @return JsonResponse
     */
    public function edit(Garage $garage = null, Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, GarageRepository $garageRepository): JsonResponse
    {
        if ($garage === null) { return $this->json(["code" => 404, "message" => "No Garage was found"], Response::HTTP_NOT_FOUND); }

        // Get Request Body
        $json = $request->getContent();

        // Check permission for update a Garage
        if ($this->isGranted("garage_edit", $garage)) {

            // Deserialzation with entity Garage and object Garage in context, check and insert new modification
            $serializerInterface->deserialize($json, Garage::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $garage]);

            $garage->setUpdatedAt(new \DateTimeImmutable());

            // Save Garage into database
            $garageRepository->add($garage, true);

            return $this->json($garage, Response::HTTP_ACCEPTED, [], ["groups" => ["read:Garage:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Add a Garage
     * 
     * @Route("/add", name="app_api_garage_add", methods={"POST"})
     * 
     * @OA\Post(
     *      summary="Create a Garage resource",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="AutoGarage2000"
     *                  ),
     *                  @OA\Property(
     *                      property="registerNumber",
     *                      type="integer",
     *                      example="231 190 987 12315"
     *                  ),
     *                  @OA\Property(
     *                      property="phone",
     *                      type="string",
     *                      example="0412258462"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      example="john.doe@mail.com"
     *                  ),
     *                  @OA\Property(
     *                      property="rating",
     *                      type="integer",
     *                      example="3.9"
     *                  ),
     *                  @OA\Property(
     *                      property="address",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="users",
     *                      type="array",
     *                      @OA\Items(example="1")
     *                  ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Garage")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="400"),
     *              @OA\Property(property="message", example="Invalid JSON"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="401"),
     *              @OA\Property(property="message", example="JWT Token not found"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="403"),
     *              @OA\Property(property="message", example="Access Denied"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="422"),
     *              @OA\Property(property="message", type="array", @OA\Items(example="Field: This value should not be blank")),
     *          )
     *      ),
     * )
     * 
     * @param Request $request
     * @param SerializerInterface $serializerInterface
     * @param ValidatorInterface $validatorInterface
     * @param GarageRepository $garageRepository
     * @return JsonResponse
     */
    public function add(Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, GarageRepository $garageRepository): JsonResponse
    {
        // Get Request Body
        $json = $request->getContent();

        if ($json === "") { return $this->json(["code" => 400, "message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);}

        // Deserialization with entity Garage, insert field
        $garage = $serializerInterface->deserialize($json, Garage::class, 'json', []);

        // Check permission for add new Garage
        if ($this->isGranted("garage_add", $garage)) {

            // Check constraints validation into Garage entity
            $errors = $validatorInterface->validate($garage);

            if (count($errors) > 0) {

                // Formatting errors messages via ApiError model
                $apiError  = new ApiError($errors);

                return $this->json(["code" => 422, $apiError->getAllMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Save Garage into database
            $garageRepository->add($garage, true);

            return $this->json($garage, Response::HTTP_CREATED, [], ["groups" => ["read:Garage:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove a Garage
     * 
     * @Route("/delete/{id<\d+>}", name="app_api_garage_delete", methods={"DELETE"})
     * 
     * @OA\Delete(
     *      summary="Remove a Garage resource",
     *      description="Remove a Garage resource based on the Garage Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Garage resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Success"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="401"),
     *              @OA\Property(property="message", example="JWT Token not found"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="403"),
     *              @OA\Property(property="message", example="Access Denied"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="404"),
     *              @OA\Property(property="message", example="No Garage was found"),
     *          )
     *      ),
     * )
     * 
     * @param Garage|null $garage
     * @param GarageRepository $garageRepository
     * @param AddressRepository $addressRepository
     * @return JsonResponse
     */
    public function delete(Garage $garage = null, GarageRepository $garageRepository, AddressRepository $addressRepository): JsonResponse
    {
        if ($garage === null) { return $this->json(["code" => 404, "message" => "No Garage was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for delete Garage
        if ($this->isGranted("garage_delete", $garage)) {

            // Find Garage Address
            $garageAddress = $addressRepository->find($garage->getAddress());

            // Remove Garage Adress into database
            $addressRepository->remove($garageAddress, true);

            // Remove Garage into database
            $garageRepository->remove($garage, true);

            return $this->json(null, Response::HTTP_NO_CONTENT);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }
}