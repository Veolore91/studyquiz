<?php
include "db_connect.php";

// Lösung anzeigen (ja/nein)
//$zeige_loesung = "ja";

if (isset($kurs)) {
 $rnd = 'da4b9237bacccdf19c0760cab7aec4a8359010b0';
 if (!isset($_POST["save"])) {

  // Anzahl der Fragen ermitteln
   $select = $db->query("SELECT `kursfs`
                         FROM `Fragen`
					     WHERE `kursfs` = '" . $kurs . "'
						 LIMIT " . $fragenlimit . "");
  $fragen = count($select->fetchAll(PDO::FETCH_OBJ));

  // Anzahl der Frage erhöhen
  $frage = 0;
  if (isset($_POST["frage"])) {
   $frage = $_POST["frage"] += 1;
  }

  // Quizfrage auslesen
  if ($frage <= $fragen) {
   $select = $db->query("SELECT `fragenid`, `frage`, `antwort1`, `antwort2`, `antwort3`, `antwort4`, `richtigeantwort`, `kursfs`
                                            FROM `Fragen`
                                            WHERE `kursfs` = '" . $kurs . "'
                                            LIMIT " . $frage . ",1");
   $eintrag = $select->fetch();

   // Punkte vergeben
   $punkte = isset($_POST["punkte"]) ? substr($_POST["punkte"], strlen($rnd)) : 0;
   if (isset($_POST["aw"])) {
    if (sha1($_POST["aw"]) === $_POST["richtigeantwort"]) {
     $punkte++;

     // Lösung bei einer richtigen Antwort ausgeben
     if ($zeige_loesung == "ja") {
      echo '<p><span style="color: #3DCE00; font-weight: bold;">&#10004;</span> Die Antwort ist richtig.</p>';
     }
    }
    else {

     // Lösung bei einer falschen Antwort ausgeben
     if ($zeige_loesung == "ja") {
      $loesung = $db->query("SELECT `fragenid`, `frage`, `antwort1`, `antwort2`, `antwort3`, `antwort4`, `richtigeantwort`
                                               FROM `Fragen`
                                               WHERE `fragenid` = '" . $_POST["fragenid"] . "'");
      $ergebnis = $loesung->fetch();
      echo '<p><span style="color: #FF0000; font-weight: bold;">&#10008;</span> Die Antwort ist leider falsch,<br>' .
      'richtig wäre &bdquo;<i>' . $ergebnis["antwort" . $ergebnis["richtigeantwort"]] . '</i>&rdquo; gewesen.</p>';
     }
    }
   }

   // Quizfrage stellen
   if ($eintrag["frage"] != "" &&
       !isset($_POST["ende"])) {
    echo '<form action="" method="post">' .
     '<p class="zaehler">Frage ' . ($frage + 1) . ' von ' . $fragen . '</p>' .
     '<div class="frage">' . nl2br($eintrag["frage"]) . '</div>' .
     '<p class="antwort"><label><input type="radio" name="aw" value="1" required="required"> ' . $eintrag["antwort1"] . '</label></p>' .
     '<p class="antwort"><label><input type="radio" name="aw" value="2"> ' . $eintrag["antwort2"] . '</label></p>' .
     '<p class="antwort"><label><input type="radio" name="aw" value="3"> ' . $eintrag["antwort3"] . '</label></p>' .
     '<p class="antwort"><label><input type="radio" name="aw" value="4"> ' . $eintrag["antwort4"] . '</label></p>' .
     '<input type="hidden" name="punkte" value="' . $rnd . $punkte . '">' .
     '<input type="hidden" name="richtigeantwort" value="' . sha1($eintrag["richtigeantwort"]) . '">' .
     '<input type="hidden" name="frage" value="' . $frage . '">' .
     '<input type="hidden" name="fragen" value="' . $fragen . '">' .
     '<input type="hidden" name="fragenid" value="' . $eintrag["fragenid"] . '">' .
     '<p><input type="submit" value="Weiter"></p>' .
    '</form>';
   }
   else {
    echo '<form action="" method="post">' .
     '<input type="hidden" name="punkte" value="' . $rnd . $punkte . '">' .
     '<input type="hidden" name="frage" value="' . $frage . '">' .
     '<input type="hidden" name="fragen" value="' . $fragen . '">' .
     '<p><input type="submit" name="ende" value="Weiter zur Auswertung"></p>' .
     '</form>';
   }
  }
  if (isset($_POST["ende"])) {

   // Auswertung
   $punkte = substr($_POST["punkte"], strlen($rnd));
   echo '<p>Sie haben <b>' . $punkte . '</b> '.
    ($punkte == 1 ? 'Frage' : 'Fragen') .' von <b>' .
    $_POST["fragen"] . '</b> richtig beantwortet.</p>';
  }
 }

 // Quiz neu starten
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
   echo '<p>&raquo; <a href="' . basename($_SERVER["SCRIPT_NAME"]) . '">Quiz neu starten</a></p>';
 }
}
else {
 exit;
}
?>