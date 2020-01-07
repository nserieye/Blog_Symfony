<?php


namespace App\Security;


use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ArticleVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Article && in_array($attribute, ['view', 'edit']);
    }

    protected function voteOnAttribute($attribute, $article, TokenInterface $token)
    {

        if ('view' === $attribute && $article->getPublished()) {
            return true;
        }

        // Seul l'utilisateur qui a créé l'article peut le modifier
        $userId = $token->getUser()->getId();
        $owner = $article->getUser();
        if ('edit' === $attribute && $owner instanceof User && $userId === $owner->getId()) {
            return true;
        }
        return false;
    }
}