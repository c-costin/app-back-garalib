<?php

namespace App\Controller\Api;

use App\Entity\Appointment;
use App\Models\ApiError;
use App\Repository\AppointmentRepository;
use App\Repository\GarageRepository;
use App\Repository\UserRepository;
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
 * @Route("/api/appointment")
 * @OA\Tag(name="Appointment")
*/
class AppointmentController extends AbstractController
{
    /**
     * Retrieve the collection of Appointment
     *
     * @Route("/", name="app_api_appointment_browse", methods={"GET"})
     * 
     * @OA\Get(
     *      summary="Retrieve the collection of Appointment resources",
     *      description="Retrieve the collection of Appointment or identifie an Appointment with parameters",
     *      @OA\Parameter(
     *          name="user",
     *          in="query",
     *          description="Retrieve an Appointment resource based on the User Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Parameter(
     *          name="garage",
     *          in="query",
     *          description="Retrieve an Appointment resource based on the Garage Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Appointment::class, groups={"read:Appointment:item"})))
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
     *              @OA\Property(property="message", example="No Appointment was found"),
     *          )
     *      ),
     *      @Security(name="bearerAuth")
     * )
     * 
     * @param Request $request
     * @param AppointmentRepository $appointmentRepository
     * @return JsonResponse
     */
    public function browse(Request $request, AppointmentRepository $appointmentRepository, UserRepository $userRepository, GarageRepository $garageRepository): JsonResponse
    {
        // Parameters into query
        if ($request->query->all() !== []) {

            // Parameter "user"
            if (array_key_exists("user", $request->query->all())) {

                // Find User by Id via Query Parameter
                $user = $userRepository->find($request->query->all()["user"]);

                // Find all Appointment by User ID
                $result = $appointmentRepository->findAppointmentByUserId($request->query->all()['user']);

                if ($result !== []) {
                    // Check permission for browse the Appointments
                    if ($this->isGranted("user_browse", $user)) {
                        return $this->json($result, Response::HTTP_OK, [], ["groups" => ["read:Appointment:item"]]);
                    } else {
                        return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
                    }
                } else {
                    return $this->json(["code" => 404, "message" => "No Appointment was found"], Response::HTTP_NOT_FOUND);
                }
            }

            // Parameter "garage"
            if (array_key_exists("garage", $request->query->all())) {

                // Find User by Id via Query Parameter
                $garage = $garageRepository->find($request->query->all()["garage"]);

                // Find all Appointment by Garage ID
                $result = $appointmentRepository->findAppointmentByGarageId($request->query->all()['garage']);

                if ($result !== []) {
                    // Check permission for browse the Appointments
                    if ($this->isGranted("garage_browse", $garage)) {
                        return $this->json($result, Response::HTTP_OK, [], ["groups" => ["read:Appointment:item"]]);
                    } else {
                        return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
                    }
                } else {
                    return $this->json(["code" => 404, "message" => "No Appointment was found"], Response::HTTP_NOT_FOUND);
                }
            }
        }

        // Check permission for browse all Appointments
        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->json($appointmentRepository->findAll(), Response::HTTP_OK, [], ["groups" => ["read:Appointment:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Retrieve an Appointment
     * 
     * @Route("/{id<\d+>}", name="app_api_appointment_read", methods={"GET"})
     * 
     * @OA\Get(
     *      summary="Retrieve an Appointment resource",
     *      description="Retrieve the collection of Appointment or identifie an Appointment with parameters",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Appointment resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Appointment")
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
     *              @OA\Property(property="message", example="No Appointment was found"),
     *          )
     *      ),
     * )
     * 
     * @param Appointment|null $appointment
     * @return JsonResponse
     */
    public function read(Appointment $appointment = null): JsonResponse
    {
        if ($appointment === null) { return $this->json(["code" => 404, "message" => "No Appointment was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for read a Appointment
        if ($this->isGranted("appointment_read", $appointment)) {
            return $this->json($appointment, Response::HTTP_OK, [], ["groups" => ["read:Appointment:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Update an Appointment
     * 
     * @Route("/edit/{id<\d+>}", name="app_api_appointment_edit", methods={"PATCH"})
     * 
     * @OA\Patch(
     *      summary="Update an Appointment resource",
     *      description="Update an Appointment resource based on the Appointment Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Appointment resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      example="John Doe - Contrôle Technique - AutoGarage2000"
     *                  ),
     *                  @OA\Property(
     *                      property="details",
     *                      type="text",
     *                      example="Ma voiture, n'a que trois roue.. soyez gentil !"
     *                  ),
     *                  @OA\Property(
     *                      property="startDate",
     *                      type="date-time",
     *                      example="2023-05-26T08:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="endDate",
     *                      type="date-time",
     *                      example="2023-05-26T09:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="user",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="garage",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="type",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Appointment")
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
     *              @OA\Property(property="message", example="No Appointment was found"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="422"),
     *              @OA\Property(property="message", example="Field name errors"),
     *          )
     *          
     *      ),
     * )
     * 
     * @param Request $request
     * @param SerializerInterface $serializerInterface
     * @param ValidatorInterface $validatorInterface
     * @param Appointment|null $appointment
     * @param AppointmentRepository $appointmentRepository
     * @return JsonResponse
     */
    public function edit(Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, Appointment $appointment = null, AppointmentRepository $appointmentRepository): JsonResponse
    {
        if ($appointment === null) { return $this->json(["code" => 404, "message" => "No Appointment was found"], Response::HTTP_NOT_FOUND); }

        // Get Request Body
        $json = $request->getContent();

        // Check permission for update a Appointment
        if ($this->isGranted("appointment_edit", $appointment)) {

            // Deserialzation with entity Appointment and object Appointment in context, check and insert new modification
            $serializerInterface->deserialize($json, Appointment::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $appointment]);

            $appointment->setUpdatedAt(new \DateTimeImmutable());

            // Save Appointment into database
            $appointmentRepository->add($appointment, true);

            return $this->json($appointment, Response::HTTP_ACCEPTED, [], ["groups" => ["read:Appointment:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Create an Appointment
     *
     * @Route("/add", name="app_api_appointment_add", methods={"POST"})
     * 
     * @OA\Post(
     *      summary="Create an Appointment resource",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      nullable=false,
     *                      example="John Doe - Contrôle Technique - AutoGarage2000"
     *                  ),
     *                  @OA\Property(
     *                      property="details",
     *                      type="text",
     *                      nullable=true,
     *                      example="Ma voiture, n'a que trois roue.. soyez gentil !"
     *                  ),
     *                  @OA\Property(
     *                      property="startDate",
     *                      type="date-time",
     *                      nullable=false,
     *                      example="2023-05-26T08:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="endDate",
     *                      type="date-time",
     *                      nullable=false,
     *                      example="2023-05-26T09:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="user",
     *                      type="integer",
     *                      nullable=false,
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="garage",
     *                      type="integer",
     *                      nullable=false,
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="type",
     *                      type="integer",
     *                      nullable=false,
     *                      example="1"
     *                  ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Appointment")
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
     * @param AppointmentRepository $appointmentRepository
     * @return JsonResponse
     */
    public function add(Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, AppointmentRepository $appointmentRepository): JsonResponse
    {
        // Get Request Body
        $json = $request->getContent();

        if ($json === "") { return $this->json(["code" => 400, "message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);}

        // Deserialization with entity Appointment, insert field
        $appointment = $serializerInterface->deserialize($json, Appointment::class, 'json');

        // Check permission for add new Appointment
        if ($this->isGranted("appointment_add", $appointment)) {

            // Check constraints validation into Appointment entity
            $errors = $validatorInterface->validate($appointment);

            if (count($errors) > 0) {

                // Formatting errors messages via ApiError model
                $apiError  = new ApiError($errors);

                return $this->json(["code" => 422, $apiError->getAllMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Save Appointment into database
            $appointmentRepository->add($appointment, true);

            return $this->json($appointment, Response::HTTP_CREATED, [], ["groups" => ["read:Appointment:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove an Appointment
     * 
     * @Route("/delete/{id<\d+>}", name="app_api_appointment_delete", methods={"DELETE"})
     * 
     * @OA\Delete(
     *      summary="Remove an Appointment resource",
     *      description="Remove an Appointment resource based on the Appointment Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Appointment resource Identifier",
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
     *              @OA\Property(property="message", example="No Appointment was found"),
     *          )
     *      ),
     * )
     * 
     * @param Appointment|null $appointment
     * @param AppointmentRepository $appointmentRepository
     * @return JsonResponse
     */
    public function delete(Appointment $appointment = null, AppointmentRepository $appointmentRepository): JsonResponse
    {
        if ($appointment === null) { return $this->json(["code" => 404, "message" => "No Appointment was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for delete Appointment
        if ($this->isGranted("appointment_delete", $appointment)) {

            // Remove Appointment into database
            $appointmentRepository->remove($appointment, true);

            return $this->json(null, Response::HTTP_NO_CONTENT);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }
}