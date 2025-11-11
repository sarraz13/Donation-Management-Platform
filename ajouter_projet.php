<?php
session_start();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #fff);
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 20px;
        }
        .card-header {
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            background-color:#003C37;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-success {
            background-color:#003C37;
            border: none;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .btn-success:hover {
            opacity: 0.85;
            background-color:#003C37;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card mx-auto shadow-lg" style="max-width: 700px;">
        <div class="card-header text-white text-center py-4">
            <h3 class="mb-0">Ajouter un nouveau projet</h3>
        </div>
        <div class="card-body p-4">
            <form action="traiter_ajout_projet.php" method="post">
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre du projet</label>
                    <input type="text" class="form-control" id="titre" name="titre" placeholder="Ex: Aide aux orphelins" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description détaillée</label>
                    <textarea class="form-control" id="description" name="description" rows="5" placeholder="Décrivez le but, les bénéficiaires, etc." required></textarea>
                </div>

                <div class="mb-3">
                    <label for="montant" class="form-label">Montant à collecter (en Dinars)</label>
                    <input type="number" step="0.01" class="form-control" id="montant" name="montant" placeholder="Ex: 10000" required>
                </div>

                <div class="mb-3">
                    <label for="date_limite" class="form-label">Date limite</label>
                    <input type="date" class="form-control" id="date_limite" name="date_limite" required>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success w-100">Ajouter le projet</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
