<?php
require 'vendor/autoload.php';

$parsedown = new Parsedown();
$parsedown->setSafeMode(false); // pour éviter le HTML non désiré

// Dossier où se trouvent tes fichiers .md
$docsDir = __DIR__ . '/docs/';

// Récupérer le fichier demandé dans l'URL
$file = isset($_GET['file']) ? basename($_GET['file']) : 'installation';
$filePath = $docsDir . $file . '.md';

if (file_exists($filePath)) {
    $markdownContent = file_get_contents($filePath);
    $htmlContent = $parsedown->text($markdownContent);
} else {
    http_response_code(404);
    $htmlContent = "<h1>404 - Page non trouvée</h1><p>Le fichier <code>$file.md</code> n'existe pas.</p>";
}

// Liste des fichiers disponibles pour affichage dans un menu
$files = array_filter(scandir($docsDir), function ($f) {
    return pathinfo($f, PATHINFO_EXTENSION) === 'md';
});
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Documentation Laravel (Offline)</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; }
        aside { width: 200px; background: #f0f0f0; padding: 20px; }
        main { flex-grow: 1; padding: 20px; }
        a { text-decoration: none; display: block; margin-bottom: 10px; }
        pre { background-color: #eee; padding: 10px; overflow-x: auto; }
        code { background-color: #eee; padding: 2px 4px; }
    </style>
    <link rel="stylesheet" href="./assets/highlight/default.min.css"/>
    <script src="./assets/highlight/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
</head>
<body>
<aside>
    <h3>Sommaire</h3>
    <?php foreach ($files as $mdFile):
        $name = basename($mdFile, '.md');
    ?>
        <a href="?file=<?= urlencode($name) ?>"><?= ucfirst($name) ?></a>
    <?php endforeach; ?>
</aside>
<main>
    <?= $htmlContent ?>
</main>
</body>
</html>
