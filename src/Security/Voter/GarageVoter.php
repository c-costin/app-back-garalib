<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Garage;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class GarageVoter extends Voter
{
    private $security;

    public const GARAGE_BROWSE = 'garage_browse';
    public const GARAGE_EDIT = 'garage_edit';
    public const GARAGE_ADD = 'garage_add';
    public const GARAGE_DELETE = 'garage_delete';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::GARAGE_BROWSE, self::GARAGE_EDIT, self::GARAGE_ADD, self::GARAGE_DELETE]) && $subject instanceof \App\Entity\Garage;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        // Get User via TokenInterface
        $user = $token->getUser();

        // Check if the User is anonymous
        if (!$user instanceof UserInterface) return false;

        // Allow for Administrators
        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        // Do not allow if is no have owner
        if ($subject->getUsers()[0] === null) return false;

        switch ($attribute) {

            // Checking permission if browse this Garage
            case self::GARAGE_BROWSE:
                return $this->canBrowse($subject, $user);
                break;

            // Checking permission if update this Garage
            case self::GARAGE_EDIT:
                return $this->canEdit($subject, $user);
                break;
            
            // Checking permission if create new Garage
            case self::GARAGE_ADD:
                return $this->canAdd($subject, $user);
                break;

            // Checking permission if delete this Garages
            case self::GARAGE_DELETE:
                return $this->canDelete($subject, $user);
                break;
        }

        return false;
    }

    /**
     * Check for permission browse Users
     *
     * @param Garage $subject
     * @param User $user
     * @return boolean
     */
    private function canBrowse(Garage $subject, User $user)
    {
        return $subject === $user->getGarage();
    }

    /**
     * Check for permission update a Garage
     *
     * @param Garage $subject
     * @param User $user
     * @return boolean
     */
    private function canEdit(Garage $subject, User $user)
    {
        return $subject->getUsers()[0]  === $user;
    }

    /**
     * Check for permission create new Garage
     *
     * @param Garage $subject
     * @param User $user
     * @return boolean
     */
    private function canAdd(Garage $subject, User $user)
    {
        return $subject->getUsers()[0]  === $user;
    }

    /**
     * Check for permission delete a Garage
     *
     * @param Garage $subject
     * @param User $user
     * @return boolean
     */
    private function canDelete(Garage $subject, User $user)
    {
        return $subject->getUsers()[0]  === $user;
    }
}
