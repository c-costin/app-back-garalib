<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Vehicle;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class VehicleVoter extends Voter
{
    private $security;

    const VEHICLE_READ = 'vehicle_read';
    const VEHICLE_EDIT = 'vehicle_edit';
    const VEHICLE_ADD = 'vehicle_add';
    const VEHICLE_DELETE = 'vehicle_delete';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::VEHICLE_READ, self::VEHICLE_EDIT, self::VEHICLE_ADD, self::VEHICLE_DELETE]) && $subject instanceof \App\Entity\Vehicle;
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

            // Checking permission if read this Vehicle
            case self::VEHICLE_READ:
                return $this->canRead($subject, $user);
                break;

            // Checking permission if read this Vehicle
            case self::VEHICLE_EDIT:
                return $this->canEdit($subject, $user);
                break;

            // Checking permission if read this Vehicle
            case self::VEHICLE_ADD:
                return $this->canAdd($subject, $user);
                break;

            // Checking permission if delete this Vehicle
            case self::VEHICLE_DELETE:
                return $this->canDelete($subject, $user);
                break;
        }

        return false;
    }

    /**
     * Check for permission read a Vehicle
     * 
     * @param Vehicle $subject
     * @param User $user
     * @return boolean
     */
    private function canRead(Vehicle $subject, User $user)
    {
        return $subject->getUser() === $user;
    }

    /**
     * Check for permission update a Vehicle
     *
     * @param Vehicle $subject
     * @param User $user
     * @return boolean
     */
    private function canEdit(Vehicle $subject, User $user)
    {
        return $subject->getUser() === $user;
    }

    /**
     * Check for permission create new Vehicle
     *
     * @param Vehicle $subject
     * @param User $user
     * @return boolean
     */
    private function canAdd(Vehicle $subject, User $user)
    {
        return $subject->getUser() === $user;
    }

    /**
     * Check for permission delete a Vehicle
     *
     * @param Vehicle $subject
     * @param User $user
     * @return boolean
     */
    private function canDelete(Vehicle $subject, User $user)
    {
        return $subject->getUser() === $user;
    }
}
