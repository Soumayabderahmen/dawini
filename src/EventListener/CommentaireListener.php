<?php
namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\Commentaire;

class CommentaireListener
{
    private $badWords = ['spam'];

    public function prePersist(Commentaire $commentaire, LifecycleEventArgs $event)
    {
        $this->checkMessage($commentaire);
    }

    public function preUpdate(Commentaire $commentaire, LifecycleEventArgs $event)
    {
        $this->checkMessage($commentaire);
    }

    private function checkMessage(Commentaire $commentaire)
    {
        $message = $commentaire->getMessage();

        foreach ($this->badWords as $badWord) {
            if (stripos($message, $badWord) !== false) {
                throw new \Exception('Ce commentaire contient un mot interdit.');
            }
        }
    }
}