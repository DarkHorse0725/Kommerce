<?php
namespace inklabs\kommerce\Lib;

use inklabs\kommerce\Entity\User;
use inklabs\kommerce\Entity\UserPasswordException;

class UserPasswordValidator
{
    /**
     * @param User $user
     * @param string $password
     * @throws UserPasswordException
     */
    public function assertPasswordValid(User $user, $password)
    {
        if (strlen($password) < 8) {
            throw new UserPasswordException('Password must be at least 8 characters');
        }

        if ($user->verifyPassword($password)) {
            throw new UserPasswordException('Invalid password');
        }

        $tooSimilarValues = [
            $user->getFullName(),
            $user->getEmail(),
        ];

        foreach ($tooSimilarValues as $text) {
            if ($this->isTooSimilar($password, $text)) {
                throw new UserPasswordException('Password is too similar to your name or email');
            }
        }
    }

    /**
     * @param $password
     * @param $text
     * @return bool
     */
    private function isTooSimilar($password, $text)
    {
        if (stripos($text, $password) !== false) {
            return true;
        }

        return $this->getSimilarity($password, $text) > 60;
    }

    /**
     * @param $password
     * @param $text
     * @return int
     */
    private function getSimilarity($password, $text)
    {
        $percentDifference = 0;
        similar_text(strtolower($password), strtolower($text), $percentDifference);
        return $percentDifference;
    }
}
