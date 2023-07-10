<?php

namespace App\Controller\Api;

use App\Entity\Schedule;
use App\Models\ApiError;
use App\Repository\GarageRepository;
use App\Repository\ScheduleRepository;
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
 * @Route("/api/schedule")
 * @OA\Tag(name="Schedule")
 */
class ScheduleController extends AbstractController
{
    /**
     * Retrieve the collection of Schedule
     * 
     * @Route("/", name="app_api_schedule_browse", methods={"GET"})
     * 
     * @OA\Get(
     *      summary="Retrieve the collection of Schedule resources",
     *      description="Retrieve the collection of Schedule or identifie a Schedule with parameters",
     *      @OA\Parameter(
     *          name="garage",
     *          in="query",
     *          description="Retrieve a Schedule resource based on the Garage Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Schedule::class, groups={"read:Schedule:item"})))
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
     *              @OA\Property(property="message", example="No Schedule was found"),
     *          )
     *      ),
     * )
     * @Security(name=null)
     * 
     * @param ScheduleRepository $scheduleRepository
     * @return JsonResponse
     */
    public function browse(Request $request, ScheduleRepository $scheduleRepository, GarageRepository $garageRepository): JsonResponse
    {
        // Parameters into query
        if ($request->query->all() !== []) {

            // Parameter "user"
            if (array_key_exists('garage', $request->query->all())) {

                // Find User by Id via Query Parameter
                $garage = $garageRepository->find($request->query->all()["garage"]);

                // Find all Schedule by Garage ID
                $result = $scheduleRepository->findSchedulesByGarageId($request->query->all()['garage']);

                if ($result !== []) {
                    // Check permission for browse the Schedules
                    if ($this->isGranted("garage_browse", $garage)) {
                        return $this->json($result, Response::HTTP_OK, [], ["groups" => ["read:Schedule:item"]]);
                    } else {
                        return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
                    }
                } else {
                    return $this->json(["code" => 404, "message" => "No Schedule was found"], Response::HTTP_NOT_FOUND);
                }
            }
        }

        // Check permission for browse all Shedules
        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->json($scheduleRepository->findAll(), Response::HTTP_OK, [], ["groups" => ["read:Schedule:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Retrieve a Schedule
     * 
     * @Route("/{id<\d+>}", name="app_api_schedule_read", methods={"GET"})
     * 
     * @OA\Get(
     *      summary="Retrieve a Schedule resource",
     *      description="Retrieve a Schedule resource based on the Schedule Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path", required=true,
     *          description="Schedule resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Schedule")
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
     *              @OA\Property(property="message", example="No Schedule was found"),
     *          )
     *      ),
     * )
     * 
     * @param Schedule|null $schedule
     * @return JsonResponse
     */
    public function read(Schedule $schedule = null): JsonResponse
    {
        if ($schedule === null) { return $this->json(["code" => 404, "message" => "No Schedule was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for read a Schedule
        if ($this->isGranted("vehicle_read", $schedule)) {
            return $this->json($schedule, Response::HTTP_OK, [], ["groups" => ["read:Schedule:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Update a Schedule
     * 
     * @Route("/edit/{id<\d+>}", name="app_api_schedule_edit", methods={"PATCH"})
     * 
     * @OA\Patch(
     *      summary="Update a Schedule resource",
     *      description="Update a Schedule resource based on the Schedule Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Schedule resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="day",
     *                      type="string",
     *                      example="lundi"
     *                  ),
     *                  @OA\Property(
     *                      property="startTime",
     *                      type="string",
     *                      example="07:00"
     *                  ),
     *                  @OA\Property(
     *                      property="endTime",
     *                      type="string",
     *                      example="12:00"
     *                  ),
     *                  @OA\Property(
     *                      property="garage",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Schedule")
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
     *              @OA\Property(property="message", example="No Schedule was found"),
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
     * @param Schedule|null $schedule
     * @param Request $request
     * @param SerializerInterface $serializerInterface
     * @param ValidatorInterface $validatorInterface
     * @param ScheduleRepository $scheduleRepository
     * @return JsonResponse
     */
    public function edit(Schedule $schedule = null, Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, ScheduleRepository $scheduleRepository): JsonResponse
    {
        if ($schedule === null) { return $this->json(["code" => 404, "message" => "No Schedule was found"], Response::HTTP_NOT_FOUND); }

        // Get Request Body
        $json = $request->getContent();

        // Check permission for update a Schedule
        if ($this->isGranted("schedule_edit", $schedule)) {

            // Deserialzation with entity Schedule and object Schedule in context, check and insert new modification
            $serializerInterface->deserialize($json, Schedule::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $schedule]);

            $schedule->setUpdatedAt(new \DateTimeImmutable());

            // Save Schedule into database
            $scheduleRepository->add($schedule, true);

            return $this->json($schedule, Response::HTTP_ACCEPTED, [], ["groups" => ["read:Schedule:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Create a Schedule
     * 
     * @Route("/add", name="app_api_schedule_add", methods={"POST"})
     * 
     * @OA\Post(
     *      summary="Create a Schedule resource",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="day",
     *                      type="string",
     *                      example="lundi"
     *                  ),
     *                  @OA\Property(
     *                      property="startTime",
     *                      type="string",
     *                      example="07:00"
     *                  ),
     *                  @OA\Property(
     *                      property="endTime",
     *                      type="string",
     *                      example="12:00"
     *                  ),
     *                  @OA\Property(
     *                      property="garage",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Schedule")
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad request",
     *          @OA\JsonContent(type="string",
     *          example="Invalid JSON")
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
     *          description="Not Convertible",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="422"),
     *              @OA\Property(property="message", example="Field name errors"),
     *          )
     *      ),
     * )
     * 
     * @param Request $request
     * @param SerializerInterface $serializerInterface
     * @param ValidatorInterface $validatorInterface
     * @param ScheduleRepository $scheduleRepository
     * @return JsonResponse
     */
    public function add(Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, ScheduleRepository $scheduleRepository): JsonResponse
    {
        // Get Request Body
        $json = $request->getContent();

        if ($json === "") { return $this->json(["code" => 400, "message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);}

        // Deserialization with entity Schedule, insert field
        $schedule = $serializerInterface->deserialize($json, Schedule::class, 'json');

        // Check permission for add new Schedule
        if ($this->isGranted("schedule_add", $schedule)) {

            // Check constraints validation into Schedule entity
            $errors = $validatorInterface->validate($schedule);

            if (count($errors) > 0) {

                // Formatting errors messages via ApiError model
                $apiError  = new ApiError($errors);

                return $this->json(["code" => 422, $apiError->getAllMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Save Schedule into database
            $scheduleRepository->add($schedule, true);

            return $this->json($schedule, Response::HTTP_CREATED, [], ["groups" => ["read:Schedule:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove a Schedule
     * 
     * @Route("/delete/{id<\d+>}", name="app_api_schedule_delete", methods={"DELETE"})
     * 
     * @OA\Delete(
     *      summary="Remove a Schedule resource",
     *      description="Remove a Schedule resource based on the Schedule Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path", required=true,
     *          description="Schedule resource Identifier",
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
     *              @OA\Property(property="message", example="No Schedule was found"),
     *          )
     *      ),
     * )
     * 
     * @param Schedule $schedule
     * @param ScheduleRepository $scheduleRepository
     * @return JsonResponse
     */
    public function delete(Schedule $schedule, ScheduleRepository $scheduleRepository): JsonResponse
    {
        if ($schedule === null) { return $this->json(["code" => 404, "message" => "No Schedule was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for delete a Schedule
        if ($this->isGranted("schedule_delete", $schedule)) {
            // Remove Schedule into database
            $scheduleRepository->remove($schedule, true);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
