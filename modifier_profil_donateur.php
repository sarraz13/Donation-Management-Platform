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

// Récupérer les informations actuelles du donateur
$sql_select = "SELECT nom, prenom, email, CIN FROM donateur WHERE pseudo = :pseudo";
$stmt_select = $pdo->prepare($sql_select);
$stmt_select->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
$stmt_select->execute();
$donateur = $stmt_select->fetch(PDO::FETCH_ASSOC);

if (!$donateur) {
    // Si le donateur n'est pas trouvé (erreur), rediriger vers le profil
    header("Location: profilDonateur.php");
    exit;
}

$nom_actuel = $donateur['nom'];
$prenom_actuel = $donateur['prenom'];
$email_actuel = $donateur['email'];
$cin_actuel = $donateur['CIN'];

$message_succes = null;
$message_erreur = null;

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nouveau_nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $nouveau_prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
    $nouveau_email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $nouveau_cin = isset($_POST['cin']) ? trim($_POST['cin']) : '';

    // Validation des champs (vous pouvez ajouter des validations plus strictes)
    if (!empty($nouveau_nom) && !empty($nouveau_prenom) && !empty($nouveau_email) && filter_var($nouveau_email, FILTER_VALIDATE_EMAIL) && !empty($nouveau_cin)) {
        // Préparer la requête de mise à jour
        $sql_update = "UPDATE donateur SET nom = :nom, prenom = :prenom, email = :email, CIN = :cin WHERE pseudo = :pseudo";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindParam(':nom', $nouveau_nom, PDO::PARAM_STR);
        $stmt_update->bindParam(':prenom', $nouveau_prenom, PDO::PARAM_STR);
        $stmt_update->bindParam(':email', $nouveau_email, PDO::PARAM_STR);
        $stmt_update->bindParam(':cin', $nouveau_cin, PDO::PARAM_STR);
        $stmt_update->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);

        if ($stmt_update->execute()) {
            $message_succes = "Votre profil a été mis à jour avec succès.";
            // Mettre à jour les variables actuelles pour afficher les nouvelles informations
            $nom_actuel = $nouveau_nom;
            $prenom_actuel = $nouveau_prenom;
            $email_actuel = $nouveau_email;
            $cin_actuel = $nouveau_cin;
        } else {
            $message_erreur = "Une erreur est survenue lors de la mise à jour de votre profil.";
        }
    } else {
        $message_erreur = "Veuillez remplir tous les champs correctement.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Profil Donateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="accueil.css">
    <style>
        body {
            padding-top: 76px; /* Espace pour la navbar fixe */
        }
        
        .profile-container {
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto 40px auto;
        }

        .profile-title {
            color: #004A42;
            font-size: 2rem;
            margin-bottom: 30px;
            font-weight: bold;
            text-align: center;
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }

        .btn-primary {
            background-color: #004A42;
            border-color: #004A42;
        }

        .btn-primary:hover {
            background-color: #003C37;
            border-color: #003C37;
        }

        .alert {
            margin-bottom: 20px;
        }
        
        /* Ajustements responsives */
        @media (max-width: 768px) {
            .profile-container {
                padding: 20px;
                margin: 15px;
                width: auto;
            }
            
            .profile-title {
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 576px) {
            body {
                padding-top: 62px;
            }
            
            .navbar-brand {
                margin: 0 !important;
            }
            
            .profile-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="container profile-container">
        <h2 class="profile-title">Modifier mon profil</h2>

        <?php if ($message_succes): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($message_succes); ?>
            </div>
        <?php endif; ?>

        <?php if ($message_erreur): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($message_erreur); ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom:</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($nom_actuel); ?>" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom:</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom_actuel); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email_actuel); ?>" required>
            </div>
            <div class="mb-3">
                <label for="cin" class="form-label">CIN:</label>
                <input type="text" class="form-control" id="cin" name="cin" value="<?php echo htmlspecialchars($cin_actuel); ?>" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="profilDonateur.php" class="btn btn-secondary">Retour au profil</a>
            </div>
        </form>
    </div>

  

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>