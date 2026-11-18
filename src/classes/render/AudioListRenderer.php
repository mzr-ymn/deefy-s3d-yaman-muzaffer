<?php
declare(strict_types=1);

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

class AudioListRenderer implements Renderer {
    protected AudioList $audioList;

    public function __construct(AudioList $audioList) {
        $this->audioList = $audioList;
    }

    public function render(int $selector = self::COMPACT): string {
        $html = "<div class=\"audiolist\">\n";
        $html .= "  <h2>{$this->audioList->nom}</h2>\n";
        $html .= "  <ul class=\"track-list\">\n";

        foreach ($this->audioList->pistes as $piste) {
            if ($piste instanceof AlbumTrack) {
                $renderer = new AlbumTrackRenderer($piste);
            } elseif ($piste instanceof PodcastTrack) {
                $renderer = new PodcastRenderer($piste);
            } else {
                continue;
            }
            $html .= "    <li>\n" . $renderer->render(Renderer::COMPACT) . "\n    </li>\n";
        }

        $html .= "  </ul>\n";
        $html .= "  <p>Nombre de pistes : {$this->audioList->nbPistes} | Durée totale : {$this->audioList->dureeTotale} s</p>\n";
        $html .= "</div>";

        return $html;
    }
}