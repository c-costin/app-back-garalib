<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    private $security;

    public const USER_BROWSE = 'user_browse';
    public const USER_READ = 'user_read';
    public const USER_EDIT = 'user_edit';
    public const USER_ADD = 'user_add';
    public const USER_DELETE = 'user_delete';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::USER_BROWSE, self::USER_READ, self::USER_EDIT, self::USER_ADD, self::USER_DELETE]) && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        // Get User via TokenInterface
        $user = $token->getUser();

        // Check if the User is anonymous
        if (!$user instanceof UserInterface)  return false;

        // Allow for Administrators
        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        // Do not allow if is User has not identifier
        if ($user->getUserIdentifier() === null) return false;

        switch ($attribute) {

            // Checking permission if can browse
            case self::USER_BROWSE:
                return $this->canBrowse($subject, $user);
                break;

            // Checking permission if read this User
            case self::USER_READ:
                return $this->canRead($subject, $user);
                break;

            // Checking permission if update this User
            case self::USER_EDIT:
                return $this->canEdit($subject, $user);
                break;

            // Checking permission if create new User
            case self::USER_ADD:
                return $this->canAdd($subject, $user);
                break;

            // Checking permission if delete this User
            case self::USER_DELETE:
                return $this->canDelete($subject, $user);
                break;
        }

        return false;
    }

    /**
     * Check for permission browse Users
     *
     * @param User $subject
     * @param User $user
     * @return boolean
     */
    private function canBrowse(User $subject, User $user)
    {
        return $subject === $user;
    }

    /**
     * Check for permission read a User
     *
     * @param User $subject
     * @param User $user
     * @return boolean
     */
    private function canRead(User $subject, User $user)
    {
        return $subject === $user;
    }

    /**
     * Check for permission update a User
     *
     * @param User $subject
     * @param User $user
     * @return boolean
     */
    private function canEdit(User $subject, User $user)
    {
        return $subject === $user;
    }

    /**
     * Check for permission create new User
     *
     * @param User $subject
     * @param User $user
     * @return boolean
     */
    private function canAdd(User $subject, User $user)
    {
        return $subject === $user;
    }

    /**
     * Check for permission delete a User
     *
     * @param User $subject
     * @param User $user
     * @return boolean
     */
    private function canDelete(User $subject, User $user)
    {
        return $subject === $user;
    }
}
