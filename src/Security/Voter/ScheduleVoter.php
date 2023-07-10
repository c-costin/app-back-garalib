<?php

namespace App\Security\Voter;

use App\Entity\Schedule;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class ScheduleVoter extends Voter
{
    private $security;

    public const SCHEDULE_EDIT = 'schedule_edit';
    public const SCHEDULE_ADD = 'schedule_add';
    public const SCHEDULE_DELETE = 'schedule_delete';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::SCHEDULE_EDIT, self::SCHEDULE_ADD, self::SCHEDULE_DELETE]) && $subject instanceof \App\Entity\Schedule;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        // Get User via TokenInterface
        $user = $token->getUser();

        // Check if the User is anonymous
        if (!$user instanceof UserInterface) return false;

        // Allow for Administrators
        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        // Do not allow if is not Garage Id
        if ($subject->getGarage() === null) return false;

        switch ($attribute) {

            // Checking permission if update this Schedule
            case self::SCHEDULE_EDIT:
                return $this->canEdit($subject, $user);
                break;

            // Checking permission if create new Schedule
            case self::SCHEDULE_ADD:
                    return $this->canAdd($subject, $user);
                    break;

            // Checking permission if delete this Schedule
            case self::SCHEDULE_DELETE:
                return $this->canDelete($subject, $user);
                break;
        }

        return false;
    }

    /**
     * Check for permission update a Schedule
     *
     * @param Schedule $subject
     * @param User $user
     * @return boolean
     */
    private function canEdit(Schedule $subject, User $user)
    {
        return $subject->getGarage() === $user->getGarage();
    }

    /**
     * Check for permission create new Schedule
     *
     * @param Schedule $subject
     * @param User $user
     * @return boolean
     */
    private function canAdd(Schedule $subject, User $user)
    {
        return $subject->getGarage() === $user->getGarage();
    }

    /**
     * Check for permission delete a Schedule
     *
     * @param Schedule $subject
     * @param User $user
     * @return boolean
     */
    private function canDelete(Schedule $subject, User $user)
    {
        return $subject->getGarage() === $user->getGarage();
    }
}
