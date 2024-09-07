<?php

namespace App\Security;

use App\Entity\Activity;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ActivityVoter extends Voter
{
    const EDIT = 'edit';
    const WITHDRAW = 'withdraw';
    const REGISTER = 'register';

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::EDIT, self::REGISTER, self::WITHDRAW])) {
            return false;
        }

        // only vote on `Activity` objects
        if (!$subject instanceof Activity) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User){
            return false;
        }

        /**
         * @Var Activity $activity
         */
        $activity = $subject;

        return match ($attribute) {
            self::EDIT => $this->canEdit($activity, $user),
            self::WITHDRAW => $this->canWithdraw($activity, $user),
            self::REGISTER => $this->canRegister($activity, $user),
            default => false,
        };
    }


    /**
     * Vérifie si l'utilisateur peut éditer l'activité (update et cancel)
     *
     * @param Activity $activity
     * @param User $user
     * @return bool
     */
    private function canEdit(Activity $activity, User $user): bool
    {
        if ($activity->getOrganizer() === $user && $activity->getState()->getCode() == "ACT_CR"){
            return true;
        }

        return false;
    }

    /**
     * Vériie si l'utilisateur peut se désinscrire de l'activité
     *
     * @param Activity $activity
     * @param User $user
     * @return bool
     */
    private function canWithdraw(Activity $activity, User $user): bool
    {
        if ($user->getActivities()->contains($activity) &&
            ($activity->getState()->getCode() == "ACT_INS" || $activity->getState()->getCode() == "ACT_INS_F")
        ){
            return true;
        }

        return false;
    }


    /**
     * Vérifie si l'utilisateur peut s'inscrire à l'activité
     *
     * @param Activity $activity
     * @param User $user
     * @return bool
     */
    private function canRegister(Activity $activity, User $user): bool
    {
        if (!$user->getActivities()->contains($activity) && // L'utilisateur n'est pas déjà dans l'activité
            $activity->getState()->getCode() == "ACT_INS" && // L'activité est ouverte aux inscriptions
            $activity->getRegistered()->count() < $activity->getNbLimitParticipants() // Il reste des places
        ){
            return true;
        }

        return false;
    }
}