<?php 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_dons";

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des données du formulaire 
    $nom = $_POST['nom']; 
    $prenom = $_POST['prenom']; 
    $email = $_POST['email']; 
    $cin = $_POST['cin']; 
    $pseudo = $_POST['pseudo']; 
    $pwrd = $_POST['password'];

    // Vérifier s'il existe déjà un pseudo ou un CIN
    $check = $pdo->prepare("SELECT id_donateur FROM donateur WHERE CIN = :cin OR pseudo = :pseudo");
    $check->bindParam(":cin", $cin, PDO::PARAM_STR);
    $check->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
    $check->execute();
    $resultat = $check->fetchAll(PDO::FETCH_ASSOC);

    // Si un résultat est trouvé (CIN ou pseudo déjà utilisé)
    if (count($resultat) > 0) {
        echo "<script>alert('CIN ou pseudo déjà utilisé !'); window.history.back();</script>";
        exit; // Arrêter l'exécution du script si CIN ou pseudo est déjà utilisé
    }

    // Insertion du donateur dans la base de données
    $req = $pdo->prepare("INSERT INTO donateur (nom, prenom, email, CIN, pseudo, pwrd)
                          VALUES (?,?,?,?,?,?)"); //marqueurs interrogatifs
    $test = $req->execute(array($nom, $prenom, $email, $cin, $pseudo, $pwrd));

    // Exécution de la requête d'insertion
    if ($test) {
        // Si l'insertion réussie, afficher le message de succès
        echo "<script>alert('Inscription réussie !'); window.location.href='login.html';</script>";
    } else {
        // Si une erreur survient lors de l'insertion, afficher l'erreur
        echo "Erreur : " . $req->errorInfo()[2];
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
