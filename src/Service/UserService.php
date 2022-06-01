<?php

namespace App\Service;

use App\Helper\MockUserHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class UserService
{
    private $em;
    private $params;
    private $env;
    private $requestStack;
    private $security;
    private $authorizationChecker;

    public function __construct(
        EntityManagerInterface $em,
        ContainerBagInterface $params,
        EnvironmentService $env,
        RequestStack $requestStack,
        Security $security,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->em = $em;
        $this->params = $params;
        $this->env = $env;
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getGoogleUser()
    {
        if ($this->canMockLogin()) {
            return $this->requestStack->getSession()->get('mockUser');
        }
        return $this->requestStack->getSession()->get('googleUser');
    }

    public function canMockLogin()
    {
        return $this->env->isLocal() && $this->params->has('local_mock_auth') && $this->params->get('local_mock_auth');
    }

    public function getUserInfo($googleUser)
    {
        if ($this->env->values['isUnitTest']) {
            return [
                'id' => 1,
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getUserId(),
                'timezone' => $googleUser->getTimezone()
            ];
        }

        $attempts = 0;
        $maxAttempts = 3;
        do {
            try {
                $user = $this->em->getRepository(User::class)->findOneBy(['email' => $googleUser->getEmail()]);
                break;
            } catch (\Exception $e) {
                if ($attempts == 2) {
                    sleep(1);
                }
                $attempts++;
            }
        } while ($attempts < $maxAttempts);
        if (empty($user)) {
            $user = new User();
            $user->setEmail($googleUser->getEmail());
            $user->setGoogleId($googleUser->getUserId());
            $this->em->persist($user);
            $this->em->flush();
        }

        if (empty($user)) {
            throw new AuthenticationException('Failed to retrieve user information');
        }
        // Return user info in array format
        $userInfo = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'google_id' => $user->getGoogleId(),
            'timezone' => $user->getTimezone(),
        ];
        return $userInfo;
    }

    public function getUser()
    {
        $token = $this->security->getToken();
        if ($token && is_object($token->getUser())) {
            return $token->getUser();
        }
        return null;
    }

    public function getRoles($roles, $site, $awardee)
    {
        if (!empty($site)) {
            if (($key = array_search('ROLE_AWARDEE', $roles)) !== false) {
                unset($roles[$key]);
            }
            if (($key = array_search('ROLE_AWARDEE_SCRIPPS', $roles)) !== false) {
                unset($roles[$key]);
            }
        }
        if (!empty($awardee)) {
            if (($key = array_search('ROLE_USER', $roles)) !== false) {
                unset($roles[$key]);
            }
            if (isset($awardee->id) && $awardee->id !== User::AWARDEE_SCRIPPS && ($key = array_search('ROLE_AWARDEE_SCRIPPS', $roles)) !== false) {
                unset($roles[$key]);
            }
        }
        return $roles;
    }

    public function updateLastLogin(): void
    {
        $userInfo = $this->getUser();
        if ($userInfo) {
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $userInfo->getEmail()]);
            if ($user) {
                $user->setLastLogin(new \DateTime());
                $this->em->persist($user);
                $this->em->flush();
            }
        }
        $this->requestStack->getSession()->set('isLoginReturn', true);
    }

    public function setMockUser($email): void
    {
        MockUserHelper::switchCurrentUser($email);
        $this->requestStack->getSession()->set('mockUser', MockUserHelper::getCurrentUser());
    }

    /** Is the user's session expired? */
    public function isLoginExpired(): bool
    {
        $time = time();
        // custom "last used" session time updated on keepAliveAction
        $idle = $time - $this->requestStack->getSession()->get('pmiLastUsed', $time);
        $remaining = $this->env->values['sessionTimeOut'] - $idle;
        return $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') && $remaining <= 0;
    }

    public function getUserEntity()
    {
        return $this->em->getRepository(User::class)->find($this->getUser()->getId());
    }

    public function getUserEntityFromEmail($email): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
    }
}
