<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;

class AddPlaylistAction extends Action {
    public function execute(): string {
        if ($this->http_method === 'GET') {
            return <<<HTML
            <h2>Créer une Playlist</h2>
            <form method="post" action="?action=add-playlist">
                <div>
                    <label for="nom">Nom de la playlist :</label>
                    <input type="text" id="nom" name="nom" required placeholder="Ma playlist">
                </div>
                <br>
                <button type="submit">Créer la playlist</button>
            </form>
            HTML;
        }

        $nom = filter_var($_POST['nom'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

        $playlist = new Playlist($nom);
        $_SESSION['playlist'] = $playlist;

        $renderer = new AudioListRenderer($playlist);
        $html = $renderer->render();
        $html .= '<p><a href="?action=add-track">Ajouter une piste</a></p>';

        return $html;
    }
}