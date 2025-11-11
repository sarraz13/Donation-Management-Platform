
<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_dons";
try {
    // Connexion à la base via PDO
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Récupération des données
    $pseudo = $_POST['pseudo'];
    $pwrd = $_POST['password'];
    $type = $_POST['userType'];
    if ($type === "donateur") {
        $sth = $pdo->prepare("SELECT id_donateur, pwrd FROM donateur WHERE pseudo = :pseudo");
    } else {
        $sth = $pdo->prepare("SELECT id_responsable, pwrd FROM responsable_association WHERE pseudo = :pseudo");
    }

    $sth->execute(['pseudo' => $pseudo]);
    $resultat = $sth->fetch(PDO::FETCH_ASSOC); //fetch une seule ligne

    if ($resultat) {
        // Comparaison du mot de passe en clair
        if ($pwrd === $resultat['pwrd']) {
            // Connexion réussie
            $_SESSION['pseudo'] = $pseudo;
            $_SESSION['type'] = $type;

            if ($type === "donateur") {
                header("Location: profilDonateur.php");
            } else {
                header("Location: profilAssociation.php");
            }
            exit;
        } else {
            echo "<script>alert('Mot de passe incorrect.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Utilisateur non trouvé.'); window.history.back();</script>";
    }

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
