<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

class DisplayPlaylistAction extends Action {
    public function execute(): string {
        if (!isset($_SESSION['playlist'])) {
            return "<p>Aucune playlist en cours de session.</p>";
        }
        return "<h2>Votre Playlist en cours :</h2><p>Contenu de la session chargé.</p>";
    }
}