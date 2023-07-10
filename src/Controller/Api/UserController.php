<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Models\ApiError;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/user")
 */
class UserController extends AbstractController
{
    /**
     * Send User info connected
     * 
     * @Route("/info", name="app_api_user_info", methods={"GET"})
     *
     * @OA\Tag(name="Authentification")
     * @OA\Get(
     *      summary="Retrieve an User resource",
     *      description="Retrieve an User resource identifies by Token JWT",
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=User::class, groups={"read:User:item"})))
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
     * )
     * 
     * @return JsonResponse
     */
    public function info(): JsonResponse
    {
        // Get User info
        $user = $this->getUser();

        return $this->json($user, Response::HTTP_OK, [], ["groups" => ["read:User:item"]]);
    }

    /**
     * Retrieve the collection of User
     * 
     * @Route("/", name="app_api_user_browse", methods={"GET"})
     * 
     * @OA\Tag(name="User")
     * @OA\Get(
     *      summary="Retrieve the collection of User resources",
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=User::class, groups={"read:User:item"})))
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
     * )
     * 
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function browse(UserRepository $userRepository): JsonResponse
    {
        // Check permission for browse all Vehicles
        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->json($userRepository->findAll(), Response::HTTP_OK, [], ["groups" => ["read:User:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Retrieve an User
     * 
     * @Route("/{id<\d+>}", name="app_api_user_read", methods={"GET"})
     * 
     * @OA\Tag(name="User")
     * @OA\Get(
     *      summary="Retrieve an User resource",
     *      description="Retrieve an User resource based on the User Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="User resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/User")
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
     *              @OA\Property(property="message", example="No User was found"),
     *          )
     *      ),
     * )
     * 
     * @param User|null $user
     * @return JsonResponse
     */
    public function read(User $user = null): JsonResponse
    {
        if ($user === null) { return $this->json(["code" => 404, "message" => "No User was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for read a User
        if ($this->isGranted("user_read", $user)) {
            return $this->json($user, Response::HTTP_OK, [], ["groups" => ["read:User:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Update an User
     * 
     * @Route("/edit/{id<\d+>}", name="app_api_user_edit", methods={"PATCH"})
     * 
     * @OA\Tag(name="User")
     * @OA\Patch(
     *      summary="Update an User resource",
     *      description="Update an User resource based on the User Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="User resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="firstname",
     *                      type="string",
     *                      example="John"
     *                  ),
     *                  @OA\Property(
     *                      property="lastname",
     *                      type="string",
     *                      example="Doe"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      example="john.doe@mail.com"
     *                  ),
     *                  @OA\Property(
     *                      property="roles",
     *                      type="array",
     *                      @OA\Items(example="['ROLE_USER']")
     *                  ),
     *                  @OA\Property(
     *                      property="phone",
     *                      type="string",
     *                      example="0695483215"
     *                  ),
     *                  @OA\Property(
     *                      property="dateOfBirth",
     *                      type="string",
     *                      example="1984-05-04"
     *                  ),
     *                  @OA\Property(
     *                      property="garage",
     *                      type="integer",
     *                      example="1"
     *                  ),
     *                  @OA\Property(
     *                      property="vehicles",
     *                      type="array",
     *                      @OA\Items(example="1")
     *                  ),
     *                  @OA\Property(
     *                      property="appointments",
     *                      type="array",
     *                      @OA\Items(example="1")
     *                  ),
     *                  @OA\Property(
     *                      property="reviews",
     *                      type="array",
     *                      @OA\Items(example="1")
     *                  ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/User")
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
     *              @OA\Property(property="message", example="No User was found"),
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
     * @param User|null $user
     * @param Request $request
     * @param SerializerInterface $serializerInterface
     * @param ValidatorInterface $validatorInterface
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function edit(User $user = null, Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        if ($user === null) { return $this->json(["code" => 404, "message" => "No User was found"], Response::HTTP_NOT_FOUND); }

        // Get Request Body
        $json = $request->getContent();

        // Check permission for update a User
        if ($this->isGranted("user_edit", $user)) {

            // Deserialzation with entity User and object User in context, check and insert new modification
            $userFromJson = $serializerInterface->deserialize($json, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

            // Hash User new password
            if ((substr($user->getPassword(), 0, 4 ) !== "$2y$") || (substr($userFromJson->getPassword(), 0, 4 ) !== "$2y$")) { $passwordHasher->hashPassword($user, $userFromJson->getPassword()); }

            $user->setUpdatedAt(new \DateTimeImmutable());

            // Save User into database
            $userRepository->add($user, true);

            return $this->json($user, Response::HTTP_ACCEPTED, [], ["groups" => ["read:User:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Create an User
     * 
     * @Route("/add", name="app_api_user_add", methods={"POST"})
     * 
     * @OA\Tag(name="User")
     * @OA\Post(
     *      summary="Create an User resource",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="firstname",
     *                      type="string",
     *                      example="John"
     *                  ),
     *                  @OA\Property(
     *                      property="lastname",
     *                      type="string",
     *                      example="Doe"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string",
     *                      example="john.doe@mail.com"
     *                  ),
     *                  @OA\Property(
     *                      property="roles",
     *                      type="array",
     *                      @OA\Items(example="['ROLE_USER']")
     *                  ),
     *                  @OA\Property(
     *                      property="phone",
     *                      type="string",
     *                      example="0695483215"
     *                  ),
     *                  @OA\Property(
     *                      property="dateOfBirth",
     *                      type="string",
     *                      example="1984-05-04"
     *                  ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/User")
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
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @return JsonResponse
     */
    public function add(Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        // Get Request Body
        $json = $request->getContent();

        if ($json === "") { return $this->json(["code" => 400, "message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);}

        // Deserialization with entity User, insert field
        $user = $serializerInterface->deserialize($json, User::class, 'json');

        // Check constraints validation into User entity
        $errors = $validatorInterface->validate($user);

        if (count($errors) > 0) {

            // Formatting errors messages via ApiError model
            $apiError  = new ApiError($errors);

            return $this->json(["code" => 422, $apiError->getAllMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Hash User password
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));

        // Save User into database
        $userRepository->add($user, true);

        return $this->json($user, Response::HTTP_CREATED, [], ["groups" => ["read:User:item"]]);
    }

    /**
     * Remove an User
     * 
     * @Route("/delete/{id<\d+>}", name="app_api_user_delete", methods={"DELETE"})
     * 
     * @OA\Tag(name="User")
     * @OA\Delete(
     *      summary="Remove an User resource",
     *      description="Remove an User resource based on the User Identifier",
     *      @OA\Parameter(
     *          name="id", in="path",
     *          required=true,
     *          description="User resource Identifier",
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
     *              @OA\Property(property="message", example="No User was found"),
     *          )
     *      ),
     * )
     * 
     * @param User|null $user
     * @param UserRepository $userRepository
     * @param AddressRepository $addressRepository
     * @return JsonResponse
     */
    public function delete(User $user = null, UserRepository $userRepository, AddressRepository $addressRepository): JsonResponse
    {
        if ($user === null) { return $this->json(["code" => 404, "message" => "No User was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for delete User
        if ($this->isGranted("user_delete", $user)) {
            // Check if user has an address
            if ($user->getAddress() !== null) {
                // Find User Address
                $userAddress = $addressRepository->find($user->getAddress());
                // Remove User Adress into database
                $addressRepository->remove($userAddress, true);
            }

            // Remove User into database
            $userRepository->remove($user, true);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
