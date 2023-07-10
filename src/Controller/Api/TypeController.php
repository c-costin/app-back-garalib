<?php

namespace App\Controller\Api;

use App\Entity\Type;
use App\Models\ApiError;
use App\Repository\UserRepository;
use App\Repository\TypeRepository;
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
* @Route("/api/type")
* @OA\Tag(name="Type")
 */
class TypeController extends AbstractController
{
    /**
     * Retrieve the collection of Type
     *
     * @Route("/", name="app_api_type_browse", methods={"GET"})
     *
     * @OA\Get(
     *      summary="Retrieve the collection of Type resources",
     *      description="Retrieve the collection of Type or identifie a Type with parameters",
     *      @OA\Parameter(
     *          name="garage",
     *          in="query",
     *          description="Retrieve a Type resource based on the Garage Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Type::class, groups={"read:Type:item"})))
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
     *              @OA\Property(property="message", example="No Type was found"),
     *          )
     *      ),
     * )
     * @Security(name=null)
     *
     * @param Request $request
     * @param TypeRepository $typeRepository
     * @return JsonResponse
     */
    public function browse(Request $request, TypeRepository $typeRepository, UserRepository $userRepository): JsonResponse
    {
        // Parameters into query
        if ($request->query->all() !== []) {

            // Parameter "garage"
            if (array_key_exists('garage', $request->query->all())) {

                // Find User by Id via Query Parameter
                $user = $userRepository->find($request->query->all()["user"]);

                // Find all Types by Garage ID
                $result = $typeRepository->findTypeByGarageId($request->query->all()['garage']);
                
                if ($result !== []) {

                    // Check permission for browse the Types
                    if ($this->isGranted("user_browse", $user)) {
                        return $this->json($result, Response::HTTP_OK, [], ["groups" => ["read:Type:item"]]);   
                    } else {
                        return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
                    }
                    
                } else {
                    return $this->json(["code" => 404, "message" => "No Type was found"], Response::HTTP_NOT_FOUND);
                }
            }
        }

        // Check permission for browse all Types
        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->json($typeRepository->findAll(), Response::HTTP_OK, [], ["groups" => ["read:Type:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
   }

    /**
     * Retrieve a Type
     * 
     * @Route("/{id<\d+>}", name="app_api_type_read", methods={"GET"})
     *
     * @OA\Get(
     *      summary="Retrieve a Type resource",
     *      description="Retrieve a Type resource based on the Type Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Type resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Type")
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
     *              @OA\Property(property="message", example="No Type was found"),
     *          )
     *      ),
     * )
     *
     * @param Type|null $type
     * @return JsonResponse
     */

    public function read(Type $type = null): JsonResponse
    {
        if ($type === null) { return $this->json(["code" => 404, "message" => "No Type was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for read a Type
        if ($this->isGranted("type_read", $type)) {
            return $this->json($type, Response::HTTP_OK, [], ["groups" => ["read:Type:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Update a Type
     * 
     * @Route("/edit/{id<\d+>}", name="app_api_type_edit", methods={"PATCH"})
     *
     * @OA\Patch(
     *      summary="Update a Type resource",
     *      description="Update a Type resource based on the Type Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Type resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="Contrôle Technique"
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string",
     *                      example="Vérification de l'ensemble des normes de sécurité d'un véhicule"
     *                  ),
     *                  @OA\Property(
     *                      property="duration",
     *                      type="integer",
     *                      example="45"
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
     *          @OA\JsonContent(ref="#/components/schemas/Type")
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
     *              @OA\Property(property="message", example="No Type was found"),
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
     * @param Type|null $type
     * @param TypeRepository $typeRepository
     * @return JsonResponse
     */
    public function edit(Type $type = null, Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, TypeRepository $typeRepository): JsonResponse
    {
        if ($type === null) { return $this->json(["code" => 404, "message" => "No Type was found"], Response::HTTP_NOT_FOUND); }

        // Get Request Body
        $json = $request->getContent();

        // Check permission for update a Type
        if ($this->isGranted("type_edit", $type)) {

            // Deserialzation with entity Type and object Type in context, check and insert new modification
            $serializerInterface->deserialize($json, Type::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $type]);

            $type->setUpdatedAt(new \DateTimeImmutable());

            // Save Type into database
            $typeRepository->add($type, true);

            return $this->json($type, Response::HTTP_ACCEPTED, [], ["groups" => ["read:Type:item", "read:Garage:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Create a Type
     * 
     * @Route("/add", name="app_api_type_add", methods={"POST"})
     *
     * @OA\Post(
     *      summary="Create a Type resource",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="Contrôle Technique"
     *                  ),
     *                  @OA\Property(
     *                      property="description",
     *                      type="string",
     *                      example="Vérification de l'ensemble des normes de sécurité d'un véhicule"
     *                  ),
     *                  @OA\Property(
     *                      property="duration",
     *                      type="integer",
     *                      example="45"
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
     *          @OA\JsonContent(ref="#/components/schemas/Type")
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
     * @param TypeRepository $typeRepository
     * @return JsonResponse
     */
    public function add(Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, TypeRepository $typeRepository): JsonResponse
    {
        // Get Request Body
        $json = $request->getContent();

        if ($json === "") { return $this->json(["code" => 400, "message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST); }

        // Deserialzation with entity Type, insert field
        $type = $serializerInterface->deserialize($json, Type::class, 'json');

        // Check permission for add new Type
        if ($this->isGranted("type_add", $type)) {

            // Check constraints validation into Type entity
            $errors = $validatorInterface->validate($type);

            if (count($errors) > 0) {

                // Formatting errors messages via ApiError model
                $apiError  = new ApiError($errors);
            
                return $this->json(["code" => 422, $apiError->getAllMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Save Type into database
            $typeRepository->add($type, true);

            return $this->json($type, Response::HTTP_CREATED, [], ["groups" => ["read:Type:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }          
    }

    /**
     * Remove a Type
     * 
     * @Route("/delete/{id<\d+>}", name="app_api_type_delete", methods={"DELETE"})
     *
     * @OA\Delete(
     *      summary="Remove a Type resource",
     *      description="Remove a Type resource based on the Type Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Type resource Identifier",
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
     *              @OA\Property(property="message", example="No Type was found"),
     *          )
     *      ),
     * )
     *
     * @param Type|null $type
     * @param TypeRepository $typeRepository
     * @return JsonResponse
     */
    public function delete(Type $type = null, TypeRepository $typeRepository): JsonResponse
    {
        if ($type === null) { return $this->json(["code" => 404, "message" => "No Type was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for delete  Type
        if ($this->isGranted("type_delete", $type)) {
            // Remove Type into database
            $typeRepository->remove($type, true);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}