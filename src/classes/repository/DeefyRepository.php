<?php
declare(strict_types=1);

namespace iutnc\deefy\repository;

use PDO;
use Exception;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

class DeefyRepository {
    private PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf) {
        $this->pdo = new PDO(
            $conf['dsn'],
            $conf['user'],
            $conf['pass'],
            [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]
        );
        $this->pdo->prepare("SET NAMES 'UTF8'")->execute();
    }

    public static function setConfig(string $file): void {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new Exception("Erreur lors de la lecture du fichier de configuration BD.");
        }
        $dsn = "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']}";
        self::$config = [
            'dsn'  => $dsn,
            'user' => $conf['username'],
            'pass' => $conf['password'],
        ];
    }

    public static function getInstance(): DeefyRepository {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository(self::$config);
        }
        return self::$instance;
    }

    public function findAllPlaylists(): array {
        $stmt = $this->pdo->prepare("SELECT id, nom FROM playlist");
        $stmt->execute();

        $playlists = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pl = new Playlist($row['nom']);
            $pl->setId((int)$row['id']);
            $playlists[] = $pl;
        }
        return $playlists;
    }

    public function findPlaylistById(int $id): ?Playlist {
        $stmtPl = $this->pdo->prepare("SELECT id, nom FROM playlist WHERE id = :id");
        $stmtPl->execute(['id' => $id]);
        $rowPl = $stmtPl->fetch(PDO::FETCH_ASSOC);

        if (!$rowPl) {
            return null;
        }

        $playlist = new Playlist($rowPl['nom']);
        $playlist->setId((int)$rowPl['id']);

        $queryTracks = "SELECT t.* 
                        FROM track t
                        INNER JOIN playlist2track p2t ON t.id = p2t.id_track
                        WHERE p2t.id_pl = :id_pl
                        ORDER BY p2t.no_piste_dans_liste ASC";
        $stmtTracks = $this->pdo->prepare($queryTracks);
        $stmtTracks->execute(['id_pl' => $id]);

        while ($row = $stmtTracks->fetch(PDO::FETCH_ASSOC)) {
            if ($row['type'] === 'A') {
                $track = new AlbumTrack(
                    $row['titre'],
                    $row['filename'] ?? '',
                    $row['titre_album'] ?? '',
                    (int)($row['numero_album'] ?? 1)
                );
                if (!empty($row['artiste_album'])) {
                    $track->setArtiste($row['artiste_album']);
                }
            } else {
                $track = new PodcastTrack(
                    $row['titre'],
                    $row['filename'] ?? ''
                );
                if (!empty($row['auteur_podcast'])) {
                    $track->setAuteur($row['auteur_podcast']);
                }
                if (!empty($row['date_posdcast'])) {
                    $track->setDate($row['date_posdcast']);
                }
            }

            $track->setId((int)$row['id']);
            if (!empty($row['genre'])) {
                $track->setGenre($row['genre']);
            }
            if (!empty($row['duree']) && (int)$row['duree'] > 0) {
                $track->setDuree((int)$row['duree']);
            }

            $playlist->ajouterPiste($track);
        }

        return $playlist;
    }

    public function saveEmptyPlaylist(Playlist $pl): Playlist {
        $stmt = $this->pdo->prepare("INSERT INTO playlist (nom) VALUES (:nom)");
        $stmt->execute(['nom' => $pl->nom]);
        $pl->setId((int)$this->pdo->lastInsertId());
        return $pl;
    }

    public function savePodcastTrack(PodcastTrack $track): PodcastTrack {
        $query = "INSERT INTO track (titre, genre, duree, filename, type, auteur_podcast, date_posdcast)
                  VALUES (:titre, :genre, :duree, :filename, 'P', :auteur, :date_podcast)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'titre'        => $track->titre,
            'genre'        => $track->genre,
            'duree'        => $track->duree,
            'filename'     => $track->fichierAudio,
            'auteur'       => $track->auteur,
            'date_podcast' => $track->date,
        ]);
        $track->setId((int)$this->pdo->lastInsertId());
        return $track;
    }

    public function addTrackToPlaylist(int $idPlaylist, int $idTrack): void {
        $stmtMax = $this->pdo->prepare("SELECT COALESCE(MAX(no_piste_dans_liste), 0) + 1 FROM playlist2track WHERE id_pl = :id_pl");
        $stmtMax->execute(['id_pl' => $idPlaylist]);
        $nextNum = (int)$stmtMax->fetchColumn();

        $stmt = $this->pdo->prepare("INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (:id_pl, :id_track, :no_piste)");
        $stmt->execute([
            'id_pl'    => $idPlaylist,
            'id_track' => $idTrack,
            'no_piste' => $nextNum,
        ]);
    }
}