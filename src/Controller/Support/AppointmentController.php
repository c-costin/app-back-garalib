<?php

namespace App\Controller\Support;

use App\Entity\User;
use App\Entity\Appointment;
use App\Repository\AppointmentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/support/appointment")
 */
class AppointmentController extends AbstractController
{
    /**
     * Update an Appointment
     * 
     * @Route("/editer/{id<\d+>}", name="app_support_appointment_edit", methods={"GET", "POST"})
     * 
     * @param Request $request
     * @param AppointmentRepository $appointmentRepository
     * @param Appointment|null $appointment
     * @return Response
     */
    public function edit(Request $request, AppointmentRepository $appointmentRepository, User $user = null): Response
    {
        $appointments = $appointmentRepository->findAppointmentByUserId($user->getId());

        if ($user === null) { throw $this->createNotFoundException("Aucun rendez-vous n'a été trouvé"); }

        // Check form is submited
        if ($request->getMethod() === 'POST') {

            // Find Schedule by ID
            $appointment = $appointmentRepository->find($request->request->get('id'));

            // Update Appointment fields
            if ($appointment->getTitle() !== $request->request->get('title')) { $appointment->setTitle($request->request->get('title')); }
            if ($appointment->getDetails() !== $request->request->get('details')) { $appointment->setDetails($request->request->get('details')); }
            if ( $appointment->getStartDate() !== $request->request->get('startDate')) {  $appointment->setStartDate(new \DateTimeImmutable($request->request->get('startDate'))); }
            if ( $appointment->getEndDate() !== $request->request->get('endDate')) {  $appointment->setEndDate(new \DateTimeImmutable($request->request->get('endDate'))); }

            // Save Appointment into database
            $appointmentRepository->add($appointment, true);

            return $this->redirectToRoute('app_support_user_browse', ["id" => $appointment->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('support/appointment/edit.html.twig', [
            "user" => $user,
            "appointments" => $appointments
        ]);
    }

    /**
     * Remove an Appointment
     * 
     * @Route("/supprimer/{id<\d+>}", name="app_support_appointment_delete", methods={"POST"})
     * 
     * @param Request $request
     * @param Appointment|null $appointment
     * @param AppointmentRepository $appointmentRepository
     * @return Response
     */
    public function delete(Request $request, Appointment $appointment = null, AppointmentRepository $appointmentRepository): Response
    {
        if ($appointment === null) { throw $this->createNotFoundException("Aucun utilisateur n'a été trouvé"); }

        // Check is CSRF Token is valid
        if ($this->isCsrfTokenValid('delete' . $appointment->getId(), $request->request->get('_token'))) {

            // Remove appointment into database
            $appointmentRepository->remove($appointment, true);
        }

        return $this->redirectToRoute('app_support_dashboard_default');
    }
}
