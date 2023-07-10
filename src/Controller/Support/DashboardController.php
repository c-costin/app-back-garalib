<?php

namespace App\Controller\Support;

use App\Repository\GarageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /** 
     * Display home
     * 
     * @Route("/", name="app_support_dashboard_default")
     */
    public function default(): Response
    {
        return $this->render('support/dashboard/default.html.twig', [
            "currentPage" => "home"
        ]);
    }

    /**
     * Make a search
     * 
     * @Route("/recherche", name="app_support_dashboard_search", methods={"GET"})
     */
    public function search(Request $request, UserRepository $userRepository, GarageRepository $garageRepository)
    {
        // Get keyword via query parameter
        $keyword = $request->query->get('search');

        // Find User by lastname
        $users = $userRepository->findUserByLastname($keyword);

        // Find Garage by name
        $garages = $garageRepository->findGarageByName($keyword);

        return $this->render('support/dashboard/results.html.twig', [
            'currentPage' => "",
            'users' => $users,
            'garages' => $garages,
            'keyword' => $keyword,
        ]);
    }
}
