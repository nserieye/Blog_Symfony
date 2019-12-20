<?php


namespace App\Service;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\Article;

class MyEdit implements EventSubscriber
{
    /** @var MyMailer */
    private $articleMailer;

    /**
     * Edit constructor.
     * @param MyMailer $articleMailer
     */
    public function __construct(MyMailer $articleMailer)
    {
        $this->articleMailer = $articleMailer;
    }

    public function getSubscribedEvents()
    {
        return ['preUpdate'];
    }


    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Article) {
            return;
        }

        if ($entity->getNbViews() % 100 != 0) {
            return;
        }

        $this->articleMailer->sendEdit($entity);
    }
}