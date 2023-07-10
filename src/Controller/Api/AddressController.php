<?php

namespace App\Controller\Api;

use App\Entity\Address;
use App\Models\ApiError;
use App\Repository\AddressRepository;
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
 * @Route("/api/address")
 * @OA\Tag(name="Address")
*/
class AddressController extends AbstractController
{
    /**
     * Retrieve the collection of Addresse
     * 
     * @Route("/", name="app_api_address_browse", methods={"GET"})
     * 
     * @OA\Get(
     *      summary="Retrieve the collection of Address resources",
     *      description="Retrieve the collection of Address or identifie an Address with parameters",
     *      @OA\Parameter(
     *          name="user",
     *          in="query",
     *          description="Retrieve an Address resource based on the User Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Parameter(
     *          name="garage",
     *          in="query",
     *          description="Retrieve an Address resource based on the Garage Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Address::class, groups={"read:Address:item"})))
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
     *              @OA\Property(property="message", example="No Address was found"),
     *          )
     *      ),
     * )
     * 
     * @param Request $request
     * @param AddressRepository $addressRepository
     * @return JsonResponse
     */
    public function browse(Request $request, AddressRepository $addressRepository, UserRepository $userRepository): JsonResponse
    {
        // Parameters into query
        if ($request->query->all() !== []) {

            // Parameter "user"
            if (array_key_exists("user", $request->query->all())) {

                // Find User Address by User Id via Query Parameter
                $user = $userRepository->find($request->query->all()["user"]);

                // Find all Address by User ID
                $result = $addressRepository->findAddressByUserId($request->query->all()["user"]);

                if ($result !== []) {
                    // Check permission for find an Address
                    if ($this->isGranted("user_read", $user)) {
                        return $this->json($result, Response::HTTP_OK, [], ["groups" => ["read:Address:item"]]);
                    } else {
                        return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
                    }
                } else {
                    return $this->json(["code" => 404, "message" => "No Address was found"], Response::HTTP_NOT_FOUND);
                }
            }

            // Parameter "garage"
            if (array_key_exists("garage", $request->query->all())) {

                // Find all Address by Garage ID
                $result = $addressRepository->findAddressByGarageId($request->query->all()["garage"]);

                if ($result !== []) {
                    return $this->json($result, Response::HTTP_OK, [], ["groups" => ["read:Address:item"]]);
                } else {
                    return $this->json(["code" => 404, "message" => "No Address was found"], Response::HTTP_NOT_FOUND);
                }
            }
        }

        // Check permission for browse all Address
        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->json($addressRepository->findAll(), Response::HTTP_OK, [], ["groups" => ["read:Address:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Retrieve an Address
     * 
     * @Route("/{id<\d+>}", name="app_api_address_read", methods={"GET"})
     * 
     * @OA\Get(
     *      summary="Retrieve an Address resource",
     *      description="Retrieve an Address resource based on the Address Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Address resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200, 
     *          description="Success", 
     *          @OA\JsonContent(ref="#/components/schemas/Address")
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
     *              @OA\Property(property="message", example="No Address was found"),
     *          )
     *      ),
     * )
     * 
     * @param Address|null $address
     * @return JsonResponse
     */
    public function read(Address $address = null): JsonResponse
    {
        if ($address === null) { return $this->json(["code" => 404, "message" => "No Address was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for read a Addres
        if ($this->isGranted("address_read", $address)) {
            return $this->json($address, Response::HTTP_OK, [], ["groups" => ["read:Address:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Update an Address
     * 
     * @Route("/edit/{id<\d+>}", name="app_api_address_edit", methods={"PATCH"})
     * 
     * @OA\Patch(
     *      summary="Update an Address resource",
     *      description="Update an Address resource based on the Address Identifier",
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
     *                      property="number",
     *                      type="string",
     *                      example="17"
     *                  ),
     *                  @OA\Property(
     *                      property="type",
     *                      type="string",
     *                      example="rue"
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="de la paix"
     *                  ),
     *                  @OA\Property(
     *                      property="town",
     *                      type="string",
     *                      example="Paris"
     *                  ),
     *                  @OA\Property(
     *                      property="postalCode",
     *                      type="string",
     *                      example="75000"
     *                  ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200, description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Address")
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
     *              @OA\Property(property="message", example="No Address was found"),
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
     * @param Address|null $address
     * @param Request $request
     * @param SerializerInterface $serializerInterface
     * @param ValidatorInterface $validatorInterface
     * @param AddressRepository $addressRepository
     * @return JsonResponse
     */
    public function edit(Address $address = null, Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, AddressRepository $addressRepository): JsonResponse
    {
        if ($address === null) { return $this->json(["code" => 404, "message" => "No Address was found"], Response::HTTP_NOT_FOUND); }

        // Get Request Body
        $json = $request->getContent();

        // Check permission for update a Address
        if ($this->isGranted("address_edit", $address)) {

            // Deserialzation with entity Address and object Address in context, check and insert new modification
            $serializerInterface->deserialize($json, Address::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $address]);

            $address->setUpdatedAt(new \DateTimeImmutable());

            // Save Address into database
            $addressRepository->add($address, true);

            return $this->json($address, Response::HTTP_ACCEPTED, [], ["groups" => ["read:Address:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Create an Address
     * 
     * @Route("/add", name="app_api_address_add", methods={"POST"})
     * 
     * @OA\Post(
     *      summary="Create an Address resource",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="number",
     *                      type="string",
     *                      example="17"
     *                  ),
     *                  @OA\Property(
     *                      property="type",
     *                      type="string",
     *                      example="rue"
     *                  ),
     *                  @OA\Property(
     *                      property="name",
     *                      type="string",
     *                      example="de la paix"
     *                  ),
     *                  @OA\Property(
     *                      property="town",
     *                      type="string",
     *                      example="Paris"
     *                  ),
     *                  @OA\Property(
     *                      property="postalCode",
     *                      type="string",
     *                      example="75000"
     *                  ),
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Address")
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
     * @param AddressRepository $addressRepository
     * @return JsonResponse
     */
    public function add(Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, AddressRepository $addressRepository): JsonResponse
    {
        // Get Request Body
        $json = $request->getContent();

        if ($json === "") { return $this->json(["code" => 400, "message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);}

        // Deserialzation with entity Address, insert field
        $address = $serializerInterface->deserialize($json, Address::class, 'json');

        // Check permission for add new Address
        if ($this->isGranted("address_add", $address)) {
            // Check constraints validation into Address entity
            $errors = $validatorInterface->validate($address);

            if (count($errors) > 0) {

                // Formatting errors messages via ApiError model
                $apiError  = new ApiError($errors);

                return $this->json($apiError->getAllMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Save Appointment into database
            $addressRepository->add($address, true);

            return $this->json($address, Response::HTTP_CREATED, [], ["groups" => ["read:Address:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove an Address
     * 
     * @Route("/delete/{id<\d+>}", name="app_api_address_delete", methods={"DELETE"})
     * 
     * @OA\Delete(
     *      summary="Remove an Address resource",
     *      description="Remove an Address resource based on the Address Identifier",
     *      @OA\Parameter(
     *          name="id", in="path",
     *          required=true,
     *          description="Address resource Identifier",
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
     *          response=404, description="Not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", example="404"),
     *              @OA\Property(property="message", example="No Address was found"),
     *          )
     *      ),
     * )
     *
     * @param Address $address
     * @param AddressRepository $addressRepository
     * @return JsonResponse
     */
    public function delete(Address $address, AddressRepository $addressRepository): JsonResponse
    {
        if ($address === null) { return $this->json(["code" => 404, "message" => "No Address was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for delete Address
        if ($this->isGranted("address_delete", $address)) {

            // Remove Address into database
            $addressRepository->remove($address, true);

            return $this->json(null, Response::HTTP_NO_CONTENT);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }
}
