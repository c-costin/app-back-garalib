<?php

namespace App\Security\Voter;

use App\Entity\Type;
use App\Entity\User;
use App\Entity\Garage;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class TypeVoter extends Voter
{
    private $security;

    public const TYPE_EDIT = 'type_edit';
    public const TYPE_ADD = 'type_add';
    public const TYPE_DELETE = 'type_delete';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $type): bool
    {
        return in_array($attribute, [self::TYPE_EDIT, self::TYPE_ADD, self::TYPE_DELETE]) && $type instanceof \App\Entity\Type;
    }

    protected function voteOnAttribute(string $attribute, $type, TokenInterface $token): bool
    {
        // Get User via TokenInterface
        $user = $token->getUser();

        // Check if the User is anonymous
        if (!$user instanceof UserInterface) return false;

        // Allow for Administrators
        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        // Do not allow if is not Garage
        if ($type->getGarage() === null) return false;

        switch ($attribute) {

            // Checking permission if update this Type
            case self::TYPE_EDIT:
                return $this->canEdit($type, $user);
                break;

            // Checking permission if create new Type
            case self::TYPE_ADD:
                return $this->canAdd($type, $user);
                break;

            // Checking permission if delete this Type
            case self::TYPE_DELETE:
                return $this->canDelete($type, $user);
                break;
        }

        return false;
    }

    /**
     * Check for permission update a Type
     *
     * @param Type $type
     * @param User $user
     * @return boolean
     */
    private function canEdit(Type $type, User $user)
    {
        return $type->getGarage() === $user->getGarage();
    }

    /**
     * Check for permission create new Type
     *
     * @param Type $type
     * @param User $user
     * @return boolean
     */
    private function canAdd(Type $type, User $user)
    {
        return $type->getGarage() === $user->getGarage();
    }

    /**
     * Check for permission delete a Type
     *
     * @param Type $type
     * @param User $user
     * @return boolean
     */
    private function canDelete(Type $type, User $user)
    {
        return $type->getGarage() === $user->getGarage();
    }
}