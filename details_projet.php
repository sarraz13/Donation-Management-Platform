<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: liste_projetsA.php");
    exit;
}

$id_projet = $_GET['id'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_dons";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sth = $pdo->prepare("SELECT * FROM projet WHERE id_projet = ?");
    $sth->execute([$id_projet]);
    $req = $sth->fetch(PDO::FETCH_ASSOC);
    if (!$req) {
        header("Location: liste_projetsA.php");
        exit;
    }

    // Donateurs
    $sth1 = $pdo->prepare("
        SELECT d.nom, d.prenom, dp.montant_participation 
        FROM donateur d
        JOIN donateur_projet dp ON d.id_donateur = dp.id_donateur
        WHERE dp.id_projet = ?
    ");
    $sth1->execute([$id_projet]);
    $req1 = $sth1->fetchAll(PDO::FETCH_ASSOC);

    $montant_collecte = $req['montant_total_collecte'];
    $montant_a_collecter = $req['montant_total_a_collecter'];
    $montant_restant = $montant_a_collecter - $montant_collecte;
    if ($montant_restant < 0) {
        $montant_restant = 0;
    }


} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails du projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4"><?= $req['titre'] ?></h2>
    <p><strong>Description :</strong> <?= $req['description'] ?></p>
    <p><strong>Montant collecté :</strong> <?= number_format($montant_collecte, 2) ?> DT</p>
    <p><strong>Montant à collecter :</strong> <?= number_format($montant_a_collecter, 2) ?> DT</p>
    <p><strong>Montant restant :</strong> <?= number_format($montant_restant, 2) ?> DT</p>
    <p><strong>Date limite :</strong> <?= $req['date_limite'] ?></p>

    <hr>
    <h4>Liste des donateurs</h4>
    <?php if (count($req1) > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Montant du don</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($req1 as $don): ?>
                    <tr>
                        <td><?= $don['nom'] ?></td>
                        <td><?= $don['prenom'] ?></td>
                        <td><?= number_format($don['montant_participation'], 2) ?> DT</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun donateur pour ce projet.</p>
    <?php endif; ?>

    <a href="liste_projetsA.php" class="btn btn-secondary mt-3">Retour</a>
</div>
</body>
</html>
