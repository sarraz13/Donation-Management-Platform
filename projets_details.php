<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_dons";
session_start();
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
// Récupérer l'ID du projet depuis l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $project_id = $_GET['id'];
    $sql = "SELECT p.*, r.nom_association FROM projet p JOIN responsable_association r ON p.id_responsable_association = r.id_responsable WHERE p.id_projet = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $project_id, PDO::PARAM_INT);
    $stmt->execute();
    $projet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$projet) {
        // Le projet n'existe pas
        header("Location: liste_projetsD.php"); 
        exit;
    }

    // Traitement du formulaire de participation
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Vérifier si l'utilisateur est connecté comme donateur
        if (!isset($_SESSION['pseudo']) || $_SESSION['type'] !== 'donateur' || !isset($_SESSION['id_donateur'])) {
            // Rediriger vers la page de connexion avec un message
            header("Location: login.html?message=connexion_requise");
            exit;
        }
        
        if (isset($_POST['montant_don']) && is_numeric($_POST['montant_don']) && $_POST['montant_don'] > 0) {
            $montant_don = floatval($_POST['montant_don']);

            // Vérifier si le montant du don est inférieur ou égal au montant restant
            $montant_restant = $projet['montant_total_a_collecter'] - $projet['montant_total_collecte'];
            if ($montant_don <= $montant_restant) {
                // Récupérer l'ID du donateur depuis la session
                $id_donateur = $_SESSION['id_donateur'];

                // Enregistrer la participation dans la table donateur_projet
                $sql_insert = "INSERT INTO donateur_projet (id_projet, id_donateur, montant_participation, date_participation) VALUES (:projet_id, :donateur_id, :montant, NOW())";
                $stmt_insert = $pdo->prepare($sql_insert);
                $stmt_insert->bindParam(':projet_id', $project_id, PDO::PARAM_INT);
                $stmt_insert->bindParam(':donateur_id', $id_donateur, PDO::PARAM_INT);
                $stmt_insert->bindParam(':montant', $montant_don, PDO::PARAM_STR);
                $stmt_insert->execute();

                // Mettre à jour le montant total collecté dans la table projet
                $nouveau_montant_collecte = $projet['montant_total_collecte'] + $montant_don;
                $sql_update = "UPDATE projet SET montant_total_collecte = :nouveau_montant WHERE id_projet = :projet_id";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->bindParam(':nouveau_montant', $nouveau_montant_collecte, PDO::PARAM_STR);
                $stmt_update->bindParam(':projet_id', $project_id, PDO::PARAM_INT);
                $stmt_update->execute();

                // Rediriger pour éviter la soumission multiple du formulaire
                header("Location: projets_details.php?id=" . $project_id . "&don_succes=1");
                exit;
            } else {
                $erreur_don = "Le montant du don dépasse le montant restant à collecter.";
            }
        } else {
            $erreur_don = "Veuillez entrer un montant valide pour le don.";
        }
    }

    // Calculer le montant restant et la progression
    $montant_restant = $projet['montant_total_a_collecter'] - $projet['montant_total_collecte'];
    $pourcentage_collecte = ($projet['montant_total_collecte'] / $projet['montant_total_a_collecter']) * 100;
} else {
    // ID de projet manquant ou invalide
    header("Location: liste_projetsD.php"); // Rediriger vers la liste des projets
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpHub - Détails du Projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="accueil.css">
    <style>
        .project-details-container {
            margin-top: 30px;
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .project-title {
            color: #004A42;
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .project-meta {
            color: #6c757d;
            margin-bottom: 15px;
            font-style: italic;
        }

        .project-description {
            font-size: 1.1rem;
            color: #333;
            line-height: 1.8;
            margin-bottom: 30px;
            white-space: pre-line;
        }

        .progress-container {
            background-color: #e9ecef;
            border-radius: 5px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .progress-bar {
            background-color: #004A42;
            color: white;
            text-align: center;
            height: 20px;
            line-height: 20px;
        }

        .amount-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 1rem;
            color: #333;
        }

        .remaining-amount {
            color: #dc3545;
            font-weight: bold;
        }

        .donation-form {
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-top: 30px;
        }

        .donation-form h4 {
            color: #003C37;
            margin-bottom: 15px;
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }

        .btn-donate {
            background-color: #004A42;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-donate:hover {
            background-color: #003C37;
        }

        .error-message {
            color: #dc3545;
            margin-top: 10px;
        }

        .success-message {
            color: #28a745;
            margin-top: 10px;
        }
    </style>
</head>
<body>


    <div class="container project-details-container mt-5 pt-5">
        <?php if ($projet): ?>
            <h2 class="project-title"><?php echo htmlspecialchars($projet['titre']); ?></h2>
            <p class="project-meta">Organisé par : <?php echo htmlspecialchars($projet['nom_association']); ?></p>
            <p class="project-description"><?php echo htmlspecialchars($projet['description']); ?></p>

            <div class="progress-container">
                <div class="progress-bar" role="progressbar" style="width: <?php echo $pourcentage_collecte; ?>%;" aria-valuenow="<?php echo $pourcentage_collecte; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo round($pourcentage_collecte, 2); ?>%</div>
            </div>
            <div class="amount-info">
                <span>Objectif : <?php echo number_format($projet['montant_total_a_collecter'], 2); ?> €</span>
                <span class="remaining-amount">Reste à collecter : <?php echo number_format($montant_restant, 2); ?> €</span>
            </div>

            <div class="donation-form">
                <h4>Participer à ce projet</h4>
                <?php if (isset($_GET['don_succes']) && $_GET['don_succes'] == 1): ?>
                    <p class="success-message">Votre don a été enregistré avec succès. Merci pour votre soutien !</p>
                <?php endif; ?>
                
                <?php if (isset($erreur_don)): ?>
                    <p class="error-message"><?php echo htmlspecialchars($erreur_don); ?></p>
                <?php endif; ?>
                
                <?php if (!isset($_SESSION['pseudo']) || $_SESSION['type'] !== 'donateur'): ?>
                    <div class="alert alert-warning">
                        Vous devez être connecté en tant que donateur pour faire un don. 
                        <a href="login.html" class="alert-link">Se connecter</a>
                    </div>
                <?php else: ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="montant_don" class="form-label">Montant de votre don (en €) :</label>
                            <input type="number" class="form-control" id="montant_don" name="montant_don" min="0.01" step="0.01" max="<?php echo $montant_restant; ?>" required>
                            <small class="form-text text-muted">Le montant maximum que vous pouvez donner est de <?php echo number_format($montant_restant, 2); ?> €.</small>
                        </div>
                        <button type="submit" class="btn btn-donate">Faire un don</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                Le projet demandé n'a pas été trouvé.
            </div>
            <p><a href="liste_projetsD.php" class="btn btn-secondary">Retour à la liste des projets</a></p>
        <?php endif; ?>
    </div>

    
    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>