<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPlaylistAction extends Action {
    public function execute(): string {
        if ($this->http_method === 'GET') {
            return <<<HTML
            <h2>Créer une nouvelle playlist</h2>
            <form method="post" action="?action=add-playlist">
                <label for="nom">Nom de la playlist :</label>
                <input type="text" id="nom" name="nom" required>
                <button type="submit">Créer</button>
            </form>
            HTML;
        }

        $nom = trim((string)filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS));
        if ($nom === '') {
            return "<p>Erreur : le nom de la playlist ne peut pas être vide.</p><p><a href='?action=add-playlist'>Réessayer</a></p>";
        }

        $playlist = new Playlist($nom);
        $repo = DeefyRepository::getInstance();
        $playlist = $repo->saveEmptyPlaylist($playlist);

        $_SESSION['playlist'] = $playlist;

        $renderer = new AudioListRenderer($playlist);
        $html = "<h2>Playlist créée avec succès !</h2>";
        $html .= $renderer->render();
        $html .= "<p><a href='?action=add-track'>Ajouter une piste à cette playlist</a></p>";
        return $html;
    }
}