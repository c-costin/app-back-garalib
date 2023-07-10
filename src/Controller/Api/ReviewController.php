<?php

namespace App\Controller\Api;

use App\Entity\Review;
use App\Models\ApiError;
use App\Repository\UserRepository;
use App\Repository\ReviewRepository;
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
 * @Route("/api/review")
 * @OA\Tag(name="Review")
 */
class ReviewController extends AbstractController
{
    /**
     * Retrieve the collection of Review
     *
     * @Route("/", name="app_api_review_browse", methods={"GET"})
     * 
     * @OA\Get(
     *      summary="Retrieve the collection of Review resources",
     *      description="Retrieve the collection of Review or identifie a Review with parameters",
     *      @OA\Parameter(
     *          name="user",
     *          in="query",
     *          description="Retrieve a Review resource based on the User Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Parameter(
     *          name="garage",
     *          in="query",
     *          description="Retrieve a Review resource based on the Garage Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Review::class, groups={"read:Review:item"})))
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
     *              @OA\Property(property="message", example="No Review was found"),
     *          )
     *      ),
     * )
     * 
     * @param Request $request
     * @param ReviewRepository $reviewRepository
     * @return JsonResponse
     */
    public function browse(Request $request, ReviewRepository $reviewRepository): JsonResponse
    {
        // Parameters into query
        if ($request->query->all() !== []) {

            // Parameter "user"
            if (array_key_exists('user', $request->query->all())) {

                // Find all Review by User ID
                $result = $reviewRepository->findReviewByUserId($request->query->all()['user']);

                if ($result !== []) {
                    return $this->json($result, Response::HTTP_OK, [], ["groups" => ["read:Review:item"]]);
                } else {
                    return $this->json(["code" => 404, "message" => "No Review was found"], Response::HTTP_NOT_FOUND);
                }
            }

            // Parameter "garage"
            if (array_key_exists('garage', $request->query->all())) {

                // Find all Review by Garage ID
                $result = $reviewRepository->findReviewByGarageId($request->query->all()['garage']);

                if ($result !== []) {
                    return $this->json($result, Response::HTTP_OK, [], ["groups" => ["read:Review:item"]]);
                } else {
                    return $this->json(["code" => 404, "message" => "No Review was found"], Response::HTTP_NOT_FOUND);
                }
            }
        }
            
        // Check permission for browse all Vehicles
        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->json($reviewRepository->findAll(), Response::HTTP_OK, [], ["groups" => ["read:Review:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Retrieve a Review
     * 
     * @Route("/{id<\d+>}", name="app_api_review_read", methods={"GET"})
     * 
     * @OA\Get(
     *      summary="Retrieve a Review resource",
     *      description="Retrieve a Review resource based on the Review Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Review resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Review")
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
     *              @OA\Property(property="message", example="No Review was found"),
     *          )
     *      ),
     * )
     * 
     * @param Review|null $review
     * @return JsonResponse
     */
    public function read(Review $review = null): JsonResponse
    {
        if ($review === null) { return $this->json(["code" => 404, "message" => "No Review was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for read a Review
        if ($this->isGranted("review_read", $review)) {
            return $this->json($review, Response::HTTP_OK, [], ["groups" => ["read:Review:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Update a Review
     * 
     * @Route("/edit/{id<\d+>}", name="app_api_review_edit", methods={"PATCH"})
     * 
     * @OA\Patch(
     *      summary="Update a Review resource",
     *      description="Update a Review resource based on the Review Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Review resource Identifier",
     *          @OA\Schema(type="interger")
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      example="AutoGarage2000 est vraiment super !"
     *                  ),
     *                  @OA\Property(
     *                      property="text",
     *                      type="string",
     *                      example="Lorem ipsum..."
     *                  ),
     *                  @OA\Property(
     *                      property="rating",
     *                      type="integer",
     *                      example="5.0"
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
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Review")
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
     *          @OA\JsonContent(type="string",
     *          example="No Review was found")
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
     * @param Review|null $review
     * @param ReviewRepository $reviewRepository
     * @return JsonResponse
     */
    public function edit(Review $review = null, Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, ReviewRepository $reviewRepository): JsonResponse
    {
        if ($review === null) { return $this->json(["code" => 404, "message" => "No Review was found"], Response::HTTP_NOT_FOUND); }

        // Get Request Body
        $json = $request->getContent();

        // Check permission for update a Review
        if ($this->isGranted("review_edit", $review)) {

            // Deserialzation with entity Review and object Review in context, check and insert new modification
            $serializerInterface->deserialize($json, Review::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $review]);

            $review->setUpdatedAt(new \DateTimeImmutable());

            // Save Review into database
            $reviewRepository->add($review, true);

            return $this->json($review, Response::HTTP_ACCEPTED, [], ["groups" => ["read:Review:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Create a Review
     *
     * @Route("/add", name="app_api_review_add", methods={"POST"})
     * 
     * @OA\Post(
     *      summary="Create a Review resource",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      example="AutoGarage2000 est vraiment super !"
     *                  ),
     *                  @OA\Property(
     *                      property="text",
     *                      type="string",
     *                      example="Lorem ipsum..."
     *                  ),
     *                  @OA\Property(
     *                      property="rating",
     *                      type="integer",
     *                      example="5.0"
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
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/Review")
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
     * @param ReviewRepository $reviewRepository
     * @return JsonResponse
     */
    public function add(Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, ReviewRepository $reviewRepository): JsonResponse
    {
        // Get Request Body
        $json = $request->getContent();

        if ($json === "") { return $this->json(["code" => 400, "message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);}

        // Deserialization with entity Review, insert field
        $review = $serializerInterface->deserialize($json, Review::class, 'json');

        // Check permission for add new Review
        if ($this->isGranted("review_add", $review)) {

            // Check constraints validation into Review entity
            $errors = $validatorInterface->validate($review);

            if (count($errors) > 0) {

                // Formatting errors messages via ApiError model
                $apiError  = new ApiError($errors);

                return $this->json(["code" => 422, $apiError->getAllMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Save Review into database
            $reviewRepository->add($review, true);

            return $this->json($review, Response::HTTP_CREATED, [], ["groups" => ["read:Review:item"]]);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Remove a Review
     * 
     * @Route("/delete/{id<\d+>}", name="app_api_review_delete", methods={"DELETE"})
     * 
     * @OA\Delete(
     *      summary="Remove a Review resource",
     *      description="Remove a Review resource based on the Review Identifier",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Review resource Identifier",
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
     *              @OA\Property(property="message", example="No Review was found"),
     *          )
     *      ),
     * )
     * 
     * @param Review|null $review
     * @param ReviewRepository $reviewRepository
     * @return JsonResponse
     */
    public function delete(Review $review = null, ReviewRepository $reviewRepository): JsonResponse
    {
        if ($review === null) { return $this->json(["code" => 404, "message" => "No Review was found"], Response::HTTP_NOT_FOUND);}

        // Check permission for delete Review
        if ($this->isGranted("review_delete", $review)) {
            // Remove Review into database
            $reviewRepository->remove($review, true);

            return $this->json(null, Response::HTTP_NO_CONTENT);
        } else {
            return $this->json(["code" => 403, "message" => "Access Denied"], Response::HTTP_FORBIDDEN);
        }
    }
}
