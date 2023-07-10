<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Review;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class ReviewVoter extends Voter
{
    private $security;

    public const REVIEW_ADD = 'review_add';
    public const REVIEW_EDIT = 'review_edit';
    public const REVIEW_DELETE = 'review_delete';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::REVIEW_ADD, self::REVIEW_EDIT, self::REVIEW_DELETE]) && $subject instanceof \App\Entity\Review;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Check if the User is anonymous
        if (!$user instanceof UserInterface) return false;

        // Allow for Administrators
        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        // Do not allow if is not owner
        if ($subject->getUser() === null) return false;

        switch ($attribute) {

            // Checking permission if create new Review
            case self::REVIEW_ADD:
                return $this->canAdd($subject, $user);
                break;

            // Checking permission if read this Review
            case self::REVIEW_EDIT:
                return $this->canEdit($subject, $user);
                break;

            // Checking permission if delete this Review
            case self::REVIEW_DELETE:
                return $this->canDelete($subject, $user);
                break;
        }

        return false;
    }

    /**
     * Check for permission create new Review
     *
     * @param Review $subject
     * @param User $user
     * @return boolean
     */
    private function canAdd(Review $subject, User $user)
    {
        return $subject->getUser() === $user;
    }

    /**
     * Check for permission update a Type
     *
     * @param Review $subject
     * @param User $user
     * @return boolean
     */
    private function canEdit(Review $subject, User $user)
    {
        return $subject->getUser() === $user;
    }

    /**
     * Check for permission delete a Review
     *
     * @param Review $subject
     * @param User $user
     * @return boolean
     */
    private function canDelete(Review $subject, User $user)
    {
        return $subject->getUser() === $user;
    }
}
