<?php
session_start(); 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_dons";

try {
    // créer une instance de la classe pdo
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // récupérer les infos du formulaire
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $montant = $_POST['montant'];
    $date_limite = $_POST['date_limite'];

    // récupérer l' ID du responsable
    $pseudo = $_SESSION['pseudo'];
    $sth = $pdo->prepare("SELECT id_responsable FROM responsable_association WHERE pseudo = ?");
    $sth->execute([$pseudo]);
    $req = $sth->fetch(PDO::FETCH_ASSOC);

    if ($req) {
        $stmt = $pdo->prepare("INSERT INTO projet (titre, description, date_limite, montant_total_a_collecter, montant_total_collecte, id_responsable_association) 
                               VALUES (?, ?, ?, ?, 0, ?)");
        $stmt->execute([$titre, $description, $date_limite, $montant, $req['id_responsable']]);
    }
    header("Location: liste_projetsA.php");
    exit;

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>