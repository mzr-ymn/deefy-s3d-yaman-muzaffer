<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action {
    public function execute(): string {
        $playlist = null;

        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = (int)$_GET['id'];
            $repo = DeefyRepository::getInstance();
            $playlist = $repo->findPlaylistById($id);

            if (is_null($playlist)) {
                return "<p>Erreur : la playlist demandée (ID {$id}) n'existe pas.</p>";
            }

            $_SESSION['playlist'] = $playlist;
        } elseif (isset($_SESSION['playlist']) && $_SESSION['playlist'] instanceof Playlist) {
            $playlist = $_SESSION['playlist'];
        } else {
            return "<p>Aucune playlist sélectionnée. <a href='?action=default'>Choisir une playlist</a> ou <a href='?action=add-playlist'>en créer une</a>.</p>";
        }

        $renderer = new AudioListRenderer($playlist);
        $html = $renderer->render();
        $html .= "<p><a href='?action=add-track'>+ Ajouter une piste à cette playlist</a></p>";
        return $html;
    }
}