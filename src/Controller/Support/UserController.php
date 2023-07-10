<?php

namespace App\Controller\Support;

use App\Entity\User;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

 /**
 * Retrieve the collection of User
 *
 * @Route("/support/utilisateur")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_support_user_browse", methods={"GET"})
     *
     * @param UserRepository $userRepository
     * @return Response
     */
    public function browse(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('support/user/browse.html.twig', [
            "currentPage" => "user",
        ]);
    }

    /**
     * Retrieve an User
     * 
     * @Route("/{id<\d+>}", name="app_support_user_read", methods={"GET"})
     *
     * @param User|null $user
     * @return Response
     */
    public function read(User $user = null): Response
    {
        if ($user === null) { throw $this->createNotFoundException("Aucun utilisateur n'a été trouvé"); }

        return $this->render('support/user/read.html.twig', [
            "currentPage" => "user",
            "user" => $user,
        ]);
    }

    /**
     * Update an User
     *
     * @Route("/editer/{id<\d+>}", name="app_support_user_edit", methods={"GET"})
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param User|null $user
     * @return Response
     */
    public function edit(Request $request, UserRepository $userRepository, User $user = null): Response
    {
        if ($user === null) { throw $this->createNotFoundException("Aucun utilisateur n'a été trouvé"); }

        // Check form is submited
        if ($request->isMethod('POST')) {
            
            // Update User fields
            if ($user->getFirstname() !== $request->request->get('firstname')) { $user->setFirstname($request->request->get('firstname')); }
            if ($user->getLastname() !== $request->request->get('lastname')) { $user->setLastname($request->request->get('lastname')); }
            if ($user->getEmail() !== $request->request->get('email')) { $user->setEmail($request->request->get('email')); }
            if ($user->getRoles() !== $request->request->get('role')) { $user->setRoles(["{$request->request->get('role')}"]); }
            if ($user->getPhone() !== $request->request->get('phone')) { $user->setPhone($request->request->get('phone')); }
            if ($user->getDateOfBirth() !== $request->request->get('dateOfBirth')) { $user->setDateOfBirth(new \DateTime($request->request->get('dateOfBirth'))); }

            // Save User into database
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_support_user_read', ["id" => $user->getId()], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('support/user/edit.html.twig', [
            "currentPage" => "user",
            "user" => $user
        ]);
    }

    /**
     * Add an User
     *
     * @Route("/ajouter}", name="app_support_user_add", methods={"GET"})
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    public function add(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Check form is submited
        if ($request->isMethod('POST')) {

            // Create new User
            $user = new User();

            // Set User fields
            $user->setFirstname($request->request->get('firstname'));
            $user->setLastname($request->request->get('lastname'));
            $user->setEmail($request->request->get('email'));
            $user->setPassword($passwordHasher->hashPassword($request->request->get('password')));
            $user->setRoles(["{$request->request->get('role')}"]);
            $user->setPhone($request->request->get('phone'));
            $user->setDateOfBirth(new \DateTime($request->request->get('dateOfBirth')));

            // Save User into database
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_support_user_read', ["id" => $user->getId()], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('support/user/add.html.twig', [
            "currentPage" => "user",
        ]);
    }

    /**
     * Delete an User
     *
     * @Route("/supprimer/{id<\d+>}", name="app_support_user_delete", methods={"POST"})
     * 
     * @param Request $request
     * @param User|null $user
     * @param UserRepository $userRepository
     * @param AddressRepository $addressRepository
     * @return Response
     */
    public function delete(Request $request, User $user = null, UserRepository $userRepository, AddressRepository $addressRepository): Response
    {
        if ($user === null) { throw $this->createNotFoundException("Aucun utilisateur n'a été trouvé"); }

        // dd($this->isCsrfTokenValid('deleteUser' . $user->getId(), $request->request->get('_token')));
        // Check is CSRF Token is valid
        if ($this->isCsrfTokenValid('deleteUser' . $user->getId(), $request->request->get('_token'))) {

            // Check if user has an address
            if ($user->getAddress() !== null) {
                // Find User Address
                $userAddress = $addressRepository->find($user->getAddress());
                // Remove User Adress into database
                $addressRepository->remove($userAddress, true);
            }

            // Remove User into database
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_support_user_browse');
    }
}
