<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Address;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class AddressVoter extends Voter
{
    private $security;

    public const ADDRESS_ADD = 'address_add';
    public const ADDRESS_READ = 'address_read';
    public const ADDRESS_EDIT = 'address_edit';
    public const ADDRESS_DELETE = 'address_delete';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::ADDRESS_ADD, self::ADDRESS_READ, self::ADDRESS_EDIT, self::ADDRESS_DELETE]) && $subject instanceof \App\Entity\Address;
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
        //? if ($subject->getUser()[0] === null) return false;

        switch ($attribute) {

            // Checking permission if create this Garage
            case self::ADDRESS_ADD:
                return $this->canAdd($subject, $user);
                break;

            // Checking permission if read this Garage
            case self::ADDRESS_READ:
                return $this->canRead($subject, $user);
                break;

            // Checking permission if update this Garage
            case self::ADDRESS_EDIT:
                return $this->canEdit($subject, $user);
                break;

            // Checking permission if delete this Garage
            case self::ADDRESS_DELETE:
                return $this->canDelete($subject, $user);
                break;
        }

        return false;
    }
    
    /**
     * Check for permission create new Address
     *
     * @param Address $subject
     * @param User $user
     * @return boolean
     */
    private function canAdd(Address $subject, User $user)
    {
        return $subject->getId()  === $user->getAddress()->getId();
    }

    /**
     * Check for permission read an Address
     *
     * @param Adress $subject
     * @param User $user
     * @return boolean
     */
    private function canRead(Address $subject, User $user)
    {
        return $subject->getId()  === $user->getAddress()->getId() || $subject === $user->getGarage()[0]->getAddress();
    }

    /**
     * Check for permission update an Address
     *
     * @param Adress $subject
     * @param User $user
     * @return boolean
     */
    private function canEdit(Address $subject, User $user)
    {
        return $subject->getId()  === $user->getAddress()->getId() || $subject === $user->getGarage()[0]->getAddress();
    }

    /**
     * Check for permission delete an Address
     *
     * @param Adress $subject
     * @param User $user
     * @return boolean
     */
    private function canDelete(Address $subject, User $user)
    {
        return $subject->getId()  === $user->getAddress()->getId() || $subject === $user->getGarage()[0]->getAddress();
    }
}
