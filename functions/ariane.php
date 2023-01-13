<?php
$ndd = "http://www.fouet.test/";
$niv = $_SERVER['PHP_SELF'];
$explo = explode("/", $niv);
echo "<a href=\"" . $ndd . "\">Accueil</a> &gt; \n";
echo "<a href=\"" . $ndd . $explo[1] . "/\">" . $explo[1] . "</a> &gt; \n";
echo "<a href=\"" . $ndd . $explo[2] . "/\">" . $explo[2] . "</a>\n";


function fildarianise(&$titres, $separateur = ' > ')
{

    /*
	* $titres est un tableau associatif du genre :
        * $titres = array(0=>'Accueil', 'cat1'=>'Catégorie 1', 'cat2'=>'Categorie 2', 'contact.html'=>'Contact', 'index.html'=>false);
        */
    $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
    $retour = "<a href=\" $baseUrl \" > " . $titres[0] . '</a>';

    $chemin = explode(substr($_SERVER['PHP_SELF'], 1), '/');

    if (is_array($chemin)) foreach ($chemin as $k => $v) if ($titres[$v] !== false) {
        $baseUrl .= "/$v";
        $titre = isset($titres[$v]) ? $titres[$v] : $v;
        $retour .= $separateur . "<a href=\" $baseUrl \" > $titre </a>";
    }

    return $retour;
}
