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

    // Récupération des infos actuelles
    $sth = $pdo->prepare("SELECT * FROM responsable_association WHERE pseudo = :pseudo");
    $sth->execute(['pseudo' => $pseudo]);
    $assoc = $sth->fetch(PDO::FETCH_ASSOC);

    if (!$assoc) {
        session_destroy();
        header("Location: login.html");
        exit;
    }

    // Mise à jour si formulaire soumis
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $CIN = $_POST['CIN'];
        $adresse = $_POST['adresse_association'];
        $nom_asso = $_POST['nom_association'];
        $matricule = $_POST['matricule_fiscal'];

        $update = $pdo->prepare("
            UPDATE responsable_association
            SET nom = :nom, prenom = :prenom, email = :email, CIN = :CIN,
                adresse_association = :adresse, nom_association = :nom_asso,
                matricule_fiscal = :matricule
            WHERE pseudo = :pseudo
        ");

        $update->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'CIN' => $CIN,
            'adresse' => $adresse,
            'nom_asso' => $nom_asso,
            'matricule' => $matricule,
            'pseudo' => $pseudo
        ]);

        header("Location: profilAssociation.php");
        exit;
    }

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container {
            max-width: 600px;
            margin-top: 50px;
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 { color: #004A42; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4 text-center">Modifier mon profil</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($assoc['nom']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($assoc['prenom']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="CIN" class="form-label">CIN</label>
            <input type="text" class="form-control" name="CIN" value="<?= htmlspecialchars($assoc['CIN']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($assoc['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="nom_association" class="form-label">Nom de l'association</label>
            <input type="text" class="form-control" name="nom_association" value="<?= htmlspecialchars($assoc['nom_association']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="adresse_association" class="form-label">Adresse de l'association</label>
            <input type="text" class="form-control" name="adresse_association" value="<?= htmlspecialchars($assoc['adresse_association']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="matricule_fiscal" class="form-label">Matricule fiscal</label>
            <input type="text" class="form-control" name="matricule_fiscal" value="<?= htmlspecialchars($assoc['matricule_fiscal']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="pseudo" class="form-label">Pseudo</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($assoc['pseudo']) ?>" disabled>
        </div>
        <button type="submit" class="btn btn-success w-100">Enregistrer les modifications</button>
        <a href="profilAssociation.php" class="btn btn-secondary w-100 mt-2">Annuler</a>
    </form>
</div>

</body>
</html>
