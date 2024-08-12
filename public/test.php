<?php
// Récupérer les variables d'environnement
$organizationId = getenv('SENTINELDB_ORGANIZATION_ID');
$organizationSecret = getenv('SENTINELDB_ORGANIZATION_SECRET');

// Vérifier que les variables ont été récupérées correctement
if ($organizationId === false || $organizationSecret === false) {
    die('Les variables d\'environnement ne sont pas définies.');
}

// Configurer l'URL API et les en-têtes
$apiUrl = "https://api.sentineldb.com/endpoint"; // Remplacez par l'URL correcte
$headers = [
    "Authorization: Bearer $organizationSecret",
    "Content-Type: application/json"
];

// Exemple d'appel API avec cURL
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

// Traitez la réponse
echo $response;
?>