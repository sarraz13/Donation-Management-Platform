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
    $cin = $_POST['cin']; 
    $pseudo = $_POST['pseudo']; 

    // Vérifier s'il existe déjà un pseudo ou un CIN
    $check = $pdo->prepare("SELECT id_responsable FROM responsable_association WHERE CIN = :cin OR pseudo = :pseudo");
    $check->bindParam(":cin", $cin, PDO::PARAM_STR);
    $check->bindParam(":pseudo", $pseudo, PDO::PARAM_STR);
    $check->execute();
    $resultat = $check->fetchAll(PDO::FETCH_ASSOC);
    // Si un résultat est trouvé, l'utilisateur existe déjà
    if (count($resultat) > 0) {
        echo "<script>alert('CIN ou pseudo déjà utilisé !'); window.history.back();</script>";
        exit;
    }

    // Insertion dans la base de données
    $req =$pdo->prepare("INSERT INTO responsable_association 
            (nom, prenom, CIN, email, nom_association, adresse_association, matricule_fiscal, logo, pseudo, pwrd)
            VALUES (?,?,?,?,?,?,?,?,?,?)"); 
    $test=$req->execute(array($_POST["nom"],$_POST['prenom'],$_POST['cin'],$_POST['email'],$_POST['assoc_nom'],$_POST['adresse'],$_POST['identifiant_fiscal'], file_get_contents($_FILES["logo"]["tmp_name"]),$_POST['pseudo'],$_POST['password']));
    

    // Exécution de la requête
    if ($test) {
        echo "<script>alert('Inscription réussie !'); window.location.href='login.html';</script>";
    } else {
        echo "Erreur : " . implode(", ", $req->errorInfo());
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>


