<?php
session_start();

$pseudo = $_SESSION['pseudo'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_dons";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sth = $pdo->prepare("
        SELECT * FROM projet
        WHERE id_responsable_association = (
            SELECT id_responsable FROM responsable_association WHERE pseudo = :pseudo
        )
        ORDER BY date_limite ASC
    ");
    $sth->execute(['pseudo' => $pseudo]);
    $req = $sth->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des projets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
        }
        .card-title {
            color: #004A42;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-4">Mes projets</h2>
    <?php if (count($req) > 0): ?>
        <div class="row g-4">
            <?php foreach ($req as $projet): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= $projet['titre'] ?></h5>
                            <p><strong>Objectif :</strong> <?= number_format($projet['montant_total_a_collecter'], 2) ?> DT</p>
                            <a href="details_projet.php?id=<?= $projet['id_projet'] ?>" class="btn btn-info btn-sm">Détails</a>
                            <a href="supprimer_projet.php?id=<?= $projet['id_projet'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">Supprimer</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Aucun projet trouvé.</div>
    <?php endif; ?>
</div>

</body>
</html>
