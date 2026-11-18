<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class DefaultAction extends Action {
    public function execute(): string {
        $repo = DeefyRepository::getInstance();
        $playlists = $repo->findAllPlaylists();

        $html = "<h2>Bienvenue sur Deefy !</h2>";
        $html .= "<p>Sélectionnez une playlist existante ou créez-en une nouvelle :</p>";

        if (empty($playlists)) {
            $html .= "<p>Aucune playlist enregistrée en base de données.</p>";
        } else {
            $html .= "<ul>";
            foreach ($playlists as $pl) {
                $nom = htmlspecialchars($pl->nom, ENT_QUOTES, 'UTF-8');
                $id = (int)$pl->id;
                $html .= "<li><a href='?action=playlist&id={$id}'>{$nom}</a></li>";
            }
            $html .= "</ul>";
        }

        return $html;
    }
}