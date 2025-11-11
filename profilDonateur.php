<?php
session_start();
if (!isset($_SESSION['pseudo']) || $_SESSION['type'] !== 'donateur') {
    header("Location: login.html");
    exit;
}

$pseudo = $_SESSION['pseudo'];
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_dons";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Récupération de l'ID si non présent en session
    if (!isset($_SESSION['id_donateur'])) {
        $stmt = $conn->prepare("SELECT id_donateur FROM donateur WHERE pseudo = :pseudo");
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $_SESSION['id_donateur'] = $result['id_donateur'];
        } else {
            header("Location: login.html");
            exit;
        }
    }
    // Récupération des infos complètes du donateur
    $id_donateur = $_SESSION['id_donateur'];
    $stmt2 = $conn->prepare("SELECT nom, prenom, CIN, email, pseudo FROM donateur WHERE id_donateur = :id");
    $stmt2->bindParam(':id', $id_donateur, PDO::PARAM_INT);
    $stmt2->execute();
    $req = $stmt2->fetch(PDO::FETCH_ASSOC);
    if (!$req) {
        echo "Erreur : Donateur introuvable.";
        exit;
    }

} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Donateur</title>
    <style>
        body {
            background-color: #F8F9FA;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            height: 55px;
            background-color: #003C37;
            color: #F8CFCF;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            color: #F8CFCF;
            font-size: 20px;
            text-decoration: none;
            margin-left: 20px;
        }
        a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 2rem;
            color: #004A42;
            text-align: center;
        }

        h3 {
            text-align: center;
            color: #003C37;
            margin-bottom: 20px;
        }

        .infos {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 30px;
            align-items: flex-start;
        }

        .infos1 {
            flex: 1;
        }

        .list-group-item {
            font-size: 1rem;
            margin-bottom: 10px;
            line-height: 1.6;
            border-bottom: 1px solid #eee;
        }

        .list-group-item strong {
            color: #004A42;
        }

        .logo-container {
            flex: 1;
            text-align: center;
        }

        .logo-container img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: none;
            padding: 0;
            background: none;
        }

        .d-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding-top: 12px;
            font-size: 1rem;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
            text-align: center;
            display: inline-block;
            padding-bottom: 12px;
        }

        .btn-outline-primary {
            background-color: #004A42;
            color: white;
            border: none;
        }

        .btn-outline-success {
            background-color: #F8CFCF;
            color: #003C37;
            border: none;
        }

        .btn-outline-secondary,.btn-outline-info {
            background-color: #F8CFCF;
            color: #003C37;
            border: none;
        }

        .btn-danger {
            background-color: #003C37;
            color: white;
            border: none;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Bienvenue, <?php echo htmlspecialchars($pseudo); ?>  !</h2>

    <h3>Vos informations :</h3>
    <div class="infos">
        <div class="infos1">
            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item"><strong>Nom :</strong> <?php echo $req['nom']; ?></li>
                <li class="list-group-item"><strong>Prénom :</strong> <?php echo $req['prenom']; ?></li>
                <li class="list-group-item"><strong>CIN :</strong> <?php echo $req['CIN']; ?></li>
                <li class="list-group-item"><strong>Email :</strong> <?php echo $req['email']; ?></li>
                <li class="list-group-item"><strong>Pseudo :</strong> <?php echo $req['pseudo']; ?></li>
            </ul>
        </div>
    </div>

    <div class="d-grid gap-2 col-md-8 mx-auto mt-4">
        <a href="liste_projetsD.php" class="btn btn-outline-success">Voir les projets disponibles</a>
        <a href="modifier_profil_donateur.php" class="btn btn-outline-primary">Modifier mon profil</a>
        <a href="mes_participations.php" class="btn btn-outline-info">Voir mes participations</a>
        <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
    </div>
</div>

</body>
</html>
