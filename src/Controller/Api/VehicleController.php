<?php

namespace App\Controller\Api;

use App\Entity\Vehicle;
use App\Models\ApiError;
use App\Repository\UserRepository;
use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/vehicle")
 * @OA\Tag(name="Vehicle")
*/
class VehicleController extends AbstractController
{
    /**
     * Retrieve the collection of Vehicle
     * 
     * @Route("/", name="app_api_vehicle_browse", methods={"GET"})
     * 
     * @OA\Get(
     *      summary="Retrieve the collection of Vehicle resources",
     *      description="Retrieve the collection of Vehicle or identifie a Vehicle with parameters",
     *      @OA\Parameter(
     *          name="user",
     *          in="query",
     *          description="Retrieve a Vehicle resource based on the User Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Vehicle::class, groups={"read:Vehicle:item"})))
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
     *          response=404, description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="404"),
     *              @OA\Property(property="message", example="No Vehicle was found"),
     *          )
     *      ),
     * )
     * 
     * @param Request $request
     * @param VehicleRepository $vehicleRepository
     * @return JsonResponse
     */
    public function browse(Request $request, VehicleRepository $vehicleRepository, UserRepository $userRepository): JsonResponse
    {
        // Parameters into query
        if ($request->query->all() !== []) {

            // Parameter "user"
            if (array_key_exists("user", $request->query->all())) {

                // Find User by Id via Query Parameter
                $user = $userRepository->find($request->query->all()["user"]);

                // Find all Vehicle by User ID
                $result = $vehicleRepository->findVehicleByUserId($request->query->all()['user']);

                if ($result !== []) {

                    // Check permission for browse the Vehicles
                    if ($this->isGranted("user_browse", $user)) {
                        return $this->json($result, Response::HTTP_OK, [], ["groups" => ["read:Vehicle:item"]]);
                    } else {
                        return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
                    }

                } else {
                    return $this->json(["code" => 404, "message" => "No Vehicle was found"], Response::HTTP_NOT_FOUND);
                }
            }
        }

        // Check permission for browse all Vehicles
        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->json($vehicleRepository->findAll(), Response::HTTP_OK, [], ["groups" => ["read:Vehicle:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Retrieve a Vehicle
     * 
     * @Route("/{id<\d+>}", name="app_api_vehicle_read", methods={"GET"})
     * 
     * @OA\Get(
     *      summary="Retrieve a Vehicle resource",
     *      description="Retrieve a Vehicle resource based on the Vehicle Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Vehicle resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Vehicle")
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
     *              @OA\Property(property="message", example="No Vehicle was found"),
     *          )
     *      ),
     * )
     * 
     * @param Vehicle|null $vehicle
     * @return JsonResponse
     */
    public function read(Vehicle $vehicle = null): JsonResponse
    {
        if ($vehicle === null) { return $this->json(["code" => 404, "message" => "No Vehicle was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for read a Vehicle
        if ($this->isGranted("vehicle_read", $vehicle)) {
            return $this->json($vehicle, Response::HTTP_OK, [], ["groups" => ["read:Vehicle:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Update a Vehicle
     * 
     * @Route("/edit/{id<\d+>}", name="app_api_vehicle_edit", methods={"PATCH"})
     * 
     * @OA\Patch(
     *      summary="Update the Vehicle resource",
     *      description="Update a Vehicle resource based on the Vehicle Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Address resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="type",
     *                      type="string",
     *                      example="Compact"
     *                  ),
     *                  @OA\Property(
     *                      property="brand",
     *                      type="string",
     *                      example="Peugeot"
     *                  ),
     *                  @OA\Property(
     *                      property="model",
     *                      type="string",
     *                      example="308"
     *                  ),
     *                  @OA\Property(
     *                      property="numberPlate",
     *                      type="string",
     *                      example="AA-123-BB"
     *                  ),
     *                  @OA\Property(
     *                      property="releaseDate",
     *                      type="string",
     *                      example="2023-05-26"
     *                  ),
     *                  @OA\Property(
     *                      property="mileage",
     *                      type="integer",
     *                      example=48005
     *                  ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Vehicle")
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
     *              @OA\Property(property="message", example="No Vehicle was found"),
     *          )
     *      ),
     * )
     * 
     * @param Vehicle|null $vehicle
     * @param Request $request
     * @param SerializerInterface $serializerInterface
     * @param ValidatorInterface $validatorInterface
     * @param VehicleRepository $vehicleRepository
     * @return JsonResponse
     */
    public function edit(Vehicle $vehicle = null, Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, VehicleRepository $vehicleRepository): JsonResponse
    {
        if ($vehicle === null) { return $this->json(["code" => 404, "message" => "No Vehicle was found"], Response::HTTP_NOT_FOUND); }

        // Get Request Body
        $json = $request->getContent();

        // Check permission for update a Vehicle
        if ($this->isGranted("vehicle_edit", $vehicle)) {

            // Deserialzation with entity Vehicle and object Vehicle in context, check and insert new modification
            $serializerInterface->deserialize($json, Vehicle::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $vehicle]);

            $vehicle->setUpdatedAt(new \DateTimeImmutable());

            // Save Appointment into database
            $vehicleRepository->add($vehicle, true);

            return $this->json($vehicle, Response::HTTP_ACCEPTED, [], ["groups" => ["read:Vehicle:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Create a Vehicle
     * 
     * @Route("/add", name="app_api_vehicle_add", methods={"POST"})
     * 
     * @OA\Post(
     *      summary="Create a Vehicle resource",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="type",
     *                      type="string",
     *                      example="Compact"
     *                  ),
     *                  @OA\Property(
     *                      property="brand",
     *                      type="string",
     *                      example="Peugeot"
     *                  ),
     *                  @OA\Property(
     *                      property="model",
     *                      type="string",
     *                      example="308"
     *                  ),
     *                  @OA\Property(
     *                      property="numberPlate",
     *                      type="string",
     *                      example="AA-123-BB"
     *                  ),
     *                  @OA\Property(
     *                      property="releaseDate",
     *                      type="string",
     *                      example="2023-05-26"
     *                  ),
     *                  @OA\Property(
     *                      property="mileage",
     *                      type="integer",
     *                      example=48005
     *                  ),
     *                  @OA\Property(
     *                      property="user",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Vehicle")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
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
     * @param VehicleRepository $vehicleRepository
     * @return JsonResponse
     */
    public function add(Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, VehicleRepository $vehicleRepository): JsonResponse
    {
        // Get Request Body
        $json = $request->getContent();

        if ($json === "") { return $this->json(["code" => 400, "message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);}

        // Deserialization with entity Vehicle, insert field
        $vehicle = $serializerInterface->deserialize($json, Vehicle::class, 'json');

        // Check permission for add new Vehicle
        if ($this->isGranted("vehicle_add", $vehicle)) {

            // Check constraints validation into Vehicle entity
            $errors = $validatorInterface->validate($vehicle);

            if (count($errors) > 0) {

                // Formatting errors messages via ApiError model
                $apiError  = new ApiError($errors);

                return $this->json(["code" => 422, $apiError->getAllMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Save Vehicle into database
            $vehicleRepository->add($vehicle, true);

            return $this->json($vehicle, Response::HTTP_CREATED, [], ["groups" => ["read:Vehicle:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove a Vehicle
     * 
     * @Route("/delete/{id<\d+>}", name="app_api_vehicle_delete", methods={"DELETE"})
     * 
     * @OA\Delete(
     *      summary="Remove a Vehicle resource",
     *      description="Remove a Vehicle resource based on the Vehicle Identifier",
     *      @OA\Parameter(
     *          name="id", in="path",
     *          required=true,
     *          description="Vehicle resource Identifier",
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
     *              @OA\Property(property="message", example="No Vehicle was found"),
     *          )
     *      ),
     * )
     * 
     * @param Vehicle|null $vehicle
     * @param VehicleRepository $vehicleRepository
     * @return JsonResponse
     */
    public function delete(Vehicle $vehicle = null, VehicleRepository $vehicleRepository): JsonResponse
    {
        if ($vehicle === null) { return $this->json(["code" => 404, "message" => "No Vehicle was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for delete Vehicle
        if ($this->isGranted("vehicle_delete", $vehicle)) {

            // Remove Vehicle into database
            $vehicleRepository->remove($vehicle, true);

            return $this->json(null, Response::HTTP_NO_CONTENT);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }
}
