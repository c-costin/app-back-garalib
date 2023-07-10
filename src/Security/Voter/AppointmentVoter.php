<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Appointment;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class AppointmentVoter extends Voter
{
    private $security;

    public const APPOINTMENT_READ = 'appointment_read';
    public const APPOINTMENT_EDIT = 'appointment_edit';
    public const APPOINTMENT_ADD = 'appointment_add';
    public const APPOINTMENT_DELETE = 'appointment_delete';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::APPOINTMENT_READ, self::APPOINTMENT_EDIT, self::APPOINTMENT_ADD, self::APPOINTMENT_DELETE]) && $subject instanceof \App\Entity\Appointment;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        // Get User via TokenInterface
        $user = $token->getUser();

        // Check if the User is anonymous
        if (!$user instanceof UserInterface) return false;

        // Allow for Administrators
        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        // Do not allow if is not owner
        if ($subject->getUser() === null) return false;

        switch ($attribute) {

            // Checking permission if read this Appointment
            case self::APPOINTMENT_READ:
                return $this->canRead($subject, $user);
                break;

            // Checking permission if read this Appointment
            case self::APPOINTMENT_EDIT:
                return $this->canEdit($subject, $user);
                break;

            // Checking permission if create new Appointment
            case self::APPOINTMENT_ADD:
                return $this->canAdd($subject, $user);
                break;

            // Checking permission if delete this Appointment
            case self::APPOINTMENT_DELETE:
                return $this->canDelete($subject, $user);
                break;
        }

        return false;
    }

    /**
     * Check for permission read a Appointment
     *
     * @param Appointment $subject
     * @param User $user
     * @return boolean
     */
    private function canRead(Appointment $subject, User $user)
    {
        return $subject->getUser() === $user || $subject->getGarage() === $user->getGarage();
    }

    /**
     * Check for permission update a Appointment
     *
     * @param Appointment $subject
     * @param User $user
     * @return boolean
     */
    private function canEdit(Appointment $subject, User $user)
    {
        return $subject->getUser() === $user || $subject->getGarage() === $user->getGarage();
    }

    /**
     * Check for permission create new Appointment
     *
     * @param Appointment $subject
     * @param User $user
     * @return boolean
     */
    private function canAdd(Appointment $subject, User $user)
    {
        return $subject->getUser() === $user || $subject->getGarage() === $user->getGarage();
    }

    /**
     * Check for permission delete a Appointment
     *
     * @param Appointment $subject
     * @param User $user
     * @return boolean
     */
    private function canDelete(Appointment $subject, User $user)
    {
        return $subject->getUser() === $user || $subject->getGarage() === $user->getGarage();
    }
}
