<?php

require_once '../src/GraduateThesis.php';

$thesis = GraduateThesis::create(
    "Analyse des systèmes informatiques",
    "Ce mémoire étudie les systèmes informatiques.",
    "https://example.com/thesis/123",
    123
);

echo "Titre : " . $thesis->work_name . "\n";
echo "Contenu : " . $thesis->work_text . "\n";
echo "Lien : " . $thesis->work_link . "\n";
echo "Identifiant : " . $thesis->identification_number . "\n";

?>