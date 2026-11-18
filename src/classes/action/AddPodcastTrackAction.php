<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;

class AddPodcastTrackAction extends Action {
    public function execute(): string {
        if ($this->http_method === 'GET') {
            return <<<HTML
            <h2>Ajouter un Podcast à la Playlist</h2>
            <form method="post" action="?action=add-track" enctype="multipart/form-data">
                <div>
                    <label for="titre">Titre du podcast :</label>
                    <input type="text" id="titre" name="titre" required>
                </div>
                <br>
                <div>
                    <label for="auteur">Auteur :</label>
                    <input type="text" id="auteur" name="auteur">
                </div>
                <br>
                <div>
                    <label for="duree">Durée (en secondes) :</label>
                    <input type="number" id="duree" name="duree" min="1" value="60">
                </div>
                <br>
                <div>
                    <label for="userfile">Fichier audio (.mp3) :</label>
                    <input type="file" id="userfile" name="userfile" accept="audio/mp3,audio/mpeg" required>
                </div>
                <br>
                <button type="submit">Ajouter la piste</button>
            </form>
            HTML;
        }

        if (!isset($_SESSION['playlist']) || !($_SESSION['playlist'] instanceof Playlist)) {
            return '<p>Erreur : Aucune playlist active. <a href="?action=add-playlist">Créez d\'abord une playlist</a>.</p>';
        }

        $titre = filter_var($_POST['titre'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $auteur = filter_var($_POST['auteur'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $duree = filter_var($_POST['duree'] ?? 0, FILTER_VALIDATE_INT);
        if ($duree === false || $duree <= 0) {
            $duree = 60;
        }

        if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
            return "<p>Erreur lors de l'envoi du fichier audio.</p>";
        }

        $file = $_FILES['userfile'];
        $originalName = $file['name'];

        if (substr(strtolower($originalName), -4) !== '.mp3' || str_ends_with(strtolower($originalName), '.php')) {
            return "<p>Format de fichier interdit ! Seuls les fichiers .mp3 sont acceptés.</p>";
        }

        $uploadDir = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'audio' . DIRECTORY_SEPARATOR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uniqueFilename = uniqid('track_', true) . '.mp3';
        $destination = $uploadDir . $uniqueFilename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return "<p>Impossible de sauvegarder le fichier sur le serveur.</p>";
        }

        $track = new PodcastTrack($titre, 'audio/' . $uniqueFilename);
        if (!empty($auteur)) {
            $track->setAuteur($auteur);
        }
        $track->setDuree($duree);

        $playlist = $_SESSION['playlist'];
        $playlist->ajouterPiste($track);
        $_SESSION['playlist'] = $playlist;

        $renderer = new AudioListRenderer($playlist);
        $html = $renderer->render();
        $html .= '<p><a href="?action=add-track">Ajouter encore une piste</a></p>';

        return $html;
    }
}