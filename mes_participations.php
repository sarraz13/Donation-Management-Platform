<?php
session_start();
if (!isset($_SESSION['pseudo']) || $_SESSION['type'] !== 'donateur') {
    header("Location: login.html");
    exit;
}

$pseudo = $_SESSION['pseudo'];

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_dons";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer l'ID du donateur
$sql_donateur = "SELECT id_donateur FROM donateur WHERE pseudo = :pseudo";
$stmt_donateur = $pdo->prepare($sql_donateur);
$stmt_donateur->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
$stmt_donateur->execute();
$donateur_info = $stmt_donateur->fetch(PDO::FETCH_ASSOC);

if ($donateur_info && isset($donateur_info['id_donateur'])) {
    $id_donateur = $donateur_info['id_donateur'];

    // Récupérer les participations du donateur
    $sql_participations = "SELECT dp.montant_participation, dp.date_participation, p.titre
                                FROM donateur_projet dp
                                JOIN projet p ON dp.id_projet = p.id_projet
                                WHERE dp.id_donateur = :donateur_id
                                ORDER BY dp.date_participation DESC";
    $stmt_participations = $pdo->prepare($sql_participations);
    $stmt_participations->bindParam(':donateur_id', $id_donateur, PDO::PARAM_INT);
    $stmt_participations->execute();
    $participations = $stmt_participations->fetchAll(PDO::FETCH_ASSOC);

    // Calculer le montant total donné
    $montant_total_donne = 0;
    foreach ($participations as $participation) {
        $montant_total_donne += $participation['montant_participation'];
    }
} else {
    // Si l'ID du donateur n'est pas trouvé (erreur)
    $participations = [];
    $montant_total_donne = 0;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Participations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="accueil.css">
    <style>
        body {
            padding-top: 60px; /* Réduit l'espace pour la navbar fixe */
        }

        .participations-container {
            padding: 30px; /* Augmente un peu le padding interne */
            background-color: #f8f9fa;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 20px auto 50px auto; /* Ajoute une marge au-dessus */
        }

        .participations-title {
            color: #004A42;
            font-size: 2.5rem; /* Augmente la taille du titre */
            margin-bottom: 30px; /* Augmente la marge en dessous du titre */
            font-weight: bold;
            text-align: center;
        }

        .participation-item {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px; /* Augmente le padding interne */
            margin-bottom: 20px; /* Augmente la marge en dessous */
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .participation-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .participation-item h5 {
            color: #003C37;
            margin-bottom: 10px; /* Augmente un peu la marge */
            font-weight: 600;
            font-size: 1.1rem; /* Légèrement plus grand */
        }

        .participation-details {
            font-size: 1rem; /* Légèrement plus grand */
            color: #333;
            line-height: 1.6; /* Améliore la lisibilité */
        }

        .participation-details strong {
            font-weight: bold;
        }

        .total-donne {
            margin-top: 30px; /* Augmente la marge au-dessus */
            padding: 15px;
            background-color:pink;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            color: #004A42;
            font-size: 1.2rem; /* Légèrement plus grand */
        }

        .no-participations {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 40px 0; /* Augmente le padding vertical */
            font-size: 1.1rem; /* Légèrement plus grand */
        }

        .no-participations a {
            font-weight: bold;
            color: #007bff;
            text-decoration: none;
        }

        .no-participations a:hover {
            text-decoration: underline;
        }

        .action-btn {
            margin-top: 30px; /* Augmente la marge au-dessus */
        }

        .action-btn .btn {
            margin: 0 10px;
        }
        .btn-primary{
            background-color: #003C37;
        }
        .btn-primary:hover{
            background-color:rgba(0, 60, 55, 0.61);
        }
    </style>
</head>
<body>


    <div class="container participations-container">
        <h2 class="participations-title">Mes Participations</h2>

        <?php if (!empty($participations)): ?>
            <div class="participation-list">
                <?php foreach ($participations as $participation): ?>
                    <div class="participation-item">
                        <h5><?php echo htmlspecialchars($participation['titre']); ?></h5>
                        <div class="participation-details">
                            <div><strong>Montant :</strong> <?php echo number_format($participation['montant_participation'], 2); ?> €</div>
                            <div><strong>Date :</strong> <?php echo date('d/m/Y à H:i', strtotime($participation['date_participation'])); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="total-donne">
                Montant total donné : <?php echo number_format($montant_total_donne, 2); ?> €
            </div>
        <?php else: ?>
            <div class="no-participations">
                <p>Vous n'avez pas encore participé à des projets.</p>
                <p>Découvrez les <a href="liste_projetsD.php">projets disponibles</a> pour faire votre première contribution.</p>
            </div>
        <?php endif; ?>

        <div class="text-center action-btn">
            <a href="profilDonateur.php" class="btn btn-secondary">Retour au profil</a>
            <a href="liste_projetsD.php" class="btn btn-primary">Découvrir les projets</a>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>