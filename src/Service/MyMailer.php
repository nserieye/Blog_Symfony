<?php


namespace App\Service;

use App\Entity\Article;
use Swift_Mailer;

class MyMailer
{
    /** @var Swift_Mailer */
    private $mailer;

    /**
     * Mailer constructor.
     * @param $mailer
     */
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEdit(Article $article)
    {
        $message = new \Swift_Message(
            "L'article {$article->getTitle()} a atteint {$article->getNbViews()} vues"
        );

        $message
            ->addTo('nathalene.serieye@gmail.com')
            ->addFrom('nathalene.serieye@gmail.com');

        $this->mailer->send($message);
    }
}