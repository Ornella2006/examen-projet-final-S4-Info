<?php
// Pas de logique PHP ici, car la vérification se fait via AJAX
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin | FinVision</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="login-container bg-white/90 w-full max-w-md">
        <div class="login-content p-10">
            <div class="text-center mb-8">
                <div class="flex justify-center items-center mb-4">
                    <div class="text-4xl font-bold logo mr-2">$</div>
                    <h1 class="text-3xl font-bold text-gray-800">FinVision</h1>
                </div>
                <p class="text-gray-600">Portail d'administration financière</p>
            </div>

            <form id="login-form" class="space-y-6">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Identifiant</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" id="nom" name="nom" required 
                               class="input-field pl-10 w-full px-4 py-3 rounded-lg focus:outline-none"
                               placeholder="Votre identifiant">
                    </div>
                </div>

                <div>
                    <label for="motDePasse" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="motDePasse" name="motDePasse" required 
                               class="input-field pl-10 w-full px-4 py-3 rounded-lg focus:outline-none"
                               placeholder="Votre mot de passe">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">Se souvenir</label>
                    </div>
                    <div class="text-sm">
                        <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Mot de passe oublié?</a>
                    </div>
                </div>

                <button type="submit" class="login-btn w-full py-3 px-4 rounded-lg text-white font-semibold">
                    <i class="fas fa-sign-in-alt mr-2"></i>Se connecter
                </button>
                <p id="error-message" class="error text-red-600 mt-4 text-sm"></p>
            </form>
        </div>
    </div>

    <script>
        const apiBase = "http://localhost/examen-projet-final-S4-Info/ws";

        function ajax(method, url, data, callback, errorCallback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        console.log("Réponse brute du serveur :", xhr.responseText); // Log de la réponse brute
                        try {
                            const response = JSON.parse(xhr.responseText);
                            callback(response);
                        } catch (e) {
                            console.log("Erreur de parsing JSON :", e, xhr.responseText);
                            errorCallback(xhr.status, "Réponse serveur invalide");
                        }
                    } else {
                        errorCallback(xhr.status, xhr.responseText);
                    }
                }
            };
            console.log(`Envoi ${method} ${url} avec données : ${data}`);
            xhr.send(data);
        }

        document.getElementById("login-form").addEventListener("submit", function(event) {
            event.preventDefault();
            console.log("Formulaire soumis");
            const nom = document.getElementById("nom").value.trim();
            const motDePasse = document.getElementById("motDePasse").value;
            const errorMessage = document.getElementById("error-message");

            if (!nom || !motDePasse) {
                errorMessage.textContent = "Veuillez remplir tous les champs.";
                console.log("Validation échouée : champs manquants");
                return;
            }

            const data = `nom=${encodeURIComponent(nom)}&motDePasse=${encodeURIComponent(motDePasse)}`;
            console.log("Envoi des données :", data);
            ajax("POST", "/login", data, (response) => {
                console.log("Réponse reçue :", response);
                if (response.success) {
                    console.log("Connexion réussie, redirection vers template/template.php?page=dashboard");
                    window.location.href = "template/template.php?page=dashboard";
                } else {
                    errorMessage.textContent = response.message || "Erreur lors de la connexion.";
                    console.log("Échec de la connexion :", response.message);
                }
            }, (status, error) => {
                errorMessage.textContent = `Erreur lors de la connexion : ${error}`;
                console.log("Erreur AJAX :", status, error);
            });
        });
    </script>
</body>
</html>