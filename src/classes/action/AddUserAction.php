<?php
declare(strict_types=1);

namespace iutnc\deefy\action;

class AddUserAction extends Action {
    public function execute(): string {
        if ($this->http_method === 'GET') {
            return <<<HTML
            <h2>Inscription Utilisateur</h2>
            <form method="post" action="?action=add-user">
                <div>
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                <br>
                <div>
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <br>
                <div>
                    <label for="age">Âge :</label>
                    <input type="number" id="age" name="age" min="1" max="120" required>
                </div>
                <br>
                <button type="submit">Connexion</button>
            </form>
            HTML;
        }

        $nom = filter_var($_POST['nom'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $age = filter_var($_POST['age'] ?? 0, FILTER_VALIDATE_INT);

        if ($email === false) {
            return "<p>Erreur : format d'adresse email invalide !</p>";
        }

        return "<p>Nom: {$nom}, Email:{$email}, Age:{$age} ans</p>";
    }
}