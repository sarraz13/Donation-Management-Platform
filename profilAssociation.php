<?php
session_start();

$pseudo = $_SESSION['pseudo'];

// Connexion PDO
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_dons";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérification que l'association existe 
    $sth = $pdo->prepare("SELECT * FROM responsable_association WHERE pseudo = :pseudo");
    $sth->execute(['pseudo' => $pseudo]);
    $req = $sth->fetch(PDO::FETCH_ASSOC);


    if (!$req) {
        session_destroy();
        header("Location: login.html");
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Association</title>
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

        /* Keep your original colors */
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

        .btn-outline-secondary {
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

    <h2>Bienvenue, <?php echo $pseudo; ?> !</h2>

    <h3>Vos informations :</h3>
    <div class="infos">
        <div class="infos1">
            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item"><strong>Nom :</strong> <?php echo$req['nom']; ?></li>
                <li class="list-group-item"><strong>Prénom :</strong> <?php echo $req['prenom']; ?></li>
                <li class="list-group-item"><strong>CIN :</strong> <?php echo $req['CIN']; ?></li>
                <li class="list-group-item"><strong>Email :</strong> <?php echo $req['email']; ?></li>
                <li class="list-group-item"><strong>Nom de l'association :</strong> <?php echo $req['nom_association']; ?></li>
                <li class="list-group-item"><strong>Adresse :</strong> <?php echo $req['adresse_association']; ?></li>
                <li class="list-group-item"><strong>Matricule fiscal :</strong> <?php echo $req['matricule_fiscal']; ?></li>
                <li class="list-group-item"><strong>Pseudo :</strong> <?php echo $req['pseudo']; ?></li>
            </ul>
        </div>
        <div class="logo-container">
        <?php 
            
                echo '<img src="data:image/jpeg;base64,' . base64_encode($req["logo"]) . '" alt="Logo de l\'association" />';
            
            ?>
        </div>
    </div>

    <div class="d-grid gap-2">
        <a href="ajouter_projet.php" class="btn btn-outline-primary"> Ajouter un projet</a>
        <a href="liste_projetsA.php" class="btn btn-outline-success"> Mes projets</a>
        <a href="modifier_profil_association.php" class="btn btn-outline-secondary">Modifier mon profil</a>
        <a href="logout.php" class="btn btn-danger"> Se déconnecter</a>
    </div>
</div>

</body>
</html>
