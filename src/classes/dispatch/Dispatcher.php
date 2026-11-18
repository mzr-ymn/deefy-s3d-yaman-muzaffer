<?php
declare(strict_types=1);

namespace iutnc\deefy\dispatch;

class Dispatcher {
    private ?string $action = null;
    private array $routes = [];

    public function __construct() {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function addRoute(string $action, string $class): self {
        $this->routes[$action] = $class;
        return $this;
    }

    public function run(): void {
        if (isset($this->routes[$this->action])) {
            $actionClass = $this->routes[$this->action];
            $actionInstance = new $actionClass();
            $contentHtml = $actionInstance->execute();
        } else {
            $contentHtml = "<h2>Erreur 404</h2><p>L'action demandée n'existe pas.</p>";
        }

        $this->renderPage($contentHtml);
    }

    private function renderPage(string $html): void {
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Deefy App</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 30px; }
                header h1 { margin: 0 0 10px 0; color: #222; }
                nav { margin-bottom: 25px; padding-bottom: 12px; border-bottom: 2px solid #ddd; }
                nav a { margin-right: 20px; text-decoration: none; color: #0066cc; font-weight: bold; }
                nav a:hover { text-decoration: underline; }
                main { background: #fdfdfd; padding: 25px; border-radius: 6px; border: 1px solid #eee; }
            </style>
        </head>
        <body>
            <header>
                <h1>Deefy App</h1>
            </header>
            <nav>
                <a href="?action=default">Accueil (Playlists)</a>
                <a href="?action=add-playlist">Créer Playlist</a>
                <a href="?action=playlist">Playlist Courante</a>
                <a href="?action=add-track">Ajouter une Piste</a>
            </nav>
            <main>
                {$html}
            </main>
        </body>
        </html>
        HTML;
    }
}