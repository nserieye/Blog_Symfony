<?php


namespace App\Service;


use App\Entity\Article;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserSave implements EventSubscriber
{
    private $token;
    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
        );
    }
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Article) {
            return;
        }
        if (null === $this->token
            || !is_object($token = $this->token->getToken())
            || !is_object($user = $token->getUser())) {
            return;
        }
        $entity->setUser($user);
    }
}