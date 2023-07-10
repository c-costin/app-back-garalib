<?php

namespace App\Controller\Support;

use App\Entity\Garage;
use DateTimeImmutable;
use App\Entity\Schedule;
use App\Repository\ScheduleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/support/schedule")
 */
class ScheduleController extends AbstractController
{
    /**
     * Update an Schedule
     * 
     * @Route("/editer/{id<\d+>}", name="app_support_schedule_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param ScheduleRepository $scheduleRepository
     * @param Garage|null $garage
     * @return Response
     */
    public function edit(Request $request, ScheduleRepository $scheduleRepository, Garage $garage = null): Response
    {
        // Find all Schedule by Garage ID
        $schedules = $scheduleRepository->findSchedulesByGarageId($garage->getId());

        if ($garage === null) { throw $this->createNotFoundException("Aucun horaire n'a été trouvé"); }

        if ($request->getMethod() === 'POST') {

            // Find Schedule by ID
            $schedule = $scheduleRepository->find($request->request->get('id'));

            // Update Schedule fields
            if ($schedule->getDay() !== $request->request->get('day')) { $schedule->setDay($request->request->get('day')); }
            if ($schedule->getStartTime() !== $request->request->get('startTime')) { $schedule->setStartTime($request->request->get('startTime')); }
            if ($schedule->getEndTime() !== $request->request->get('endTime')) { $schedule->setEndTime($request->request->get('endTime')); }

            // Save Schedule into database
            $scheduleRepository->add($schedule, true);

            return $this->redirectToRoute('app_support_garage_browse', ["id" => $schedule->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('support/schedule/edit.html.twig', [
            "schedules" => $schedules,
            "garage" => $garage
        ]);
    }

    /**
     * Remove an Schedule
     * 
     * @Route("/supprimer/{id<\d+>}", name="app_support_schedule_delete", methods={"POST"})
     *
     * @param Request $request
     * @param Schedule $schedule
     * @param ScheduleRepository $scheduleRepository
     * @return Response
     */
    public function delete(Request $request, Schedule $schedule, ScheduleRepository $scheduleRepository): Response
    {
        // Check is CSRF Token is valid
        if ($this->isCsrfTokenValid('delete' . $schedule->getId(), $request->request->get('_token'))) {

            // Remove Schedule into database
            $scheduleRepository->remove($schedule, true);
        }

        return $this->redirectToRoute('app_support_schedule_edit');
    }
}
