<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPodcastTrackAction extends Action {
    public function execute(): string {
        if (!isset($_SESSION['playlist']) || !($_SESSION['playlist'] instanceof Playlist)) {
            return "<p>Vous devez d'abord sélectionner ou <a href='?action=add-playlist'>créer une playlist</a> avant d'ajouter une piste.</p>";
        }

        if ($this->http_method === 'GET') {
            $nomPl = htmlspecialchars($_SESSION['playlist']->nom, ENT_QUOTES, 'UTF-8');
            return <<<HTML
            <h2>Ajouter une piste podcast à « {$nomPl} »</h2>
            <form method="post" action="?action=add-track" enctype="multipart/form-data">
                <p>
                    <label for="titre">Titre de la piste :</label><br>
                    <input type="text" id="titre" name="titre" required>
                </p>
                <p>
                    <label for="auteur">Auteur :</label><br>
                    <input type="text" id="auteur" name="auteur">
                </p>
                <p>
                    <label for="genre">Genre :</label><br>
                    <input type="text" id="genre" name="genre">
                </p>
                <p>
                    <label for="duree">Durée (en secondes) :</label><br>
                    <input type="number" id="duree" name="duree" min="1" value="180" required>
                </p>
                <p>
                    <label for="date">Date :</label><br>
                    <input type="date" id="date" name="date">
                </p>
                <p>
                    <label for="fichier">Fichier audio (.mp3) :</label><br>
                    <input type="file" id="fichier" name="fichier" accept=".mp3,audio/mpeg" required>
                </p>
                <button type="submit">Ajouter la piste</button>
            </form>
            HTML;
        }

        $titre = trim((string)filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_SPECIAL_CHARS));
        $auteur = trim((string)filter_input(INPUT_POST, 'auteur', FILTER_SANITIZE_SPECIAL_CHARS));
        $genre = trim((string)filter_input(INPUT_POST, 'genre', FILTER_SANITIZE_SPECIAL_CHARS));
        $duree = (int)filter_input(INPUT_POST, 'duree', FILTER_VALIDATE_INT);
        $date = trim((string)filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS));

        if ($titre === '' || $duree < 1) {
            return "<p>Erreur : le titre et une durée valide sont obligatoires.</p><p><a href='?action=add-track'>Réessayer</a></p>";
        }

        if (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {
            return "<p>Erreur lors du téléchargement du fichier audio.</p><p><a href='?action=add-track'>Réessayer</a></p>";
        }

        $origName = $_FILES['fichier']['name'];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $mimeType = $_FILES['fichier']['type'];

        if ($ext !== 'mp3' || ($mimeType !== 'audio/mpeg' && $mimeType !== 'audio/mp3')) {
            return "<p>Erreur : seul le format MP3 (.mp3) est accepté.</p><p><a href='?action=add-track'>Réessayer</a></p>";
        }

        $audioDir = __DIR__ . '/../../../audio/';
        if (!is_dir($audioDir)) {
            mkdir($audioDir, 0775, true);
        }

        $randomName = uniqid('track_', true) . '.mp3';
        $destPath = $audioDir . $randomName;

        if (!move_uploaded_file($_FILES['fichier']['tmp_name'], $destPath)) {
            return "<p>Erreur serveur : impossible d'enregistrer le fichier dans le dossier audio/.</p>";
        }

        $track = new PodcastTrack($titre, $randomName);
        if ($auteur !== '') {
            $track->setAuteur($auteur);
        }
        if ($genre !== '') {
            $track->setGenre($genre);
        }
        $track->setDuree($duree);
        if ($date !== '') {
            $track->setDate($date);
        }

        $repo = DeefyRepository::getInstance();
        $track = $repo->savePodcastTrack($track);

        $playlist = $_SESSION['playlist'];
        if (!is_null($playlist->id)) {
            $repo->addTrackToPlaylist((int)$playlist->id, (int)$track->id);
        }
        $playlist->ajouterPiste($track);
        $_SESSION['playlist'] = $playlist;

        $renderer = new AudioListRenderer($playlist);
        $html = "<h2>Piste ajoutée avec succès !</h2>";
        $html .= $renderer->render();
        $html .= "<p><a href='?action=add-track'>Ajouter encore une piste</a></p>";
        return $html;
    }
}