<?php 
session_start();

if (!isset($_GET['id'])) {
    header("Location: liste_projets.php");
    exit;
}

$projet_id = $_GET['id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestion_dons";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sth = $pdo->prepare("SELECT COUNT(*) AS nb FROM donateur_projet WHERE id_projet = ?");
    $sth->execute([$projet_id]);
    $result = $sth->fetch(PDO::FETCH_ASSOC);  
    $nb_dons = $result['nb'];                

    if ($nb_dons == 0) {
        $req = $pdo->prepare("DELETE FROM projet WHERE id_projet = ?");
        $req->execute([$projet_id]);  
        $message = "Projet supprimé avec succès!";
    } else {
        $message = "Impossible de supprimer : ce projet a reçu des dons.";
    }

} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur : " . $e->getMessage();
}
echo "<script>
    alert('$message');
    window.location.href = 'liste_projetsA.php';
</script>";
exit;
?>
