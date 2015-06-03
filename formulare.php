<?php
require ("./lib/Debug.php");
Debug::enable(Debug::DETECT, './logs/php_error.log');
//// error_reporting(E_ALL);
error_reporting(E_ALL ^ ~E_STRICT);
//// ^ E_NOTICE ^ E_WARNING
require ("./lib/dibi/dibi.php");
session_start();
session_name("jmeno");
session_name("heslo");
require ("./lib/db.php");
require ("./lib/funkce.php");
require ("./lib/prihlaseni_plugin.php");
require ("./lib/PHPMailer-master/PHPMailerAutoload.php");
?>
<!DOCTYPE html>
<html lang="cs">
    <head>
        <?php
        // jazyk
        if ($_GET[lang] == null) {
            $_GET[lang] = "cs";
        }

        // titulek
        if ($_GET[id] == null || $_GET[id] == "index") {
            $title = "PIJEM, JÍME, HODNOTÍME";
            if ($_GET[lang] == null) {
                $_GET[lang] = "cs";
            }
        } elseif ($_GET[id] == "gurmani") {
            $linksactive[gurmani] = " class=\"active\"";
            if ($_GET[lang] == "cs") {
                $title = "Gurmáni | PIJEM, JÍME, HODNOTÍME";
            }
        } elseif ($_GET[id] == "pravidlahodnoceni") {
            $linksactive[pravidlahodnoceni] = " class=\"active\"";
            if ($_GET[lang] == "cs") {
                $title = "Pravidla hodnocení | PIJEM, JÍME, HODNOTÍME";
            }
        } elseif ($_GET[id] == "kontakt") {
            $linksactive[kontakt] = " class=\"active\"";
            if ($_GET[lang] == "cs") {
                $title = "Kontakt | PIJEM, JÍME, HODNOTÍME";
            }
        }
        ?>

        <title><?php echo $title; ?></title>

        <!-- font -->
        <link href='http://fonts.googleapis.com/css?family=Roboto+Slab' rel='stylesheet' type='text/css'>

        <!-- základ -->
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="author" content="martin prokop" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="keywords" content="Pijem, jíme, hodnotíme, ">
        <meta name="description" content="">
        <link type="" href="html/favicon.png" rel="shortcut icon">
        <link href="./img/favicon.gif" rel="icon" type="image/gif" />
        <meta name="robots" content="index,follow,archive" />
        <meta name="googlebot" content="snippet,archive" />
        <script type="text/javascript" src="./lib/script.js"></script>


        <!-- styl -->
        <?php
        require ("./lib/check_mobile.php");
        if (check_mobile()) {
            echo "<link href=\"./lib/style_mobile.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />";
        } else {
            echo "<link href=\"./lib/style.css\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />";
        }
        ?>

        <!-- validate -->
        <script type="text/javascript" src="./lib/fancybox/lib/jquery-1.10.1.min.js"></script>
        <script src="./lib/validate/PJH/jquery.validate.js"></script>
        <script type="text/javascript" src="./lib/validate/PJH/messages_cs.js"></script>

        <!-- inicializace validace -->

        <script type="text/javascript">
            $(document).ready(function () {
                $("#load").hide();
                $("#formul").validate();
            });
            $(document).ready(function () {
                $("#formul").submit(function ()
                {
                    if ($("#formul").valid()) {
                        $('#submit1').toggle();
                        $("#load").show();
                    }
                });
            });
        </script>


    </head>
    <body style="background: #f0f0f0;">
        <div id="okno_form">
            <?php if (check_mobile()) echo "<h2><a href=\"JavaScript:history.back()\" class=\"button_zpet\">Vrátit se zpět</a></h2>"; ?>

            <?php
            if ($_GET[id] == "registrace") {
                echo "<h1>Registrace</h1>";
                $no_error = true;

                if ($_GET[action] == "send") {
                    $query = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [jmeno] = %s', $_POST[jmeno], "AND stav <> %s", "smazan");
                    $query2 = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [email] = %s', $_POST[email], "AND stav <> %s", "smazan");

                    if ($query->count() > 0) {
                        echo "<div class=\"msg err\"><h2>Gurmán s tímto jménem již existuje!</h2><p>Vyberte jiné jméno.</p></div>";
                        $no_error = false;
                    }

                    if ($query2->count() > 0) {
                        echo "<div class=\"msg err\"><h2>Gurmán s tímto emailem již existuje!</h2><p>Vyberte jiný email.</p></div>";
                        $no_error = false;
                    }

                    if ($_POST[passwordinput] != $_POST[passwordinput2]) {
                        echo "<div class=\"msg err\"><h2>Vámi zadaná hesla se neshodují!</h2><p>Opakujte zadání hesla.</p></div>";
                        $no_error = false;
                    }

                    if ($_FILES['fileToUpload']['name'] != "") {
                        if ($_FILES["fileToUpload"]["size"] > 1000000) {
                            echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - je příliš velká!</h2><p>Zřejmě je příliž veliká - maximální velikost je 1 MB.</p></div>";
                            $no_error = false;
                        } else {
                            $timestamp = time();
                            $nazev_souboru_tmp = $_FILES['fileToUpload']['tmp_name'];
                            $nazev_souboru = $timestamp . "_" . $_FILES['fileToUpload']['name'];
                            $cil = "./temp/" . $timestamp . "_" . $_FILES['fileToUpload']['name'];
                            $imageFileType = pathinfo($cil, PATHINFO_EXTENSION);
                            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "JPEG" && $imageFileType != "gif") {
                                echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - nepovolený formát!</h2><p>Povolené formáty jsou JPG, PNG, JPEG, GIF</p></div>";
                                $no_error = false;
                            } else {
                                move_uploaded_file($nazev_souboru_tmp, $cil) or die("<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii!</h2><p>Chyba je na straně serveru, nahašte jí prosím správci.</p></div>");
                                $upload = "ok";
                            }
                        }
                    }

                    //uložime
                    if ($no_error) {
                        // uživatel
                        $heslo_hash = md5($_POST[passwordinput]);

                        $arr_uzivatel = array('jmeno' => $_POST['jmeno'], 'email' => $_POST['email'], 'pohlavi' => $_POST['radio1'], 'popis' => $_POST['textarea1'], 'date' => time(), 'heslo_hash' => $heslo_hash);
                        dibi::query('INSERT INTO [pjh_uzivatele]', $arr_uzivatel);

                        $query = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [jmeno] = %s', $_POST[jmeno]);
                        while ($row = $query->fetch()) {
                            $id_uzivatel = $row[id];
                        }

                        // foto
                        if ($upload == "ok") {
                            $newname = "./obrazky/" . $nazev_souboru;
                            $oldname = $cil;
                            rename($oldname, $newname);
                            $arr_fotka = array('nazev' => $_POST['jmeno'], 'cil' => $newname, 'typ' => "profilova", 'id_cil' => $id_uzivatel, 'poradi' => 1, 'id_autor' => $id_uzivatel, 'popis' => $_POST['jmeno'], 'date' => time());
                            dibi::query('INSERT INTO [pjh_fotky]', $arr_fotka);
                        } elseif ($_POST[uploaded_picture] != "") {
                            $newname = "./obrazky/" . $_POST[uploaded_picture_nazev];
                            $oldname = $_POST[uploaded_picture];
                            rename($oldname, $newname);
                            $arr_fotka = array('nazev' => $_POST['jmeno'], 'cil' => $newname, 'typ' => "profilova", 'id_cil' => $id_uzivatel, 'poradi' => 1, 'id_autor' => $id_uzivatel, 'popis' => $_POST['jmeno'], 'date' => time());
                            dibi::query('INSERT INTO [pjh_fotky]', $arr_fotka);
                        }
                        //mail
                        $body = "Dobrý den,\n\nděkujeme za Vaši registraci na serveru Pijem, jíme, hodnotíme.\n\nVaše uživatelské jméno: " . $_POST['jmeno'] . "\nVaše heslo je: " . $_POST[passwordinput] . "\n\nTěšíme se, že se s náma poldělité o Vaše kulinářské zážitky.\n\nS pozdravem\nPijem, jíme, hodnotíme.";
                        send_mail_kovar($_POST['email'], "Registrace na serveru pjhvysocina.cz", $body);

                        echo "<div class=\"msg information\"><h2>Jste úspěšně registrován jako '" . $_POST[jmeno] . "'!</h2><p>Na email jsme Vám odeslali potvrzení registrace.<br />Zavřete okno nebo <a href=\"./formulare.php?id=prihlaseni&ad=" . $_POST[jmeno] . "\">se přihlašte</a>.</p></div>";
                    }
                }

                if ($_GET[action] != "send" || $no_error == false) {
                    ?>            
                    <form id="formul" name="formul" method="post" action="./formulare.php?id=registrace&action=send" enctype="multipart/form-data">
                        <dl class="inline">
                            <dt><label for="jmeno">Uživatelské jméno *</label></dt>
                            <dd><input type="text" class="big" id="jmeno" name="jmeno" placeholder="Vaše uživatelské jméno" value="<?php echo $_POST[jmeno]; ?>" required></dd>
                            <dt><label for="email">Email *</label></dt>
                            <dd><input type="text" class="big" id="email" name="email" placeholder="Váš email" value="<?php echo $_POST[email]; ?>" required email="true"></dd>  

                            <?php
                            if ($_POST[radio1] == "Muž") {
                                $active[muz] = "checked";
                            } elseif ($_POST[radio1] == "Žena") {
                                $active[zena] = "checked";
                            }
                            ?>

                            <dt><label for="radio-1">Pohlaví *</label></dt>
                            <dd>
                                <input name="radio1" type="radio" id="radio-1" value="Muž" <?php echo $active[muz]; ?> required><label for="radio-1">Muž</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="radio1" type="radio" id="radio-2" value="Žena" <?php echo $active[zena]; ?> ><label for="radio-2">Žena</label>
                            </dd>                

                            <dt><label for="fileToUpload">Profilové foto gurmána</label></dt>
                            <dd>
                                <?php
                                if ($upload == "ok") {
                                    echo "<img src=\"" . $cil . "\" height=\"100px\"/>
                                      <input type=\"hidden\" name=\"uploaded_picture\" value=\"" . $cil . "\">
                                      <input type=\"hidden\" name=\"uploaded_picture_nazev\" value=\"" . $nazev_souboru . "\">";
                                } elseif ($_POST[uploaded_picture] != "") {
                                    echo "<img src=\"" . $_POST[uploaded_picture] . "\" height=\"100px\"/>
                                      <input type=\"hidden\" name=\"uploaded_picture\" value=\"" . $_POST[uploaded_picture] . "\">
                                      <input type=\"hidden\" name=\"uploaded_picture_nazev\" value=\"" . $_POST[uploaded_picture_nazev] . "\">";
                                } else {
                                    echo "<input type = \"file\" class=\"big\" name=\"fileToUpload\" id=\"fileToUpload\">";
                                }
                                ?>
                            </dd> 

                            <dt><label for="textarea1">Povězte něco o sobě *</label></dt>
                            <dd><textarea id="textarea1" name="textarea1" required maxlength="200" placeholder="Popište se jako gurmán (do 200 znaků)"><?php echo $_POST[textarea1]; ?></textarea></dd>

                            <dt><label for="passwordinput">Heslo *</label></dt>
                            <dd><input type="password" class="big" placeholder="Vaše heslo" id="passwordinput" name="passwordinput" required></dd>

                            <dt><label for="passwordinput2">Zopakujte heslo *</label></dt>
                            <dd><input type="password" class="big" placeholder="Zopakujte Vaše heslo" id="passwordinput2" name="passwordinput2" required></dd>

                            <dt><label for="">(*) označuje povinné položky</label></dt>
                            <dd><input type="submit" class="button" id="submit1" value="Registrovat"></dd>
                        </dl>
                    </form>
                    <?php
                }
            } if ($_GET[id] == "nastaveniuzivatele") {
                ?>            
                <h1>Nastavení uživatele</h1>
                <?php
                $no_error_heslo = true;
                $no_error_foto = true;
                $no_error_popis = true;

                if ($_GET[smazatfoto] == "ano") {
                    $query_maze_foto = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', get_id_uzivatele($_SESSION[jmeno]), 'AND [typ] = %s', 'profilova');
                    $row_maze_foto = $query_maze_foto->fetch();
                    unlink($row_maze_foto[cil]);
                    dibi::query('DELETE FROM [pjh_fotky] WHERE [id] = %i', $row_maze_foto[id]);
                }


                if ($_GET[action] == "send") {
                    if ($_POST[passwordinput] != $_POST[passwordinput2]) {
                        echo "<div class=\"msg err\"><h2>Vámi zadaná nová hesla se neshodují!</h2><p>Opakujte zadání hesla.</p></div>";
                        $no_error_heslo = false;
                    }

                    if ($_FILES['fileToUpload']['name'] != "") {
                        if ($_FILES["fileToUpload"]["size"] > 1000000) {
                            echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - je příliš velká!</h2><p>Zřejmě je příliž veliká - maximální velikost je 1 MB.</p></div>";
                            $no_error_foto = false;
                        } else {
                            $timestamp = time();
                            $nazev_souboru_tmp = $_FILES['fileToUpload']['tmp_name'];
                            $nazev_souboru = $timestamp . "_" . $_FILES['fileToUpload']['name'];
                            $cil = "./temp/" . $timestamp . "_" . $_FILES['fileToUpload']['name'];
                            $imageFileType = pathinfo($cil, PATHINFO_EXTENSION);
                            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                                echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - nepovolený formát!</h2><p>Povolené formáty jsou JPG, PNG, JPEG, GIF</p></div>";
                                $no_error_foto = false;
                            } else {
                                move_uploaded_file($nazev_souboru_tmp, $cil) or die("<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii!</h2><p>Chyba je na straně serveru, nahašte jí prosím správci.</p></div>");
                                $upload = "ok";
                            }
                        }
                    } else {
                        $no_error_foto = false;
                    }

                    $query = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [jmeno] = %s', $_SESSION[jmeno]);
                    while ($row = $query->fetch()) {
                        $id_uzivatel = $row[id];
                        $email = $row[email];
                        $pohlavi = $row[pohlavi];
                        $popis = $row[popis];
                    }

                    //uložime
                    if ($no_error_heslo) {
                        if ($_POST[passwordinput] != "") {
                            $heslo_hash = md5($_POST[passwordinput]);

                            $_SESSION[heslo] = $heslo_hash;

                            $arr = array('heslo_hash' => $heslo_hash);
                            dibi::query('UPDATE [pjh_uzivatele] SET ', $arr, 'WHERE [id] = %i', $id_uzivatel);

                            echo "<div class=\"msg information\"><h2>Změna Vašeho hesla proběhla úspěšně!</h2></div>";
                        }
                    }

                    if ($no_error_popis) {
                        // uživatel
                        $arr = array('popis' => $_POST['textarea1']);
                        dibi::query('UPDATE [pjh_uzivatele] SET ', $arr, 'WHERE [id] = %i', $id_uzivatel);
                    }

                    if ($no_error_foto) {
                        // foto
                        if ($upload == "ok") {
                            $query2 = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $id_uzivatel, 'AND [typ] = %s', 'profilova');
                            while ($row2 = $query2->fetch()) {
                                $fotka = $row2[cil];
                                $fotkaid = $row2[id];
                            }
                            unlink($fotka);
                            dibi::query('DELETE FROM [pjh_fotky] WHERE [id] = %i', $fotkaid);

                            $newname = "./obrazky/" . $nazev_souboru;
                            $oldname = $cil;
                            rename($oldname, $newname);

                            $arr_fotka = array('nazev' => $_SESSION['jmeno'], 'cil' => $newname, 'typ' => "profilova", 'id_cil' => $id_uzivatel, 'poradi' => 1, 'id_autor' => $id_uzivatel, 'popis' => $_SESSION['jmeno'], 'date' => time());
                            dibi::query('INSERT INTO [pjh_fotky]', $arr_fotka);
                        } elseif ($_POST[uploaded_picture] != "") {
                            $query2 = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $id_uzivatel, 'AND [typ] = %s', 'profilova');
                            while ($row2 = $query2->fetch()) {
                                $fotka = $row2[cil];
                                $fotkaid = $row2[id];
                            }
                            unlink($fotka);
                            dibi::query('DELETE FROM [pjh_fotky] WHERE [id] = %i', $fotkaid);

                            $newname = "./obrazky/" . $_POST[uploaded_picture_nazev];
                            $oldname = $_POST[uploaded_picture];
                            rename($oldname, $newname);

                            $arr_fotka = array('nazev' => $_SESSION['jmeno'], 'cil' => $newname, 'typ' => "profilova", 'id_cil' => $id_uzivatel, 'poradi' => 1, 'id_autor' => $id_uzivatel, 'popis' => $_SESSION['jmeno'], 'date' => time());
                            dibi::query('INSERT INTO [pjh_fotky]', $arr_fotka);
                        }

                        echo "<div class=\"msg information\"><h2>Změna fotografie proběhla úspěšně!</h2></div>";
                    }
                }


                $query = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [jmeno] = %s', $_SESSION[jmeno]);
                while ($row = $query->fetch()) {
                    $id_uzivatele = $row[id];
                    $email = $row[email];
                    $pohlavi = $row[pohlavi];
                    $popis = $row[popis];
                }

                $query2 = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $id_uzivatele, 'AND [typ] = %s', 'profilova');
                if ($query2->count() == 1) {
                    $row2 = $query2->fetch();
                    $fotka = "<img src=\"$row2[cil]\" height=\"100px\"/>";
                } else {
                    $fotka = "<label for=\"\">Prozatím nemá fotku</label>";
                }
                ?>            
                <form id="formul" name="formul" method="post" action="./formulare.php?id=nastaveniuzivatele&action=send" enctype="multipart/form-data">
                    <dl class="inline">
                        <dt><label for="jmeno">Uživatelské jméno *</label></dt>
                        <dd><input type="text" class="big" id="jmeno" name="jmeno" placeholder="Vaše uživatelské jméno" value="<?php echo $_SESSION[jmeno]; ?>" required disabled></dd>
                        <dt><label for="email">Email *</label></dt>
                        <dd><input type="text" class="big" id="email" name="email" placeholder="Váš email" value="<?php echo $email; ?>" required email="true" disabled></dd>  

                        <?php
                        if ($pohlavi == "Muž") {
                            $active[muz] = "checked";
                        } elseif ($pohlavi == "Žena") {
                            $active[zena] = "checked";
                        }
                        ?>

                        <dt><label for="radio-1">Pohlaví *</label></dt>
                        <dd>
                            <input name="radio1" type="radio" id="radio-1" value="Muž" <?php echo $active[muz]; ?> required disabled><label for="radio-1">Muž</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="radio1" type="radio" id="radio-2" value="Žena" <?php echo $active[zena]; ?> disabled><label for="radio-2">Žena</label>
                        </dd>     

                        <dt><label for="">Profilové foto gurmána</label><?php if ($query2->count() == 1) echo "<br /><a href=\"./formulare.php?id=nastaveniuzivatele&smazatfoto=ano\">Smazat tuto fotografii</a>"; ?></dt>
                        <dd><?php echo $fotka; ?></dd> 

                        <dt><label for="fileToUpload">Nové profilové foto gurmána</label></dt>
                        <dd><input type="file" class="big" name="fileToUpload" id="fileToUpload"></dd>                             

                        <dt><label for="textarea1">Povězte něco o sobě *</label></dt>
                        <dd><textarea id="textarea1" name="textarea1" required maxlength="230" placeholder="Popište se jako gurmán (do 230 znaků)"><?php echo $popis; ?></textarea></dd>

                        <dt><label for="passwordinput">Nové heslo</label></dt>
                        <dd><input type="password" class="big" placeholder="Vaše nové heslo" id="passwordinput" name="passwordinput"></dd>

                        <dt><label for="passwordinput2">Zopakujte nové heslo</label></dt>
                        <dd><input type="password" class="big" placeholder="Zopakujte Vaše nové heslo" id="passwordinput2" name="passwordinput2"></dd>

                        <dt><label for="">(*) označuje povinné položky</label></dt>
                        <dd><input type="submit" class="button" id="text-input-1-submit" value="Upravit"></dd>
                    </dl>
                </form>
                <?php
            } elseif ($_GET[id] == "prihlaseni") {
                ?>         
                <h1>Přihlášení</h1>
                <?php
                if ($_POST['pokusolog'] == "1" && loguj($_SESSION['jmeno'], $_SESSION['heslo'], $_POST[zustatprihlasen]) == 1) {
                    echo "<div class=\"msg information\"><h2>Přihlášeno!</h2></div>";
                    ?>
                    <script>
                        close_fancybox_redirect_parent('./index.php', 3000);
                    </script>
                    <?php
                } elseif (loguj($_SESSION['jmeno'], $_SESSION['heslo'], $_POST[zustatprihlasen]) == 2 && $_POST['pokusolog'] == "1") {
                    echo "<div class=\"msg err\"><h2>Špatně zadané jméno nebo heslo!</h2></div>";
                    ?>
                    <form id="formul" name="formul" method="post" action="./formulare.php?id=prihlaseni" >
                        <input type="hidden" name="pokusolog" value="1">
                        <input type="hidden" name="redirect" value="<?php echo $_GET[redirect]; ?>">
                        <dl class="inline">
                            <dt><label for="jmeno">Uživ. jméno / e-mail</label></dt>
                            <dd><input type="text" class="big" id="jmeno" name="jmeno" value="<?php echo $_POST[jmeno]; ?>" placeholder="Vaše uživatelské jméno nebo e-mail" required></dd>
                            <dt><label for="heslo">Heslo</label></dt>
                            <dd><input type="password" class="big" id="heslo" name="heslo" placeholder="Vaše heslo" required></dd>
                            <dt><label for="zustatprihlasen">Zůstat přihlášen?</label></dt>
                            <dd><input name="zustatprihlasen" type="checkbox" value="ano" id="zustatprihlasen" name="zustatprihlasen"><label for="zustatprihlasen">Ano</label></dd>
                            <dt><a href="./formulare.php?id=zapomenuteheslo">Zapoměli jste jméno nebo heslo?</a></dt>
                            <dd><input type="submit" class="button" id="text-input-1-submit" value="Přihlásit">
                        </dl>
                    </form>                
                    <?php
                } else {
                    ?>
                    <form id="formul" name="formul" method="post" action="./formulare.php?id=prihlaseni" >
                        <input type="hidden" name="pokusolog" value="1">
                        <input type="hidden" name="redirect" value="<?php echo $_GET[redirect]; ?>">
                        <dl class="inline">
                            <dt><label for="jmeno">Uživ. jméno / e-mail</label></dt>
                            <dd><input type="text" class="big" id="jmeno" name="jmeno" value="<?php echo $_GET[ad]; ?>" placeholder="Vaše uživatelské jméno nebo e-mail" required></dd>
                            <dt><label for="heslo">Heslo</label></dt>
                            <dd><input type="password" class="big" id="heslo" name="heslo" placeholder="Vaše heslo" required></dd>
                            <dt><label for="zustatprihlasen">Zůstat přihlášen?</label></dt>
                            <dd><input name="zustatprihlasen" type="checkbox" value="ano" id="zustatprihlasen" name="zustatprihlasen"><label for="zustatprihlasen">Ano</label></dd>
                            <dt><a href="./formulare.php?id=zapomenuteheslo">Zapoměli jste jméno nebo heslo?</a></dt>
                            <dd><input type="submit" class="button" id="text-input-1-submit" value="Přihlásit">
                        </dl>
                    </form>
                    <?php
                }
            } elseif ($_GET[id] == "zapomenuteheslo") {
                ?>      
                <h1>Zapomenté jméno nebo heslo</h1>
                <?php
                if ($_POST['pokusolog'] == "1") {

                    $query = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [email] = %s', $_POST[textinput1]);
                    if ($query->count() == 1) {
                        $row = $query->fetch();
                        echo "<div class=\"msg information\"><h2>Odeslali jsme Vám email s Vaším novým heslem!</h2><p>V emailu jsme Vám zaslali nové heslo.</p></div>";

                        $nove_heslo = generateRandomString(10);
                        $hash_hesla = md5($nove_heslo);

                        echo $nove_heslo;

                        $arr = array('heslo_hash' => $hash_hesla);
                        dibi::query('UPDATE [pjh_uzivatele] SET ', $arr, 'WHERE [id] = %i', $row[id]);

                        $body = "Dobrý den,\n\nna základě Vaší žádosti jsme Vám vygenerovali nové heslo na serveru Pijem, jíme, hodnotíme.\n\nVaše uživatelské jméno: " . $row['jmeno'] . "\nVaše heslo je: " . $nove_heslo . "\n\nHeslo můžete kdykoliv změnit v sekci nastavení.\n\nTěšíme se, že se s náma poldělité o Vaše kulinářské zážitky.\n\nS pozdravem\nPijem, jíme, hodnotíme.";
                        send_mail_kovar($_POST[textinput1], "Zapomenuté heslo na serveru pjhvysocina.cz", $body);
                    } else {
                        echo "<div class=\"msg err\"><h2>Uživatel nenalezen - neznámý email!</h2><p>Pod takovým emailem není registrován žádný uživatel. Zkuste to znovu.</p></div>";
                        ?>
                        <form id="formul" name="formul" method="post" >
                            <input type="hidden" name="pokusolog" value="1">
                            <dl class="inline">
                                <dt><label for="textinput1">Váš e-mail</label></dt>
                                <dd><input type="text" class="big" id="textinput1" name="textinput1" placeholder="Váš e-mail" email="true" required></dd>
                                <dt></dt>
                                <dd><input type="submit" class="button" id="text-input-1-submit" value="Odeslat"></dd>
                            </dl>
                        </form>  
                        <?php
                    }
                } else {
                    ?>
                    <form id="formul" name="formul" method="post" >
                        <input type="hidden" name="pokusolog" value="1">
                        <dl class="inline">
                            <dt><label for="textinput1">Váš e-mail</label></dt>
                            <dd><input type="text" class="big" id="textinput1" name="textinput1" placeholder="Váš e-mail" email="true" required></dd>
                            <dt></dt>
                            <dd><input type="submit" class="button" id="text-input-1-submit" value="Odeslat"></dd>
                        </dl>
                    </form>  
                    <?php
                }
            } elseif ($_GET[id] == "napisrecenzi") {
                ?>
                <h1>Napiš recenzi</h1>
                <?php
                if (login_check()) {
                    $no_error_foto = true;
                    $no_error_foto_aspon_neco = true;

                    $query_upfoto = dibi::query('SELECT * FROM [pjh_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto');
                    if ($query_upfoto->count() > 0)
                        $no_error_foto_aspon_neco = false;

                    if ($_GET[hash] == "")
                        $_GET[hash] = time();

                    if ($_GET[action] == "send") {
                        if (count($_FILES['fileToUpload']['name']) > 0 && $_FILES['fileToUpload']['name'][0] != "") {
                            for ($i = 0; $i < count($_FILES['fileToUpload']['name']); $i++) {
                                if ($_FILES["fileToUpload"]["size"][$i] > 1000000) {
                                    echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - je příliš velká!</h2><p>Zřejmě je příliž veliká - maximální velikost je 1 MB.</p></div>";
                                    $no_error_foto = false;
                                } else {
                                    $timestamp = time();
                                    $nazev_souboru_tmp = $_FILES['fileToUpload']['tmp_name'][$i];
                                    $nazev_souboru = $timestamp . "_" . $_FILES['fileToUpload']['name'][$i];
                                    $cil = "./temp/" . $timestamp . "_" . $_FILES['fileToUpload']['name'][$i];
                                    $imageFileType = pathinfo($cil, PATHINFO_EXTENSION);
                                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                                        echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - nepovolený formát!</h2><p>Povolené formáty jsou JPG, PNG, JPEG, GIF</p></div>";
                                        $no_error_foto = false;
                                    } else {
                                        move_uploaded_file($nazev_souboru_tmp, $cil) or die("<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii!</h2><p>Chyba je na straně serveru, nahašte jí prosím správci.</p></div>");

                                        $arr_temp = array('id_hash' => $_GET[hash], 'znacka' => "novefoto", 'value1' => $nazev_souboru, 'value2' => $cil);
                                        dibi::query('INSERT INTO [pjh_temp]', $arr_temp);
                                        $no_error_foto_aspon_neco = false;
                                    }
                                }
                            }
                        }
//                        elseif ($no_error_foto_aspon_neco) {
//                            $no_error_foto = false;
//                            echo "<div class=\"msg err\"><h2>Nahrajte alespon jednu fotografii!</h2><p>Recenze je podmíněna přdáním fotografie podniku.</p></div>";
//                        }
                        //uložime
                        if ($no_error_foto) {

                            // recenze
                            $hodnoceni = ($_POST['jidlo'] + $_POST['obsluha'] + $_POST['prostredi']) / 3;

                            if ($_POST[radio1] == "restaurace") {
                                $typ = "restaurace";
                                $id_podnik = $_POST[selectrestaurace];
                            } elseif ($_POST[radio1] == "kavarny") {
                                $typ = "kavarny";
                                $id_podnik = $_POST[selectkavarny];
                            }


                            $query_chci_id = dibi::query('SELECT * FROM [pjh_podnik] WHERE [id] = %i', $id_podnik);
                            $row_chci_id = $query_chci_id->fetch();

                            // recenze
                            $arr_recenze = array('id_podnik' => $row_chci_id['id'], 'nazevpodnik' => $row_chci_id['nazev'], 'date' => time(), 'id_autor' => get_id_uzivatele($_SESSION['jmeno']), 'nazevautor' => $_SESSION['jmeno'], 'typ' => $typ, 'obsah' => $_POST['textarea1'], 'jidlo' => $_POST['jidlo'], 'obsluha' => $_POST['obsluha'], 'prostredi' => $_POST['prostredi'], 'hodnoceni' => $hodnoceni, 'zverejnit' => 'ne');
                            dibi::query('INSERT INTO [pjh_recenze]', $arr_recenze);

                            $query_id_receze = dibi::query('SELECT * FROM [pjh_recenze] WHERE [id_podnik] = %i', $id_podnik, 'ORDER BY %by', 'id', 'DESC');
                            $row_id_recenze = $query_id_receze->fetch();

                            send_msg_to_admins("Uživatel <em>" . $_SESSION['jmeno'] . "</em> přidal do databáze recenzci podniku <em>" . $row_chci_id['nazev'] . "</em>.");

                            //foto
                            $query_upfoto = dibi::query('SELECT * FROM [pjh_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto', 'ORDER BY %by', 'id', 'ASC');
                            $i = 1;
                            while ($row_upfoto = $query_upfoto->fetch()) {
                                $newname = "./obrazky/" . $row_upfoto[value1];
                                $oldname = $row_upfoto[value2];
                                rename($oldname, $newname);

                                $arr_fotka = array('nazev' => $row_chci_id['nazev'], 'cil' => $newname, 'typ' => "podnik", 'id_cil' => $row_chci_id['id'], 'poradi' => $i, 'id_autor' => get_id_uzivatele($_SESSION['jmeno']), 'id_recenze' => $row_id_recenze[id], 'popis' => $row_chci_id['nazev'], 'date' => time(), 'zverejnit' => "ne");
                                dibi::query('INSERT INTO [pjh_fotky]', $arr_fotka);

                                dibi::query('DELETE FROM [pjh_temp] WHERE [id] = %i', $row_upfoto[id]);

                                $i++;
                            }

                            //rovnou samoschválení
                            $arr_rec = array('zverejnit' => 'ano');
                            dibi::query('UPDATE [pjh_recenze] SET', $arr_rec, 'WHERE [id] = %i', $row_id_recenze[id]);

                            $query_recenze = dibi::query('SELECT * FROM [pjh_recenze] WHERE [id] = %i', $row_id_recenze[id]);
                            $row_recenze = $query_recenze->fetch();

                            $hodnoceni = 0;
                            $jidlo = 0;
                            $obsluha = 0;
                            $prostredi = 0;
                            $i = 0;
                            $query = dibi::query('SELECT * FROM [pjh_recenze] WHERE [id_podnik] = %i', $row_recenze[id_podnik], "AND [zverejnit] = %s", "ano");
                            while ($row = $query->fetch()) {
                                $jidlo += $row[jidlo];
                                $obsluha += $row[obsluha];
                                $prostredi += $row[prostredi];
                                $hodnoceni += $row[hodnoceni];
                                $i++;
                            }

                            $jidlo_celek = $jidlo / $i;
                            $obsluha_celek = $obsluha / $i;
                            $prostredi_celek = $prostredi / $i;
                            $hodnoceni_celek = $hodnoceni / $i;

                            $arr_pod = array('jidlo' => $jidlo_celek, 'obsluha' => $obsluha_celek, 'prostredi' => $prostredi_celek, 'hodnoceni' => $hodnoceni_celek);
                            dibi::query('UPDATE [pjh_podnik] SET', $arr_pod, 'WHERE [id] = %i', $row_recenze[id_podnik]);

                            $query_fotky = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $row_recenze[id_podnik], 'AND [typ] = %s', 'podnik', "AND [zverejnit] = %s", "ano");
                            $pocet_fotek = $query_fotky->count();
                            $i = $pocet_fotek + 1;

                            $query_fotky = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_recenze] = %i', $row_recenze[id], 'ORDER BY %by', 'poradi', 'ASC');
                            while ($row_fotky = $query_fotky->fetch()) {
                                $arr_fot = array('zverejnit' => 'ano', 'poradi' => $i);
                                dibi::query('UPDATE [pjh_fotky] SET', $arr_fot, ' WHERE [id] = %i', $row_fotky[id]);
                                $i++;
                            }

                            $query_uzivat = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [id] = %i', $row_recenze[id_autor]);
                            $row_uzivat = $query_uzivat ->fetch();                            
                            
                            $arr_uzivat = array('hodnost' => $row_uzivat[hodnost]+1);
                            dibi::query('UPDATE [pjh_uzivatele] SET', $arr_uzivat, ' WHERE [id] = %i', $row_recenze[id_autor]);                           
                            
                            $arr_temp = array('id_cil' => $row_recenze[id_autor], 'date' => time(), 'obsah' => "<em>Přidali jsme recenzi podniku <u>" . $row_recenze[nazevpodnik] . "</u></em>, kterou jste navrh, do naší databáze.", 'stav' => "nova");
                            dibi::query('INSERT INTO [pjh_zpravy]', $arr_temp);
                            
                            $body = "Dobrý den,\n\n přidali jsme recenzi podniku <u>" . $row_recenze[nazevpodnik] . "</u></em>, kterou jste navrh, do naší databáze na serveru Pijem, jíme, hodnotíme.\n\nTěšíme se, že se s náma poldělité o Vaše kulinářské zážitky.\n\nS pozdravem\nPijem, jíme, hodnotíme.";
                            send_mail_kovar(get_email_uzivatele($row_recenze[id_autor]), "Přidaná recenze na serveru pjhvysocina.cz", $body);                            
                            
                            
                            if ($no_error_foto_aspon_neco) {
                                //$no_error_foto = false;
                                //echo "<div class=\"msg err\"><h2>Nahrajte alespon jednu fotografii!</h2><p>Recenze je podmíněna přdáním fotografie podniku.</p></div>";
                                //$arr_fotka = array('nazev' => $row_chci_id['nazev'], 'cil' => "./img/nofoto.png", 'typ' => "podnik", 'id_cil' => $row_chci_id['id'], 'poradi' => $i, 'id_autor' => get_id_uzivatele($_SESSION['jmeno']), 'id_recenze' => $row_id_recenze[id], 'popis' => $row_chci_id['nazev'], 'date' => time(), 'zverejnit' => "ne");
                                //dibi::query('INSERT INTO [pjh_fotky]', $arr_fotka);
                            }

                            echo "<div class=\"msg information\"><h2>Recenze podniku s názvem '" . $row_chci_id['nazev'] . "' byla úspěšně přidána!</h2></div>";
                        }
                    }

                    if ($_GET[action] != "send" || $no_error_foto == false) {
                        ?>     
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare.php?id=napisrecenzi&hash=<?php echo $_GET[hash]; ?>&action=send" enctype="multipart/form-data" >

                            <dl class="inline">
                                <?php
                                if ($_GET[ad] == "restaurace" || $_POST[radio1] == "restaurace") {
                                    $select_kategorie[restaurace] = "checked";
                                    $novypodnik = "restaurace";
                                    $select_podnik[0] = "";
                                    $select_podnik[1] = "hidden";
                                } elseif ($_GET[ad] == "kavarny" || $_POST[radio1] == "kavarny") {
                                    $select_kategorie[kavarny] = "checked";
                                    $novypodnik = "kavarny";
                                    $select_podnik[0] = "hidden";
                                    $select_podnik[1] = "";
                                }
                                ?>
                                <dt><label for="radio-1">Kategorie *</label></dt>
                                <dd>
                                    <input name="radio1" type="radio" id="radio-1" onchange="show_hide('selectrestaurace', 'selectkavarny');
                                                        show_hide('jid', 'kav');" value="restaurace" <?php echo $select_kategorie[restaurace]; ?> required><label for="radio-1">Restaurace</label><br />
                                    <input name="radio1" type="radio" id="radio-2" onchange="show_hide('selectkavarny', 'selectrestaurace');
                                                        show_hide('kav', 'jid');" value="kavarny" <?php echo $select_kategorie[kavarny]; ?>><label for="radio-2">Kavárna</label><br />
                                    <input name="radio1" type="radio" id="radio-3" disabled><label for="radio-3"><span class="non_active">Rychlé občerstvení</span></label>
                                </dd>      

                                <dt><label for="select">Podnik *</label></dt>
                                <dd>
                                    <select id="selectrestaurace" name="selectrestaurace" class="select <?php echo $select_podnik[0]; ?>" size="5" required>
                                        <option value="">--- vyberte ---</option>
                                        <?php
                                        $query_restaurace = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", "restaurace", "AND [zverejnit] = %s", "ano", "ORDER BY %by", "nazev", "ASC");
                                        while ($row_restaurace = $query_restaurace->fetch()) {
                                            if ($_POST[radio1] == "restaurace" && $_POST[selectrestaurace] == $row_restaurace[id]) {
                                                echo "<option value=\"$row_restaurace[id]\" selected>$row_restaurace[nazev]</option>";
                                            } elseif ($_GET[cd] != "" && $_GET[cd] == $row_restaurace[id]) {
                                                echo "<option value=\"$row_restaurace[id]\" selected>$row_restaurace[nazev]</option>";
                                            } else {
                                                echo "<option value=\"$row_restaurace[id]\">$row_restaurace[nazev]</option>";
                                            }
                                        }
                                        ?>
                                    </select>

                                    <select id="selectkavarny" name="selectkavarny" class="select <?php echo $select_podnik[1]; ?>" size="5" required>
                                        <option value="">--- vyberte ---</option>
                                        <?php
                                        $query_kavarny = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", "kavarny", "AND [zverejnit] = %s", "ano", "ORDER BY %by", "nazev", "ASC");
                                        while ($row_kavarny = $query_kavarny->fetch()) {
                                            if ($_POST[radio1] == "kavarny" && $_POST[selectkavarny] == $row_kavarny[id]) {
                                                echo "<option value=\"$row_kavarny[id]\" selected>$row_kavarny[nazev]</option>";
                                            } elseif ($_GET[cd] != "" && $_GET[cd] == $row_kavarny[id]) {
                                                echo "<option value=\"$row_kavarny[id]\" selected>$row_kavarny[nazev]</option>";
                                            } else {
                                                echo "<option value=\"$row_kavarny[id]\">$row_kavarny[nazev]</option>";
                                            }
                                        }
                                        ?>
                                    </select>                                    
                                </dd>
                                <dt></dt>
                                <dd><a href="./formulare.php?id=novypodnik&ad=<?php echo $novypodnik; ?>" class="button_small">Přidat nový podnik</a></dd>

                                <?php
                                $query_upfoto = dibi::query('SELECT * FROM [pjh_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto', 'ORDER BY %by', 'id', 'ASC');
                                if ($query_upfoto->count() > 0) {
                                    echo "<dt><label for=\"fileToUpload\">Nahrané fotografie</label></dt>"
                                    . "<dd>";
                                    while ($row_upfoto = $query_upfoto->fetch()) {
                                        echo "<img src=\"" . $row_upfoto[value2] . "\" height=\"100px\" class=\"img_nahrano\" />";
                                    }
                                    echo "</dd>";
                                }
                                ?>

                                <?php
                                if ($_POST[textarea1] == "" || $no_error_foto == false) {
                                    ?>
                                    <dt><label for="fileToUpload">Fotografie podniku</label><br /><a href="#" onclick="add_more_foto()">přidat další fotografii</a></dt>
                                    <dd>
                                        <div id="addfoto">
                                            <input type="file" class="big" name="fileToUpload[]" id="fileToUpload">
                                        </div>
                                    </dd>                        
                                    <?php
                                }
                                ?>
                                <div class="cara"> </div>

                                <dt>
                                <?php
                                if ($_POST[radio1] == "restaurace" || $_GET[ad] == "restaurace") {
                                    echo "<label id=\"jid\" for=\"jidlo\" class=\"\">Jídlo (0 - 10 bodů) *</label><label id=\"kav\" for=\"jidlo\" class=\"hidden\">Kávy (0 - 10 bodů) *</label>";
                                } elseif ($_POST[radio1] == "kavarny" || $_GET[ad] == "kavarny") {
                                    echo "<label id=\"jid\" for=\"jidlo\" class=\"hidden\">Jídlo (0 - 10 bodů) *</label><label id=\"kav\" for=\"jidlo\" class=\"\">Kávy (0 - 10 bodů) *</label>";
                                }
                                ?>                                   
                                </dt>
                                <dd><input type="range" class="range" name="jidlo" id="jidlo" min="0.0" max="10.0" value="<?php
                                    if ($_POST[jidlo] == "")
                                        echo "5.0";
                                    else
                                        echo $_POST[jidlo];
                                    ?>" step="0.1" oninput="jidlovalue.value=value" /><output id="jidlovalue" class="range_output"><?php
                                           if ($_POST[jidlo] == "")
                                               echo "5.0";
                                           else
                                               echo $_POST[jidlo];
                                           ?></output></dd>


                                <dt><label for="obsluha">Obsluha (0 - 10 bodů) *</label></dt>
                                <dd><input type="range" class="range" name="obsluha" id="obsluha" min="0.0" max="10.0" value="<?php
                                    if ($_POST[obsluha] == "")
                                        echo "5.0";
                                    else
                                        echo $_POST[obsluha];
                                    ?>" step="0.1" oninput="obsluhavalue.value=value" /><output id="obsluhavalue" class="range_output"><?php
                                           if ($_POST[obsluha] == "")
                                               echo "5.0";
                                           else
                                               echo $_POST[obsluha];
                                           ?></output></dd>


                                <dt><label for="prostredi">Prostředí (0 - 10 bodů) *</label></dt>
                                <dd><input type="range" class="range" name="prostredi" id="prostredi" min="0.0" max="10.0" value="<?php
                                    if ($_POST[prostredi] == "")
                                        echo "5.0";
                                    else
                                        echo $_POST[prostredi];
                                    ?>" step="0.1" oninput="prostredivalue.value=value" /><output id="prostredivalue" class="range_output"><?php
                                           if ($_POST[prostredi] == "")
                                               echo "5.0";
                                           else
                                               echo $_POST[prostredi];
                                           ?></output></dd>

                                <div class="cara"> </div>

                                <dt><label for="textarea1">Recenze *</label></dt>
                                <dd><textarea id="textarea1" name="textarea1" placeholder="Vaše recenze" required><?php echo $_POST[textarea1]; ?></textarea></dd>

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="submit1" value="Přidat recenzi"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } else {
                    echo "<div class=\"msg err\"><h2>Přidávat recenze může jen registrovaný gurmán!</h2><p>Můžete se <a href=\"./formulare.php?id=prihlaseni\">přihlásit</a> nebo <a href=\"./formulare.php?id=registrace\">registrovat</a>.</p></div>";
                }
            } elseif ($_GET[id] == "recenze") {
                $query = dibi::query("SELECT * FROM [pjh_recenze] WHERE [id] = %i", $_GET[ad], "AND [zverejnit] = %s", "ano");
                $row = $query->fetch();
                ?>   
                <div class="card_prehledy70_bez_hovereffectu border_bottom">
                    <a href="./index.php?id=vyhledavani&ad=<?php echo $row[typ]; ?>&bd=profil&cd=<?php echo $row[id_podnik]; ?>" target="_parent" title="<?php echo $row[nazevpodnik]; ?>">
                        <div class="card_vnitrni">
                            <div>
                                <div class="grid_1 card_prehledy_border70_bez_cary">
                                    <div class="card_vertical_align15 ">
                                        <span class="card_text_cislo cerna_barva">1</span>
                                    </div>
                                </div>
                                <div class="grid_2">
                                    <img class="card_obrazek_podnik100" src="<?php echo get_foto_of_podnik($row[id_podnik]); ?>" alt=""/>
                                </div>
                                <div class="grid_9 card_prehledy_border70_bez_cary">
                                    <div class="card_vertical_align15">
                                        <span class="card_text_nazev cerna_barva"><?php echo $row[nazevpodnik]; ?></span><br />
                                        <span class="card_text_procenta "><?php echo $row[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row[hodnoceni]); ?>" alt="" height="16px"/></span>
                                    </div>
                                </div>                                             
                                <div class="clear"></div>
                            </div>
                        </div>
                    </a>
                </div>        
                <h4 class="bold center margin_top2">
                    <?php echo $row[nazevautor]; ?> (<?php echo echo_date($row[date]); ?>)</h4>
                <p class="card_text cerna_barva"><?php echo nl2br($row[obsah]); ?></p>
                <?php
            } elseif ($_GET[id] == "naserecenze") {
                $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [id] = %i", $_GET[ad], "AND [zverejnit] = %s", "ano");
                $row = $query->fetch();
                ?>   
                <div class="card_prehledy70_bez_hovereffectu border_bottom">
                    <a href="./index.php?id=vyhledavani&ad=<?php echo $row[typ]; ?>&bd=profil&cd=<?php echo $row[id]; ?>" target="_parent" title="<?php echo $row[nazev]; ?>">
                        <div class="card_vnitrni">
                            <div>
                                <div class="grid_1 card_prehledy_border70_bez_cary">
                                    <div class="card_vertical_align15 ">
                                        <span class="card_text_cislo cerna_barva">1</span>
                                    </div>
                                </div>
                                <div class="grid_2">
                                    <img class="card_obrazek_podnik100" src="<?php echo get_foto_of_podnik($row[id]); ?>" alt=""/>
                                </div>
                                <div class="grid_9 card_prehledy_border70_bez_cary">
                                    <div class="card_vertical_align15">
                                        <span class="card_text_nazev cerna_barva"><?php echo $row[nazev]; ?></span><br />
                                        <span class="card_text_procenta "><?php echo $row[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row[hodnoceni]); ?>" alt="" height="16px"/></span>
                                    </div>
                                </div>                                             
                                <div class="clear"></div>
                            </div>
                        </div>
                    </a>
                </div>        
                <h4 class="bold center margin_top2">Naše recenze (<?php echo echo_date($row[date_doporuceno]); ?>)</h4>
                <p class="card_text cerna_barva"><?php echo nl2br($row[naserecenze]); ?></p>
                <?php
            } elseif ($_GET[id] == "popis") {

                $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [id] = %i", $_GET[ad], "AND [zverejnit] = %s", "ano");
                $row = $query->fetch();
                ?>   
                <div class="card_prehledy70_bez_hovereffectu border_bottom">
                    <a href="./index.php?id=vyhledavani&ad=<?php echo $row[typ]; ?>&bd=profil&cd=<?php echo $row[id]; ?>" target="_parent" title="<?php echo $row[nazev]; ?>">
                        <div class="card_vnitrni">
                            <div>
                                <div class="grid_1 card_prehledy_border70_bez_cary">
                                    <div class="card_vertical_align15 ">
                                        <span class="card_text_cislo cerna_barva">1</span>
                                    </div>
                                </div>
                                <div class="grid_2">
                                    <img class="card_obrazek_podnik100" src="<?php echo get_foto_of_podnik($row[id]); ?>" alt=""/>
                                </div>
                                <div class="grid_9 card_prehledy_border70_bez_cary">
                                    <div class="card_vertical_align15">
                                        <span class="card_text_nazev cerna_barva"><?php echo $row[nazev]; ?></span><br />
                                        <span class="card_text_procenta "><?php echo $row[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row[hodnoceni]); ?>" alt="" height="16px"/></span>
                                    </div>
                                </div>                                             
                                <div class="clear"></div>
                            </div>
                        </div>
                    </a>
                </div>        
                <h4 class="bold center margin_top2">Popis</h4>
                <p class="card_text cerna_barva"><?php echo nl2br($row[obsah]); ?></p>
                <?php
            } elseif ($_GET[id] == "gurman") {
                $query = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [id] = %i', $_GET[ad]);
                $row = $query->fetch();
                ?>   
                <div class="card_prehledy70_bez_hovereffectu border_bottom">
                    <div class="card_vnitrni">
                        <div>
                            <div class="grid_1 card_prehledy_border70_bez_cary">       
                                <img class="card_obrazek_podnik80" src="<?php echo get_foto_of_user($_GET[ad]); ?>" alt=""/>
                            </div>
                            <div class="grid_4 card_prehledy_border70_bez_cary">
                                <div class="card_vertical_align5">
                                    <span class="card_text_nazev cerna_barva"><?php echo $row[jmeno]; ?></span><br />
                                    <span class="card_text_procenta cerna_barva "><?php echo echo_hodnost($row[hodnost]); ?></span>
                                </div>
                            </div> 
                            <div class="grid_7">
                                <p class="card_text cerna_barva"><?php echo $row[popis]; ?></p>
                            </div>

                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                <div id="sezazeni">
                    <ul>
                        <?php
                        if (($_GET[order] == "nazevpodnik" && $_GET[by] == "desc") || $_GET[order] == "") {
                            $_GET[order] = "nazevpodnik";
                            $_GET[by] = "desc";
                            echo "Seřadit podle: <li class=\"first active\"><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=nazevpodnik&by=asc\">Názvu podniku <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li><li><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=date&by=desc\">Data</a></li>";
                        } elseif (($_GET[order] == "nazevpodnik" && $_GET[by] == "asc") || $_GET[order] == "") {
                            echo "Seřadit podle: <li class=\"first active\"><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=nazevpodnik&by=desc\">Názvu podniku <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li><li><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=date&by=desc\">Data</a></li>";
                        } elseif (($_GET[order] == "hodnoceni" && $_GET[by] == "desc") || $_GET[order] == "") {
                            echo "Seřadit podle: <li class=\"first\"><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=nazevpodnik&by=desc\">Názvu podniku</a></li><li class=\"active\"><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=hodnoceni&by=asc\">Hodnocení <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li><li><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=date&by=desc\">Data</a></li>";
                        } elseif (($_GET[order] == "hodnoceni" && $_GET[by] == "asc") || $_GET[order] == "") {
                            echo "Seřadit podle: <li class=\"first\"><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=nazevpodnik&by=desc\">Názvu podniku</a></li><li class=\"active\"><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li><li><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=date&by=desc\">Data</a></li>";
                        } elseif (($_GET[order] == "date" && $_GET[by] == "desc") || $_GET[order] == "") {
                            echo "Seřadit podle: <li class=\"first\"><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=nazevpodnik&by=desc\">Názvu podniku</a></li><li><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li class=\"active\"><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=date&by=asc\">Data <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li>";
                        } elseif (($_GET[order] == "date" && $_GET[by] == "asc") || $_GET[order] == "") {
                            echo "Seřadit podle: <li class=\"first\"><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=nazevpodnik&by=desc\">Názvu podniku</a></li><li><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=hodnoceni&by=desc\">Hodnocení</a></li><li class=\"active\"><a href=\"./formulare.php?id=gurman&ad=$_GET[ad]&order=date&by=desc\">Data <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li>";
                        }
                        ?>
                    </ul>
                    <div class="clear"></div>   
                </div>
                <?php
                $query2 = dibi::query("SELECT * FROM [pjh_recenze] WHERE [id_autor] = %i", $_GET[ad], "AND [zverejnit] = %s", "ano", "ORDER BY %by", $_GET[order], $_GET[by]);
                $i = 1;
                if ($query2->count() == 0)
                    echo "<div class=\"msg err\"><h2>Tento uživatel zatím nenapsal žádnou recenzi!</h2><p>Čekáme až se s námi podělí o své kulinářské zážitky.</p></div>";
                while ($row2 = $query2->fetch()) {
                    $query3 = dibi::query("SELECT * FROM [pjh_podnik] WHERE [id] = %i", $row2[id_podnik], "AND [zverejnit] = %s", "ano");
                    $row3 = $query3->fetch();
                    ?>
                    <div id="<?php echo $i; ?>" class="card_prehledy70 border_bottom">
                        <a href="./index.php?id=vyhledavani&ad=<?php echo $row3[typ]; ?>&bd=profil&cd=<?php echo $row2[id_podnik]; ?>" target="_parent" onclick="parent.$.fancybox.close();">
                            <div class="card_vnitrni">
                                <div>
                                    <div class="grid_1 card_prehledy_border70_bez_cary">
                                        <div class="card_vertical_align20 ">
                                            <span class="card_text_cislo_hodnoceni cerna_barva"><?php echo $row2[hodnoceni]; ?></span>
                                        </div>
                                    </div>
                                    <div class="grid_1">
                                        <div class="card_vertical_align10">
                                            <img class="card_obrazek_podnik100" src="<?php echo get_foto_of_podnik($row2[id_podnik]); ?>" alt=""/>
                                        </div>
                                    </div>
                                    <div class="grid_4 card_prehledy_border70_bez_cary">
                                        <div class="card_vertical_align15">
                                            <span class="card_text_nazev cerna_barva"><?php echo $row3[nazev]; ?></span><br />
                                            <span class="card_text_procenta "><?php echo $row3[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row3[hodnoceni]); ?>" alt="" height="16px"/></span>
                                        </div>
                                    </div>
                                    </a>
                                    <div class="grid_6">
                                        <div id="a<?php echo $i; ?>" class=""><p class="card_text cerna_barva text_11px"><span class="bold">(<?php echo echo_date($row2[date]); ?>)</span> <?php echo substr(nl2br($row2[obsah]), 0, 150); ?>...<a href="#" class="cerna_barva bold male" onclick="rolovat_recenzi_down_gurmani(<?php echo $i; ?>, 'a<?php echo $i; ?>', 'b<?php echo $i; ?>')"><strong>[zobrazit celý text]</strong></a>
                                            </p></div>
                                        <div id="b<?php echo $i; ?>" class="hidden"><p class="card_text cerna_barva text_11px"><span class="bold">(<?php echo echo_date($row2[date]); ?>)</span> <?php echo nl2br($row2[obsah]); ?>
                                                <br /> <a class="cerna_barva bold male" href="#" onclick="rolovat_recenzi_up_gurmani(<?php echo $i; ?>, 'a<?php echo $i; ?>', 'b<?php echo $i; ?>')">[zavřít celý text]</a>
                                            </p></div>
                                    </div>

                                    <div class="clear"></div>
                                </div>
                            </div>
                    </div>  
                    <?php
                    $i++;
                }
            } elseif ($_GET[id] == "zpravy") {
                echo "<h1>Zprávy</h1>";
                $query = dibi::query('SELECT * FROM [pjh_zpravy] WHERE [id_cil] = %s', get_id_uzivatele($_SESSION[jmeno]), 'ORDER BY %by', 'date', 'DESC');

                $query2 = dibi::query('SELECT * FROM [pjh_zpravy] WHERE [id_cil] = %s', get_id_uzivatele($_SESSION[jmeno]), 'AND [stav] = %s', 'nova');
                if ($query2->count() == 0)
                    echo "<div class=\"msg information\"><h2>Nemáte žádnou novou zprávu!</h2></div>";

                $i = 1;
                while ($row = $query->fetch()) {
                    $arr = array('stav' => '');
                    dibi::query('UPDATE [pjh_zpravy] SET ', $arr, 'WHERE [id] = %i', $row[id]);
                    ?>
                    <div id="<?php echo $i; ?>" class="card_prehledy70 border_bottom">
                        <div class="card_vnitrni">
                            <div>
                                <div class="grid_2 card_prehledy_border70_bez_cary">
                                    <div class="card_vertical_align20 ">
                                        <span class="card_text_cislo_hodnoceni cerna_barva"><?php
                                            if ($row[stav] == "nova")
                                                echo "nová";
                                            else
                                                echo "přečtená"
                                                ?></span>
                                    </div>
                                </div>
                                <div class="grid_2 card_prehledy_border70_bez_cary">
                                    <div class="card_vertical_align25">
                                        <span class="card_text_nazev cerna_barva"><?php echo echo_date($row[date]); ?></span><br />
                                    </div>
                                </div>
                                </a>
                                <div class="grid_8">
                                    <div id="a<?php echo $i; ?>" class=""><p class="card_text cerna_barva text_11px"><?php echo substr(nl2br($row[obsah]), 0, 250); ?>...<a href="#" class="cerna_barva bold male" onclick="rolovat_recenzi_down_gurmani(<?php echo $i; ?>, 'a<?php echo $i; ?>', 'b<?php echo $i; ?>')"><strong>[zobrazit celý text]</strong></a>
                                        </p></div>
                                    <div id="b<?php echo $i; ?>" class="hidden"><p class="card_text cerna_barva text_11px"><?php echo nl2br($row[obsah]); ?>
                                            <br /> <a class="cerna_barva bold male" href="#" onclick="rolovat_recenzi_up_gurmani(<?php echo $i; ?>, 'a<?php echo $i; ?>', 'b<?php echo $i; ?>')">[zavřít celý text]</a>
                                        </p></div>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>  
                    <?php
                    $i++;
                }
            } elseif ($_GET[id] == "novypodnik") {
                echo "<h1>Nový podnik</h1>";
                if (login_check()) {
                    $no_error_foto = true;
                    $no_error_jmeno = true;
                    $no_error_foto_aspon_neco = true;

                    $query_upfoto = dibi::query('SELECT * FROM [pjh_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto');
                    if ($query_upfoto->count() > 0)
                        $no_error_foto_aspon_neco = false;

                    if ($_GET[hash] == "")
                        $_GET[hash] = time();


                    if ($_GET[action] == "send") {
                        $query = dibi::query('SELECT * FROM [pjh_podnik] WHERE [nazev] = %s', $_POST[nazevpodniku], "AND [zverejnit] <> %s", "smazat");

                        if ($query->count() > 0) {
                            echo "<div class=\"msg err\"><h2>Podnik s tímto jménem již existuje!</h2><p>Vyberte jiné jméno.</p></div>";
                            $no_error_jmeno = false;
                        }

                        if (count($_FILES['fileToUpload']['name']) > 0 && $_FILES['fileToUpload']['name'][0] != "") {
                            for ($i = 0; $i < count($_FILES['fileToUpload']['name']); $i++) {
                                if ($_FILES["fileToUpload"]["size"][$i] > 1000000) {
                                    echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - je příliš velká!</h2><p>Zřejmě je příliž veliká - maximální velikost je 1 MB.</p></div>";
                                    $no_error_foto = false;
                                } else {
                                    $timestamp = time();
                                    $nazev_souboru_tmp = $_FILES['fileToUpload']['tmp_name'][$i];
                                    $nazev_souboru = $timestamp . "_" . $_FILES['fileToUpload']['name'][$i];
                                    $cil = "./temp/" . $timestamp . "_" . $_FILES['fileToUpload']['name'][$i];
                                    $imageFileType = pathinfo($cil, PATHINFO_EXTENSION);
                                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                                        echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - nepovolený formát!</h2><p>Povolené formáty jsou JPG, PNG, JPEG, GIF</p></div>";
                                        $no_error_foto = false;
                                    } else {
                                        move_uploaded_file($nazev_souboru_tmp, $cil) or die("<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii!</h2><p>Chyba je na straně serveru, nahašte jí prosím správci.</p></div>");

                                        $arr_temp = array('id_hash' => $_GET[hash], 'znacka' => "novefoto", 'value1' => $nazev_souboru, 'value2' => $cil);
                                        dibi::query('INSERT INTO [pjh_temp]', $arr_temp);
                                        $no_error_foto_aspon_neco = false;
                                    }
                                }
                            }
                        }


                        //uložime
                        if ($no_error_foto && $no_error_jmeno) {

                            // podnik
                            $hodnoceni = ($_POST['jidlo'] + $_POST['obsluha'] + $_POST['prostredi']) / 3;

                            $arr_podnik = array('nazev' => $_POST['nazevpodniku'], 'adresa' => $_POST['adresa'], 'okres' => $_POST['okres'], 'obsah' => $_POST['textarea_popis'], 'date' => time(), 'typ' => $_POST['radio1'], 'jidlo' => $_POST['jidlo'], 'obsluha' => $_POST['obsluha'], 'prostredi' => $_POST['prostredi'], 'hodnoceni' => $hodnoceni, 'vlozil' => $_SESSION['jmeno'], 'zverejnit' => 'ne');
                            dibi::query('INSERT INTO [pjh_podnik]', $arr_podnik);

                            $query_chci_id = dibi::query('SELECT * FROM [pjh_podnik] WHERE [nazev] = %s', $_POST[nazevpodniku]);
                            $row_chci_id = $query_chci_id->fetch();

                            // recenze
                            $arr_recenze = array('id_podnik' => $row_chci_id['id'], 'nazevpodnik' => $_POST['nazevpodniku'], 'date' => time(), 'id_autor' => get_id_uzivatele($_SESSION['jmeno']), 'nazevautor' => $_SESSION['jmeno'], 'typ' => $_POST['radio1'], 'obsah' => $_POST['textarea1'], 'jidlo' => $_POST['jidlo'], 'obsluha' => $_POST['obsluha'], 'prostredi' => $_POST['prostredi'], 'hodnoceni' => $hodnoceni, 'zverejnit' => 'ne');
                            dibi::query('INSERT INTO [pjh_recenze]', $arr_recenze);

                            send_msg_to_admins("Uživatel <em>" . $_SESSION['jmeno'] . "</em> přidal do databáze podnik s názvem <em>" . $_POST['nazevpodniku'] . "</em>.");

                            //foto



                            $query_upfoto = dibi::query('SELECT * FROM [pjh_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto', 'ORDER BY %by', 'id', 'ASC');
                            $i = 1;
                            while ($row_upfoto = $query_upfoto->fetch()) {
                                $newname = "./obrazky/" . $row_upfoto[value1];
                                $oldname = $row_upfoto[value2];
                                rename($oldname, $newname);

                                $arr_fotka = array('nazev' => $_POST['nazevpodniku'], 'cil' => $newname, 'typ' => "podnik", 'id_cil' => $row_chci_id['id'], 'poradi' => $i, 'id_autor' => get_id_uzivatele($_SESSION['jmeno']), 'popis' => $_POST['nazevpodniku'], 'date' => time(), 'zverejnit' => "ne");
                                dibi::query('INSERT INTO [pjh_fotky]', $arr_fotka);

                                dibi::query('DELETE FROM [pjh_temp] WHERE [id] = %i', $row_upfoto[id]);

                                $i++;
                            }

                            if ($no_error_foto_aspon_neco) {
                                //$no_error_foto = false;
                                //echo "<div class=\"msg err\"><h2>Nahrajte alespon jednu fotografii!</h2><p>Recenze je podmíněna přdáním fotografie podniku.</p></div>";
                                $arr_fotka = array('nazev' => $_POST['nazevpodniku'], 'cil' => "./img/nofoto.png", 'typ' => "podnik", 'id_cil' => $row_chci_id['id'], 'poradi' => $i, 'id_autor' => get_id_uzivatele($_SESSION['jmeno']), 'popis' => $_POST['nazevpodniku'], 'date' => time(), 'zverejnit' => "ne");
                                dibi::query('INSERT INTO [pjh_fotky]', $arr_fotka);
                            }

                            echo "<div class=\"msg information\"><h2>Podnik s názvem '" . $_POST[nazevpodniku] . "' byl úspěšně přidán a byla přidána i Vaše recenze tohoto podniku!</h2><p>Podnik nebude prozatím zobrazen v naší databázi, vyčkejte než jeho přidání schválíme.</p></div>";
                        }
                    }

                    if ($_GET[action] != "send" || $no_error_foto == false || $no_error_jmeno == false) {
                        ?>                
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare.php?id=novypodnik&ad=<?php echo $_GET[ad]; ?>&hash=<?php echo $_GET[hash]; ?>&action=send" enctype="multipart/form-data" >

                            <dl class="inline">
                                <?php
                                if ($_GET[ad] == "restaurace" || $_POST[radio1] == "restaurace") {
                                    $select_kategorie[restaurace] = "checked";
                                } elseif ($_GET[ad] == "kavarny" || $_POST[radio1] == "kavarny") {
                                    $select_kategorie[kavarny] = "checked";
                                }
                                ?>
                                <dt><label for="radio-1">Kategorie *</label></dt>
                                <dd>
                                    <input name="radio1" type="radio" id="radio-1" value="restaurace" <?php echo $select_kategorie[restaurace]; ?> onchange="show_hide('jid', 'kav');" required><label for="radio-1">Restaurace</label><br />
                                    <input name="radio1" type="radio" id="radio-2" value="kavarny" <?php echo $select_kategorie[kavarny]; ?> onchange="show_hide('kav', 'jid');"><label for="radio-2">Kavárna</label><br />
                                    <input name="radio1" type="radio" id="radio-3" disabled><label for="radio-3"><span class="non_active">Rychlé občerstvení</span></label>
                                </dd>      

                                <dt><label for="nazevpodniku">Název podniku *</label></dt>
                                <dd><input type="text" class="big" id="nazevpodniku" name="nazevpodniku" value="<?php echo $_POST[nazevpodniku]; ?>" placeholder="Název podniku" required></dd>                        

                                <dt><label for="adresa">Adresa podniku *</label></dt>
                                <dd><input type="text" class="big" id="adresa" name="adresa" value="<?php echo $_POST[adresa]; ?>" placeholder="Adresa podniku" required></dd>

                                <?php
                                if ($_POST[okres] == "ZnS") {
                                    $select_okres[ZnS] = "selected";
                                } elseif ($_POST[okres] == "T") {
                                    $select_okres[T] = "selected";
                                } elseif ($_POST[okres] == "P") {
                                    $select_okres[P] = "selected";
                                } elseif ($_POST[okres] == "HB") {
                                    $select_okres[HB] = "selected";
                                } elseif ($_POST[okres] == "J") {
                                    $select_okres[J] = "selected";
                                }
                                ?>

                                <dt><label for="okres">Okres *</label></dt>
                                <dd>
                                    <select id="okres" name="okres" class="select" required>
                                        <option value="">Vyberte</option>
                                        <option value="ZnS" <?php echo $select_okres[ZnS]; ?>>Žďár nad Sázavou</option>
                                        <option value="T" <?php echo $select_okres[T]; ?>>Třebíč</option>
                                        <option value="P" <?php echo $select_okres[P]; ?>>Pelhřimov</option>
                                        <option value="HB" <?php echo $select_okres[HB]; ?>>Havlíčkův Brod</option>
                                        <option value="J" <?php echo $select_okres[J]; ?>>Jihlava</option>
                                    </select>     
                                </dd>


                                <dt><label for="textarea_popis">Popis podniku *</label></dt>
                                <dd><textarea id="textarea_popis" name="textarea_popis" placeholder="Popis podniku" required><?php echo $_POST[textarea_popis]; ?></textarea></dd>

                                <?php
                                $query_upfoto = dibi::query('SELECT * FROM [pjh_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto', 'ORDER BY %by', 'id', 'ASC');
                                if ($query_upfoto->count() > 0) {
                                    echo "<dt><label for=\"fileToUpload\">Nahrané fotografie</label></dt>"
                                    . "<dd>";
                                    while ($row_upfoto = $query_upfoto->fetch()) {
                                        echo "<img src=\"" . $row_upfoto[value2] . "\" height=\"100px\" class=\"img_nahrano\" />";
                                    }
                                    echo "</dd>";
                                }
                                ?>

                                <?php
                                if ($_POST[textarea1] == "" || $no_error_foto == false) {
                                    ?>
                                    <dt><a name="adfoto"></a><label for="fileToUpload">Fotografie podniku</label><br /><a href="#adfoto" onclick="add_more_foto()">přidat další fotografii</a></dt>
                                    <dd>
                                        <div id="addfoto">
                                            <input type="file" class="big" name="fileToUpload[]" id="fileToUpload">
                                        </div>
                                    </dd>                        
                                    <?php
                                }
                                ?>

                                <div class="cara"> </div>

                                <dt>
                                <?php
                                if ($_POST[radio1] == "restaurace" || $_GET[ad] == "restaurace") {
                                    echo "<label id=\"jid\" for=\"jidlo\" class=\"\">Jídlo (0 - 10 bodů) *</label><label id=\"kav\" for=\"jidlo\" class=\"hidden\">Kávy (0 - 10 bodů) *</label>";
                                } elseif ($_POST[radio1] == "kavarny" || $_GET[ad] == "kavarny") {
                                    echo "<label id=\"jid\" for=\"jidlo\" class=\"hidden\">Jídlo (0 - 10 bodů) *</label><label id=\"kav\" for=\"jidlo\" class=\"\">Kávy (0 - 10 bodů) *</label>";
                                }
                                ?>          
                                </dt>
                                <dd><input type="range" class="range" name="jidlo" id="jidlo" min="0.0" max="10.0" value="<?php
                                    if ($_POST[jidlo] == "")
                                        echo "5.0";
                                    else
                                        echo $_POST[jidlo];
                                    ?>" step="0.1" oninput="jidlovalue.value=value" /><output id="jidlovalue" class="range_output"><?php
                                           if ($_POST[jidlo] == "")
                                               echo "5.0";
                                           else
                                               echo $_POST[jidlo];
                                           ?></output></dd>


                                <dt><label for="obsluha">Obsluha (0 - 10 bodů) *</label></dt>
                                <dd><input type="range" class="range" name="obsluha" id="obsluha" min="0.0" max="10.0" value="<?php
                                    if ($_POST[obsluha] == "")
                                        echo "5.0";
                                    else
                                        echo $_POST[obsluha];
                                    ?>" step="0.1" oninput="obsluhavalue.value=value" /><output id="obsluhavalue" class="range_output"><?php
                                           if ($_POST[obsluha] == "")
                                               echo "5.0";
                                           else
                                               echo $_POST[obsluha];
                                           ?></output></dd>


                                <dt><label for="prostredi">Prostředí (0 - 10 bodů) *</label></dt>
                                <dd><input type="range" class="range" name="prostredi" id="prostredi" min="0.0" max="10.0" value="<?php
                                    if ($_POST[prostredi] == "")
                                        echo "5.0";
                                    else
                                        echo $_POST[prostredi];
                                    ?>" step="0.1" oninput="prostredivalue.value=value" /><output id="prostredivalue" class="range_output"><?php
                                           if ($_POST[prostredi] == "")
                                               echo "5.0";
                                           else
                                               echo $_POST[prostredi];
                                           ?></output></dd>

                                <div class="cara"> </div>

                                <dt><label for="textarea1">Recenze *</label></dt>
                                <dd><textarea id="textarea1" name="textarea1" placeholder="Vaše recenze" required><?php echo $_POST[textarea1]; ?></textarea></dd>


                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd></dd>


                                <dt><a href="./formulare.php?id=napisrecenzi&ad=<?php echo $_GET[ad]; ?>" class="button_zpet">Zpět</a></dt>
                                <dd><input type="submit" class="button" id="text-input-1-submit" value="Přidat podnik"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } else {
                    echo "<div class=\"msg err\"><h2>Přidávat podniky může jen registrovaný gurmán!</h2><p>Můžete se <a href=\"./formulare.php?id=prihlaseni\">přihlásit</a> nebo <a href=\"./formulare.php?id=registrace\">registrovat</a>.</p></div>";
                }
            }
            ?>
        </div>
    </body>
</html>        


