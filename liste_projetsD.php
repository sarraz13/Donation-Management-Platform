<?php
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

// Récupération des projets valides (date non dépassée et montant pas encore entièrement collecté)
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT p.*, r.nom_association 
        FROM projet p 
        JOIN responsable_association r ON p.id_responsable_association = r.id_responsable
        WHERE p.date_limite >= CURDATE() 
        AND p.montant_total_collecte < p.montant_total_a_collecter";

if (!empty($searchKeyword)) {
    $sql .= " AND (p.titre LIKE :keyword OR p.description LIKE :keyword)";
}
$stmt = $pdo->prepare($sql);

if (!empty($searchKeyword)) {
    $keyword = "%$searchKeyword%";
    $stmt->bindParam(':keyword', $keyword, PDO::PARAM_STR);
}
$stmt->execute();
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpHub - Liste des Projets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="accueil.css">
    <style>
        .search-container {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .project-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            margin-bottom: 25px;
            border: none;
        }
        
        .project-card:hover {
            transform: translateY(-5px);
        }
        
        .project-header {
            background-color: #003C37;
            color: white;
            padding: 15px;
        }
        
        .project-body {
            padding: 20px;
        }
        
        .progress {
            height: 10px;
            margin: 15px 0;
        }
        
        .progress-bar {
            background-color: #004A42;
        }
        
        .remaining-amount {
            color: #004A42;
            font-weight: bold;
        }
        
        .btn-details {
            background-color: #003C37;
            color: white;
            border: none;
        }
        
        .btn-details:hover {
            background-color: #004A42;
        }
    </style>
</head>
<body>
    <div class="container mt-5 pt-4">
        <h1 class="text-center mb-4">Projets Disponibles</h1>
        
        <!-- Formulaire de recherche -->
        <div class="search-container">
            <form method="GET" action="liste_projetsD.php">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="search" placeholder="Rechercher par mot-clé (titre ou description)" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                    <button class="btn btn-primary" type="submit">Rechercher</button>
                    <?php if (!empty($searchKeyword)): ?>
                        <a href="liste_projetsD.php" class="btn btn-outline-secondary">Effacer</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Liste des projets -->
        <div class="row">
            <?php if (count($projects) > 0): ?>
                <?php foreach ($projects as $project): 
                    $progress = ($project['montant_total_collecte'] / $project['montant_total_a_collecter']) * 100;
                    $remaining = $project['montant_total_a_collecter'] - $project['montant_total_collecte'];
                ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card project-card">
                            <div class="project-header">
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($project['titre']); ?></h5>
                                <small class="text-white-50"><?php echo htmlspecialchars($project['nom_association']); ?></small>
                            </div>
                            <div class="project-body">
                                <p class="card-text"><?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?>...</p>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Objectif: <?php echo number_format($project['montant_total_a_collecter'], 2); ?> €</span>
                                    <span class="remaining-amount">Reste: <?php echo number_format($remaining, 2); ?> €</span>
                                </div>
                                
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $progress; ?>%" 
                                         aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Date limite: <?php echo date('d/m/Y', strtotime($project['date_limite'])); ?></small>
                                    <a href="projets_details.php?id=<?php echo $project['id_projet']; ?>" class="btn btn-sm btn-details">Voir détails</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h4 class="text-muted">Aucun projet disponible pour le moment</h4>
                    <?php if (!empty($searchKeyword)): ?>
                        <p>Aucun résultat trouvé pour "<?php echo htmlspecialchars($searchKeyword); ?>"</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
