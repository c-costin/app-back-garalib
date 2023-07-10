<?php

namespace App\Controller\Support;

use App\Entity\User;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/support/review")
 */
class ReviewController extends AbstractController
{
    /**
     * Update an Review
     * 
     * @Route("/editer/{id<\d+>}", name="app_support_review_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param ReviewRepository $reviewRepository
     * @param Review|null $review
     * @return Response
     */
    public function edit(Request $request, ReviewRepository $reviewRepository, User $user = null): Response
    {
        $reviews = $reviewRepository->findReviewByUserId($user->getId());

        if ($user === null) { throw $this->createNotFoundException("Aucune évaluation n'a été trouvée"); }

        if ($request->getMethod() === 'POST') {

            // Find Schedule by ID
            $review = $reviewRepository->find($request->request->get('id'));

            // Update Review fields
            if ($review->getTitle() !== $request->request->get('title')) { $review->setTitle($request->request->get('title')); }
            if ($review->getText() !== $request->request->get('text')) { $review->setText($request->request->get('text')); }
            if ($review->getRating() !== $request->request->get('rating')) { $review->setRating($request->request->get('rating')); }
            if ($review->getUser() !== $request->request->get('user')) { $review->setUser($request->request->get('user')); }
            if ($review->getGarage() !== $request->request->get('garage')) { $review->setGarage($request->request->get('garage')); }

            // Save Review into database
            $reviewRepository->add($review, true);

            return $this->redirectToRoute('app_support_user_browse', ["id" => $review->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('support/review/edit.html.twig', [
            "user" => $user,
            "reviews" => $reviews
        ]);
    }

    /**
     * Remove an review
     * 
     * @Route("/supprimer/{id<\d+>}", name="app_support_review_delete", methods={"POST"})
     * 
     * @param Request $request
     * @param Review|null $review
     * @param ReviewRepository $reviewRepository
     * @return Response
     */
    public function delete(Request $request, Review $review = null, ReviewRepository $reviewRepository): Response
    {
        if ($review === null) { throw $this->createNotFoundException("Aucune critique n'a été trouvé"); }

        // Check is CSRF Token is valid
        if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {

            // Remove review into database
            $reviewRepository->remove($review, true);
        }

        return $this->redirectToRoute('app_support_dashboard_default');
    }
}
