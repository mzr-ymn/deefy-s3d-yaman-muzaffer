<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;

$track1 = new AlbumTrack("Zoo", "zoo.mp3", "Or Noir", 2);
$track1->setArtiste("Kaaris");
$track1->setDuree(289);

$podcast = new PodcastTrack("Planete Rap Kaaris", "skyrock.mp3");
$podcast->setAuteur("Skyrock");
$podcast->setDuree(1800);

$playlist = new Playlist("Ma Playlist");
$playlist->ajouterPiste($track1);
$playlist->ajouterPiste($podcast);

echo "<h1>Affichage via Composer</h1>\n";
$renderer = new AudioListRenderer($playlist);
echo $renderer->render();