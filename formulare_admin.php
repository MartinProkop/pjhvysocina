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
            <?php
            if (login_check()) {
                if ($_GET[id] == "novaaktualita") {
                    echo "<h1>Nová aktualita</h1>";

                    $no_error_foto = true;

                    //$no_error_foto_aspon_neco = true;
                    $no_error_foto_aspon_neco = false;

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

                            if ($_POST[radio1] == "restaurace") {
                                $typ = "restaurace";
                                $id_podnik = $_POST[selectrestaurace];
                            } elseif ($_POST[radio1] == "kavarny") {
                                $typ = "kavarny";
                                $id_podnik = $_POST[selectkavarny];
                            }

                            if ($id_podnik != 0) {
                                $query_chci_id = dibi::query('SELECT * FROM [pjh_podnik] WHERE [id] = %i', $id_podnik);
                                $row_chci_id = $query_chci_id->fetch();
                            } else {
                                $row_chci_id['id'] = 0;
                            }

                            // 
                            $arr_akt = array('id_podnik' => $row_chci_id['id'], 'date' => time(), 'nazev' => $_POST['nazev'], 'sekce' => $typ, 'obsah' => $_POST['textarea1'], 'zverejnit' => 'ano');
                            dibi::query('INSERT INTO [pjh_aktuality]', $arr_akt);


                            if ($id_podnik != 0) {
                                $query_id_akt = dibi::query('SELECT * FROM [pjh_aktuality] WHERE [id_podnik] = %i', $id_podnik, 'ORDER BY %by', 'id', 'DESC');
                                $row_id_akt = $query_id_akt->fetch();
                            } else {
                                $row_id_akt['id'] = 0;
                            }

                            //foto
                            $query_upfoto = dibi::query('SELECT * FROM [pjh_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto', 'ORDER BY %by', 'id', 'ASC');
                            $i = 1;
                            while ($row_upfoto = $query_upfoto->fetch()) {
                                $newname = "./obrazky/" . $row_upfoto[value1];
                                $oldname = $row_upfoto[value2];
                                rename($oldname, $newname);

                                $arr_fotka = array('nazev' => $_POST['nazev'], 'cil' => $newname, 'typ' => "aktualita", 'id_cil' => $row_id_akt['id'], 'poradi' => $i, 'id_autor' => get_id_uzivatele($_SESSION['jmeno']), 'popis' => $_POST['nazev'], 'date' => time(), 'zverejnit' => "ano");
                                dibi::query('INSERT INTO [pjh_fotky]', $arr_fotka);

                                dibi::query('DELETE FROM [pjh_temp] WHERE [id] = %i', $row_upfoto[id]);

                                $i++;
                            }
                            echo "<div class=\"msg information\"><h2>Aktualita byla přidána!</h2></div>";
                            ?>
                            <script>

                                close_fancybox_redirect_parent('./administrace.php?id=aktuality', 3000);
                            </script>
                            <?php
                        }
                    }

                    if ($_GET[action] != "send" || $no_error_foto == false) {
                        ?>                  
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=novaaktualita&hash=<?php echo $_GET[hash]; ?>&action=send" enctype="multipart/form-data" >

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
                                    <input name="radio1" type="radio" id="radio-1" onchange="show_hide('selectrestaurace', 'selectkavarny')" value="restaurace" <?php echo $select_kategorie[restaurace]; ?> required><label for="radio-1">Restaurace</label><br />
                                    <input name="radio1" type="radio" id="radio-2" onchange="show_hide('selectkavarny', 'selectrestaurace')" value="kavarny" <?php echo $select_kategorie[kavarny]; ?>><label for="radio-2">Kavárna</label><br />
                                    <input name="radio1" type="radio" id="radio-3" disabled><label for="radio-3"><span class="non_active">Rychlé občerstvení</span></label>
                                </dd>      

                                <dt><label for="select">Podnik</label></dt>
                                <dd>
                                    <select id="selectrestaurace" name="selectrestaurace" class="select <?php echo $select_podnik[0]; ?>">
                                        <option value="0">--- žádný ---</option>
                                        <?php
                                        $query_restaurace = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", "restaurace", "AND [zverejnit] = %s", "ano", "ORDER BY %by", "nazev", "ASC");
                                        while ($row_restaurace = $query_restaurace->fetch()) {
                                            if ($_POST[radio1] == "restaurace" && $_POST[selectrestaurace] == $row_restaurace[id]) {
                                                echo "<option value=\"$row_restaurace[id]\" selected>$row_restaurace[nazev]</option>";
                                            } else {
                                                echo "<option value=\"$row_restaurace[id]\">$row_restaurace[nazev]</option>";
                                            }
                                        }
                                        ?>
                                    </select>

                                    <select id="selectkavarny" name="selectkavarny" class="select <?php echo $select_podnik[1]; ?>" required>
                                        <option value="0">--- žádný ---</option>
                                        <?php
                                        $query_kavarny = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", "kavarny", "AND [zverejnit] = %s", "ano", "ORDER BY %by", "nazev", "ASC");
                                        while ($row_kavarny = $query_kavarny->fetch()) {
                                            if ($_POST[radio1] == "kavarny" && $_POST[selectkavarny] == $row_kavarny[id]) {
                                                echo "<option value=\"$row_kavarny[id]\" selected>$row_kavarny[nazev]</option>";
                                            } else {
                                                echo "<option value=\"$row_kavarny[id]\">$row_kavarny[nazev]</option>";
                                            }
                                        }
                                        ?>
                                    </select>                                    
                                </dd>

                                <dt><label for="nazev">Název aktuality *</label></dt>
                                <dd><input type="text" class="big" id="nazev" name="nazev" value="<?php echo $_POST[nazev]; ?>" placeholder="Název aktuality" required></dd>                                

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
                                    <dt><label for="fileToUpload">Fotografie <?php if ($no_error_foto_aspon_neco) echo "*"; ?></label><br /><a href="#" onclick="add_more_foto()">přidat další fotografii</a></dt>
                                    <dd>
                                        <div id="addfoto">
                                            <input type="file" class="big" name="fileToUpload[]" id="fileToUpload" <?php if ($no_error_foto_aspon_neco) echo "required"; ?>>
                                        </div>
                                    </dd>                        
                                    <?php
                                }
                                ?>
                                <div class="cara"> </div>                             

                                <dt><label for="textarea1">Text *</label></dt>
                                <dd><textarea id="textarea1" name="textarea1" placeholder="Text aktuality" required><?php echo $_POST[textarea1]; ?></textarea></dd>

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="text-input-1-submit" value="Přidat aktualitu"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } if ($_GET[id] == "novareklama") {
                    echo "<h1>Nová reklama</h1>";

                    $no_error_foto = true;

                    //$no_error_foto_aspon_neco = true;
                    $no_error_foto_aspon_neco = false;

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

                            if ($_POST[radio1] == "restaurace") {
                                $typ = "restaurace";
                                $id_podnik = $_POST[selectrestaurace];
                            } elseif ($_POST[radio1] == "kavarny") {
                                $typ = "kavarny";
                                $id_podnik = $_POST[selectkavarny];
                            }

                            $query_chci_id = dibi::query('SELECT * FROM [pjh_podnik] WHERE [id] = %i', $id_podnik);
                            $row_chci_id = $query_chci_id->fetch();

                            //foto
                            $query_upfoto = dibi::query('SELECT * FROM [pjh_temp] WHERE [id_hash] = %i', $_GET[hash]);
                            $row_upfoto = $query_upfoto->fetch();
                            $newname = "./obrazky/" . $row_upfoto[value1];
                            $oldname = $row_upfoto[value2];
                            rename($oldname, $newname);
                            dibi::query('DELETE FROM [pjh_temp] WHERE [id] = %i', $row_upfoto[id]);

                            //banner do db
                            $arr_akt = array('id_podnik' => $row_chci_id['id'], 'nazev' => $_POST['nazev'], 'sekce' => $typ, 'zverejnit' => 'ano', 'cil' => $newname);
                            dibi::query('INSERT INTO [pjh_bannery]', $arr_akt);


                            echo "<div class=\"msg information\"><h2>Reklama byla přidána!</h2></div>";
                            ?>
                            <script>

                                close_fancybox_redirect_parent('./administrace.php?id=banner', 3000);
                            </script>
                            <?php
                        }
                    }

                    if ($_GET[action] != "send" || $no_error_foto == false) {
                        ?>                  
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=novareklama&hash=<?php echo $_GET[hash]; ?>&action=send" enctype="multipart/form-data" >

                            <dl class="inline">
                                <?php
                                if ($_GET[ad] == "restaurace" || $_POST[radio1] == "restaurace" || ($_GET[ad] == "" && $_POST[radio1] == "")) {
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
                                    <input name="radio1" type="radio" id="radio-1" onchange="show_hide('selectrestaurace', 'selectkavarny')" value="restaurace" <?php echo $select_kategorie[restaurace]; ?> required><label for="radio-1">Restaurace</label><br />
                                    <input name="radio1" type="radio" id="radio-2" onchange="show_hide('selectkavarny', 'selectrestaurace')" value="kavarny" <?php echo $select_kategorie[kavarny]; ?>><label for="radio-2">Kavárna</label><br />
                                    <input name="radio1" type="radio" id="radio-3" disabled><label for="radio-3"><span class="non_active">Rychlé občerstvení</span></label>
                                </dd>      

                                <dt><label for="select">Podnik</label></dt>
                                <dd>
                                    <select id="selectrestaurace" name="selectrestaurace" class="select <?php echo $select_podnik[0]; ?>">
                                        <?php
                                        $query_restaurace = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", "restaurace", "AND [zverejnit] = %s", "ano", "ORDER BY %by", "nazev", "ASC");
                                        while ($row_restaurace = $query_restaurace->fetch()) {
                                            if ($_POST[radio1] == "restaurace" && $_POST[selectrestaurace] == $row_restaurace[id]) {
                                                echo "<option value=\"$row_restaurace[id]\" selected>$row_restaurace[nazev]</option>";
                                            } else {
                                                echo "<option value=\"$row_restaurace[id]\">$row_restaurace[nazev]</option>";
                                            }
                                        }
                                        ?>
                                    </select>

                                    <select id="selectkavarny" name="selectkavarny" class="select <?php echo $select_podnik[1]; ?>" required>
                                        <?php
                                        $query_kavarny = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", "kavarny", "AND [zverejnit] = %s", "ano", "ORDER BY %by", "nazev", "ASC");
                                        while ($row_kavarny = $query_kavarny->fetch()) {
                                            if ($_POST[radio1] == "kavarny" && $_POST[selectkavarny] == $row_kavarny[id]) {
                                                echo "<option value=\"$row_kavarny[id]\" selected>$row_kavarny[nazev]</option>";
                                            } else {
                                                echo "<option value=\"$row_kavarny[id]\">$row_kavarny[nazev]</option>";
                                            }
                                        }
                                        ?>
                                    </select>                                    
                                </dd>

                                <dt><label for="nazev">Krátký popis reklamamy *</label></dt>
                                <dd><input type="text" class="big" id="nazev" name="nazev" value="<?php echo $_POST[nazev]; ?>" placeholder="Krátký popis reklamy (50 znak)" maxlength="50" required></dd>                                

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
                                    <dt><label for="fileToUpload">Fotografie <?php if ($no_error_foto_aspon_neco) echo "*"; ?></label></dt>
                                    <dd>
                                        <div id="addfoto">
                                            <input type="file" class="big" name="fileToUpload[]" id="fileToUpload" required>
                                        </div>
                                    </dd>                        
                                    <?php
                                }
                                ?>

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="text-input-1-submit" value="Přidat reklamu"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                }



                elseif ($_GET[id] == "schvalit") {
                    echo "<h1>Schválit podnik</h1>";
                    $ulozit = true;

                    if ($_GET[action] == "send") {
                        if ($_POST[submit1]) {
                            $arr_podnik = array('nazev' => $_POST['nazevpodniku'], 'adresa' => $_POST['adresa'], 'okres' => $_POST['okres'], 'obsah' => $_POST['textarea_popis'], 'typ' => $_POST['radio1'], 'zverejnit' => 'ano');
                            dibi::query('UPDATE [pjh_podnik] SET ', $arr_podnik, 'WHERE [id] = %i', $_GET[ad]);

                            $arr_rec = array('zverejnit' => 'ano', 'typ' => $_POST['radio1'], 'nazevpodnik' => get_nazev_podniku($_GET[ad]));
                            dibi::query('UPDATE [pjh_recenze] SET', $arr_rec, 'WHERE [id_podnik] = %i', $_GET[ad]);

                            $arr_fot = array('zverejnit' => 'ano');
                            dibi::query('UPDATE [pjh_fotky] SET', $arr_fot, ' WHERE [id_cil] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik');

                            $query_recenze = dibi::query('SELECT * FROM [pjh_recenze] WHERE [id_podnik] = %i', $_GET[ad]);
                            $row_recenze = $query_recenze->fetch();

                            $query_uzivat = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [id] = %i', $row_recenze[id_autor]);
                            $row_uzivat = $query_uzivat->fetch();

                            $arr_uzivat = array('hodnost' => $row_uzivat[hodnost] + 1);
                            dibi::query('UPDATE [pjh_uzivatele] SET', $arr_uzivat, ' WHERE [id] = %i', $row_recenze[id_autor]);

                            $arr_temp = array('id_cil' => $row_recenze[id_autor], 'date' => time(), 'obsah' => "<em>Schválili jsme přidání podniku <u>" . get_nazev_podniku($_GET[ad]) . "</u></em>, který jste navrh, do naší databáze.", 'stav' => "nova");
                            dibi::query('INSERT INTO [pjh_zpravy]', $arr_temp);

                            $body = "Dobrý den,\n\nprávě jsme schválili přidání podniku <u>" . get_nazev_podniku($_GET[ad]) . "</u></em>, který jste navrh, do naší databáze na serveru Pijem, jíme, hodnotíme.\n\nTěšíme se, že se s náma poldělité o Vaše kulinářské zážitky.\n\nS pozdravem\nPijem, jíme, hodnotíme.";
                            send_mail_kovar(get_email_uzivatele($row_recenze[id_autor]), "Přidaný podnik na serveru pjhvysocina.cz", $body);

                            echo "<div class=\"msg information\"><h2>Schválil jste přidání podniku včetně jeho první recenze!</h2></div>";
                            ?>
                            <script>
                                close_fancybox_redirect_parent('./administrace.php', 3000);
                            </script>
                            <?php
                        } elseif ($_POST[submit2] || $_GET[editfotek]) {
                            $ulozit = false;
                            echo "<h2>Spravovat fotografie</a>";
                            if ($_POST[submit2]) {
                                $arr_podnik = array('nazev' => $_POST['nazevpodniku'], 'adresa' => $_POST['adresa'], 'okres' => $_POST['okres'], 'obsah' => $_POST['textarea_popis'], 'typ' => $_POST['radio1']);
                                dibi::query('UPDATE [pjh_podnik] SET ', $arr_podnik, 'WHERE [id] = %i', $_GET[ad]);
                            } elseif ($_GET[editfotek] == "ano") {
                                if ($_GET['nahoru'])
                                    nahoru_fotky();
                                if ($_GET['dolu'])
                                    dolu_fotky();

                                if ($_GET['delete'] != "") {
                                    $result = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id] = %i', $_GET['delete']);
                                    $row = $result->fetch();
                                    if ($row[cil] != "./img/nofoto.png")
                                        unlink($row[cil]);
                                    dibi::query('DELETE FROM [pjh_fotky] WHERE [id] = %i', $_GET['delete']);
                                    $result = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik', 'ORDER BY %by', "poradi", "ASC");
                                    $i = 1;
                                    while ($row = $result->fetch()) {
                                        dibi::query('UPDATE [pjh_fotky] SET [poradi] = %i', $i, 'WHERE [id] = %i', $row['id']);
                                        $i++;
                                    }
                                }
                            }
                            echo "<dl class=\"inline\">";

                            $query_upfoto = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik', 'ORDER BY %by', 'poradi', 'ASC');
                            $pocet = $query_upfoto->count();
                            $iterace = 1;
                            if ($pocet > 0) {
                                echo "<p>Horní fotografie je profilová<p>";
                                while ($row_upfoto = $query_upfoto->fetch()) {
                                    echo "<dt>";
                                    if ($iterace == 1 && $pocet != 1) {
                                        echo "<a href=\"./formulare_admin.php?id=schvalit&ad=" . $_GET[ad] . "&action=send&editfotek=ano&dolu=" . $row_upfoto['id'] . "\">dolu</a>";
                                    } elseif ($iterace == $pocet && $pocet != 1) {
                                        echo "<a href=\"./formulare_admin.php?id=schvalit&ad=" . $_GET[ad] . "&action=send&editfotek=ano&nahoru=" . $row_upfoto['id'] . "\">nahoru</a>";
                                    } elseif ($pocet != 1) {
                                        echo "<a href=\"./formulare_admin.php?id=schvalit&ad=" . $_GET[ad] . "&action=send&editfotek=ano&nahoru=" . $row_upfoto['id'] . "\">nahoru</a> | <a href=\"./formulare_admin.php?id=schvalit&ad=" . $_GET[ad] . "&action=send&editfotek=ano&dolu=" . $row_upfoto['id'] . "\">dolu</a>";
                                    }
                                    echo " (<a href=\"./formulare_admin.php?id=schvalit&ad=" . $_GET[ad] . "&action=send&editfotek=ano&delete=" . $row_upfoto['id'] . "\" onclick=\"if(confirm('Skutečně smazat?')) location.href='./formulare_admin.php?id=schvalit&ad=" . $_GET[ad] . "&action=send&editfotek=ano&delete=" . $row_upfoto['id'] . "'; return(false);\">smazat</a>)";
                                    echo "</dt>";
                                    echo "<dd><img src=\"" . $row_upfoto[cil] . "\" height=\"100px\" class=\"img_nahrano\" /></dd>";
                                    $iterace++;
                                }
                            }
                            echo "<dt></dt><dd><a href=\"./formulare_admin.php?id=schvalit&ad=$_GET[ad]\" class=\"button\">Upraveno - pokračovat ve schvalování</a></dd>";
                            echo "</dl>";
                        }
                    }

                    if ($_GET[action] != "send") {
                        $query = dibi::query('SELECT * FROM [pjh_podnik] WHERE [id] = %i', $_GET[ad]);
                        $row = $query->fetch();
                        $query_recenze = dibi::query('SELECT * FROM [pjh_recenze] WHERE [id_podnik] = %i', $row[id]);
                        $row_recenze = $query_recenze->fetch();
                        ?>                  
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=schvalit&ad=<?php echo $_GET[ad]; ?>&action=send" enctype="multipart/form-data" >

                            <dl class="inline">
                                <?php
                                if ($row[typ] == "restaurace") {
                                    $select_kategorie[restaurace] = "checked";
                                } elseif ($row[typ] == "kavarny") {
                                    $select_kategorie[kavarny] = "checked";
                                }
                                ?>
                                <dt><label for="radio-1">Kategorie</label></dt>
                                <dd>
                                    <input name="radio1" type="radio" id="radio-1" value="restaurace" <?php echo $select_kategorie[restaurace]; ?> required><label for="radio-1">Restaurace</label><br />
                                    <input name="radio1" type="radio" id="radio-2" value="kavarny" <?php echo $select_kategorie[kavarny]; ?>><label for="radio-2">Kavárna</label><br />
                                    <input name="radio1" type="radio" id="radio-3" disabled><label for="radio-3"><span class="non_active">Rychlé občerstvení</span></label>
                                </dd>      

                                <dt><label for="nazevpodniku">Název podniku</label></dt>
                                <dd><input type="text" class="big" id="nazevpodniku" name="nazevpodniku" value="<?php echo $row[nazev]; ?>" placeholder="Název podniku" required></dd>                        

                                <dt><label for="adresa">Adresa podniku</label></dt>
                                <dd><input type="text" class="big" id="adresa" name="adresa" value="<?php echo $row[adresa]; ?>" placeholder="Adresa podniku" required></dd>

                                <?php
                                if ($row[okres] == "ZnS") {
                                    $select_okres[ZnS] = "selected";
                                } elseif ($row[okres] == "T") {
                                    $select_okres[T] = "selected";
                                } elseif ($row[okres] == "P") {
                                    $select_okres[P] = "selected";
                                } elseif ($row[okres] == "HB") {
                                    $select_okres[HB] = "selected";
                                } elseif ($row[okres] == "J") {
                                    $select_okres[J] = "selected";
                                }
                                ?>

                                <dt><label for="okres">Okres</label></dt>
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
                                <dd><textarea id="textarea_popis" name="textarea_popis" placeholder="Popis podniku" required><?php echo $row[obsah]; ?></textarea></dd>

                                <?php
                                $query_upfoto = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $row[id], 'AND [typ] = %s', 'podnik', 'ORDER BY %by', 'poradi', 'ASC');
                                if ($query_upfoto->count() > 0) {
                                    echo "<dt><label for=\"fileToUpload\">Nahrané fotografie</label><br /><input type=\"submit\" class=\"spravovat_fotografie\" name=\"submit2\" id=\"text-input-2-submit\" value=\"Spravovat fotografie\"></dt>"
                                    . "<dd>";
                                    while ($row_upfoto = $query_upfoto->fetch()) {
                                        echo "<img src=\"" . $row_upfoto[cil] . "\" height=\"100px\" class=\"img_nahrano\" />";
                                    }
                                    echo "</dd>";
                                }
                                ?>

                                <div class="cara"> </div>

                                <dt><label for="jidlo">Jídlo / Kávy (0 - 10 bodů)</label></dt>
                                <dd><input type="range" class="range" name="jidlo" id="jidlo" min="0.0" max="10.0" value="<?php echo $row[jidlo]; ?>" step="0.1" oninput="jidlovalue.value=value" disabled/><output id="jidlovalue" class="range_output"><?php echo $row[jidlo]; ?></output></dd>


                                <dt><label for="obsluha">Obsluha (0 - 10 bodů)</label></dt>
                                <dd><input type="range" class="range" name="obsluha" id="obsluha" min="0.0" max="10.0" value="<?php echo $row[obsluha]; ?>" step="0.1" oninput="obsluhavalue.value=value" disabled/><output id="obsluhavalue" class="range_output"><?php echo $row[obsluha]; ?></output></dd>


                                <dt><label for="prostredi">Prostředí (0 - 10 bodů)</label></dt>
                                <dd><input type="range" class="range" name="prostredi" id="prostredi" min="0.0" max="10.0" value="<?php echo $row[prostredi]; ?>" step="0.1" oninput="prostredivalue.value=value" disabled/><output id="prostredivalue" class="range_output"><?php echo $row[prostredi]; ?></output></dd>

                                <div class="cara"> </div>

                                <dt><label for="textarea1">Recenze</label></dt>
                                <dd><textarea id="textarea1" name="textarea1" placeholder="Vaše recenze" disabled required><?php echo $row_recenze[obsah]; ?></textarea></dd>

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd></dd>

                                <dt><a href="./formulare_admin.php?id=neschvalit&ad=<?php echo $_GET[ad]; ?>" class="button_zamitnout">Zamítnout podnik</a></dt>
                                <dd><input type="submit" class="button" name="submit1" id="text-input-1-submit" value="Schválit podnik"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } elseif ($_GET[id] == "neschvalit") {
                    echo "<h1>Zamítnout přidání podniku</h1>";
                    if ($_GET[action] == "send") {
                        $arr_rec = array('zverejnit' => 'smazat');
                        dibi::query('UPDATE [pjh_podnik] SET', $arr_rec, 'WHERE [id] = %i', $_GET[ad]);

                        $query_pod = dibi::query('SELECT * FROM [pjh_podnik] WHERE [id] = %i', $_GET[ad]);
                        $row_pod = $query_pod->fetch();


                        dibi::query('UPDATE [pjh_recenze] SET', $arr_rec, 'WHERE [id_podnik] = %i', $_GET[ad]);

                        $query_rec = dibi::query('SELECT * FROM [pjh_recenze] WHERE [id_podnik] = %i', $_GET[ad]);
                        $row_rec = $query_rec->fetch();

                        $arr_temp = array('id_cil' => $row_rec[id_autor], 'date' => time(), 'obsah' => "<em>Odmítli jsme přidat podnik <u>" . $row_rec[nazevpodnik] . "</u></em>, který jste navrh, a odůvodňujeme to takto:<br/>" . $_POST[textarea1], 'stav' => "nova");
                        dibi::query('INSERT INTO [pjh_zpravy]', $arr_temp);

                        $body = "Dobrý den,\n\nOdmítli jsme přidat podnik <u>" . $row_rec[nazevpodnik] . "</u></em>, který jste navrh, do naší databáze na serveru Pijem, jíme, hodnotíme. Oodůvodňujeme to takto:\n" . $_POST[textarea1] . " \n\nTěšíme se, že se s náma poldělité o Vaše kulinářské zážitky.\n\nS pozdravem\nPijem, jíme, hodnotíme.";
                        send_mail_kovar(get_email_uzivatele($row_rec[id_autor]), "Nepřijatý podnik na serveru pjhvysocina.cz", $body);


                        $query2 = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $_GET[ad], "AND [typ] = %s", "podnik");
                        while ($row2 = $query2->fetch()) {
                            if ($row2[cil] != "./img/nofoto.png")
                                unlink($row2[cil]);
                            dibi::query('DELETE FROM [pjh_fotky] WHERE [id] = %i', $row2[id]);
                        }

                        echo "<div class=\"msg information\"><h2>Přidání podniku bylo zamítnuto!</h2></div>";
                        ?>
                        <script>
                            close_fancybox_redirect_parent('./administrace.php', 3000);
                        </script>
                        <?php
                    }
                    if ($_GET[action] != "send") {
                        ?>                  
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=neschvalit&ad=<?php echo $_GET[ad]; ?>&action=send" enctype="multipart/form-data" >
                            <dl class="inline">
                                <dt><label for="textarea1">Odůvodnění zamítnutí *</label></dt>
                                <dd><textarea id="textarea1" name="textarea1" placeholder="Vaše odůvodnění se odešle autorovi recenze" required></textarea></dd>

                                <dt><input type="submit" class="button_zamitnout" name="submit1" id="text-input-1-submit" value="Zamítnout recenzi"></dt>
                                <dd><label for="">(*) označuje povinné položky</label></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } elseif ($_GET[id] == "schvalit_recenze") {
                    echo "<h1>Schválit recenzi</h1>";

                    if ($_GET[action] == "send") {
                        if ($_POST[submit1]) {
                            $arr_rec = array('zverejnit' => 'ano');
                            dibi::query('UPDATE [pjh_recenze] SET', $arr_rec, 'WHERE [id] = %i', $_GET[ad]);

                            $query_recenze = dibi::query('SELECT * FROM [pjh_recenze] WHERE [id] = %i', $_GET[ad]);
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

                            $query_fotky = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_recenze] = %i', $_GET[ad], 'ORDER BY %by', 'poradi', 'ASC');
                            while ($row_fotky = $query_fotky->fetch()) {
                                $arr_fot = array('zverejnit' => 'ano', 'poradi' => $i);
                                dibi::query('UPDATE [pjh_fotky] SET', $arr_fot, ' WHERE [id] = %i', $row_fotky[id]);
                                $i++;
                            }

                            $query_uzivat = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [id] = %i', $row_recenze[id_autor]);
                            $row_uzivat = $query_uzivat->fetch();

                            $arr_uzivat = array('hodnost' => $row_uzivat[hodnost] + 1);
                            dibi::query('UPDATE [pjh_uzivatele] SET', $arr_uzivat, ' WHERE [id] = %i', $row_recenze[id_autor]);

                            $arr_temp = array('id_cil' => $row_recenze[id_autor], 'date' => time(), 'obsah' => "<em>Schválili jsme přidání recenze podniku <u>" . get_nazev_podniku($_GET[ad]) . "</u></em>, kterou jste navrh, do naší databáze.", 'stav' => "nova");
                            dibi::query('INSERT INTO [pjh_zpravy]', $arr_temp);

                            $body = "Dobrý den,\n\nprávě jsme schválili přidání recenze podniku <u>" . get_nazev_podniku($_GET[ad]) . "</u></em>, kterou jste navrh, do naší databáze na serveru Pijem, jíme, hodnotíme.\n\nTěšíme se, že se s náma poldělité o Vaše kulinářské zážitky.\n\nS pozdravem\nPijem, jíme, hodnotíme.";
                            send_mail_kovar(get_email_uzivatele($row_recenze[id_autor]), "Přidaná recenze na serveru pjhvysocina.cz", $body);

                            echo "<div class=\"msg information\"><h2>Schválil jste přidání recenze!</h2></div>";
                            ?>
                            <script>
                                close_fancybox_redirect_parent('./administrace.php', 3000);
                            </script>
                            <?php
                        } elseif ($_POST[submit2] || $_GET[editfotek] == "ano") {
                            $ulozit = false;
                            echo "<h2>Spravovat fotografie</a>";
                            if ($_GET['nahoru'])
                                nahoru_fotky_recenze();
                            if ($_GET['dolu'])
                                dolu_fotky_recenze();

                            if ($_GET['delete'] != "") {
                                $result = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id] = %i', $_GET['delete']);
                                $row = $result->fetch();
                                if ($row[cil] != "./img/nofoto.png")
                                    unlink($row[cil]);
                                dibi::query('DELETE FROM [pjh_fotky] WHERE [id] = %i', $_GET['delete']);
                                $result = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_recenze] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik', 'ORDER BY %by', "poradi", "ASC");
                                $i = 1;
                                while ($row = $result->fetch()) {
                                    dibi::query('UPDATE [pjh_fotky] SET [poradi] = %i', $i, 'WHERE [id] = %i', $row['id']);
                                    $i++;
                                }
                            }

                            echo "<dl class=\"inline\">";

                            $query_upfoto = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_recenze] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik', 'ORDER BY %by', 'poradi', 'ASC');
                            $pocet = $query_upfoto->count();
                            $iterace = 1;
                            if ($pocet > 0) {
                                echo "<p>Horní fotografie je profilová<p>";
                                while ($row_upfoto = $query_upfoto->fetch()) {
                                    echo "<dt>";
                                    if ($iterace == 1 && $pocet != 1) {
                                        echo "<a href=\"./formulare_admin.php?id=schvalit_recenze&ad=" . $_GET[ad] . "&action=send&editfotek=ano&dolu=" . $row_upfoto['id'] . "\">dolu</a>";
                                    } elseif ($iterace == $pocet && $pocet != 1) {
                                        echo "<a href=\"./formulare_admin.php?id=schvalit_recenze&ad=" . $_GET[ad] . "&action=send&editfotek=ano&nahoru=" . $row_upfoto['id'] . "\">nahoru</a>";
                                    } elseif ($pocet != 1) {
                                        echo "<a href=\"./formulare_admin.php?id=schvalit_recenze&ad=" . $_GET[ad] . "&action=send&editfotek=ano&nahoru=" . $row_upfoto['id'] . "\">nahoru</a> | <a href=\"./formulare_admin.php?id=schvalit_recenze&ad=" . $_GET[ad] . "&action=send&editfotek=ano&dolu=" . $row_upfoto['id'] . "\">dolu</a>";
                                    }
                                    echo " (<a href=\"./formulare_admin.php?id=schvalit_recenze&ad=" . $_GET[ad] . "&action=send&editfotek=ano&delete=" . $row_upfoto['id'] . "\" onclick=\"if(confirm('Skutečně smazat?')) location.href='./formulare_admin.php?id=schvalit_recenze&ad=" . $_GET[ad] . "&action=send&editfotek=ano&delete=" . $row_upfoto['id'] . "'; return(false);\">smazat</a>)";
                                    echo "</dt>";
                                    echo "<dd><img src=\"" . $row_upfoto[cil] . "\" height=\"100px\" class=\"img_nahrano\" /></dd>";
                                    $iterace++;
                                }
                            }
                            echo "<dt></dt><dd><a href=\"./formulare_admin.php?id=schvalit_recenze&ad=$_GET[ad]\" class=\"button\">Upraveno - pokračovat ve schvalování</a></dd>";
                            echo "</dl>";
                        }
                    }

                    if ($_GET[action] != "send") {

                        $query_recenze = dibi::query('SELECT * FROM [pjh_recenze] WHERE [id] = %i', $_GET[ad]);
                        $row_recenze = $query_recenze->fetch();

                        $query = dibi::query('SELECT * FROM [pjh_podnik] WHERE [id] = %i', $row_recenze[id_podnik]);
                        $row = $query->fetch();
                        ?>                  
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=schvalit_recenze&ad=<?php echo $_GET[ad]; ?>&action=send" enctype="multipart/form-data" >

                            <dl class="inline">
                                <?php
                                if ($row[typ] == "restaurace") {
                                    $select_kategorie[restaurace] = "checked";
                                } elseif ($row[typ] == "kavarny") {
                                    $select_kategorie[kavarny] = "checked";
                                }
                                ?>
                                <dt><label for="radio-1">Kategorie</label></dt>
                                <dd>
                                    <input name="radio1" type="radio" id="radio-1" value="restaurace" <?php echo $select_kategorie[restaurace]; ?> required disabled><label for="radio-1">Restaurace</label><br />
                                    <input name="radio1" type="radio" id="radio-2" value="kavarny" <?php echo $select_kategorie[kavarny]; ?> disabled><label for="radio-2">Kavárna</label><br />
                                    <input name="radio1" type="radio" id="radio-3" disabled><label for="radio-3"><span class="non_active">Rychlé občerstvení</span></label>
                                </dd>      

                                <dt><label for="nazevpodniku">Název podniku</label></dt>
                                <dd><input type="text" class="big" id="nazevpodniku" name="nazevpodniku" value="<?php echo $row[nazev]; ?>" placeholder="Název podniku" disabled required></dd>                        

                                <?php
                                $query_upfoto = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $row[id], 'AND [typ] = %s', 'podnik', 'AND [id_recenze] = %i', $row_recenze[id], 'ORDER BY %by', 'id', 'ASC');
                                if ($query_upfoto->count() > 0) {
                                    echo "<dt><label for=\"fileToUpload\">Nahrané fotografie</label><br /><input type=\"submit\" class=\"spravovat_fotografie\" name=\"submit2\" id=\"text-input-2-submit\" value=\"Spravovat fotografie\"></dt>"
                                    . "<dd>";
                                    while ($row_upfoto = $query_upfoto->fetch()) {
                                        echo "<img src=\"" . $row_upfoto[cil] . "\" height=\"100px\" class=\"img_nahrano\" />";
                                    }
                                    echo "</dd>";
                                }
                                ?>

                                <div class="cara"> </div>

                                <dt><label for="jidlo">Jídlo / Kávy (0 - 10 bodů)</label></dt>
                                <dd><input type="range" class="range" name="jidlo" id="jidlo" min="0.0" max="10.0" value="<?php echo $row_recenze[jidlo]; ?>" step="0.1" oninput="jidlovalue.value=value" disabled/><output id="jidlovalue" class="range_output"><?php echo $row_recenze[jidlo]; ?></output></dd>


                                <dt><label for="obsluha">Obsluha (0 - 10 bodů)</label></dt>
                                <dd><input type="range" class="range" name="obsluha" id="obsluha" min="0.0" max="10.0" value="<?php echo $row_recenze[obsluha]; ?>" step="0.1" oninput="obsluhavalue.value=value" disabled/><output id="obsluhavalue" class="range_output"><?php echo $row_recenze[obsluha]; ?></output></dd>


                                <dt><label for="prostredi">Prostředí (0 - 10 bodů)</label></dt>
                                <dd><input type="range" class="range" name="prostredi" id="prostredi" min="0.0" max="10.0" value="<?php echo $row_recenze[prostredi]; ?>" step="0.1" oninput="prostredivalue.value=value" disabled/><output id="prostredivalue" class="range_output"><?php echo $row_recenze[prostredi]; ?></output></dd>

                                <div class="cara"> </div>

                                <dt><label for="textarea1">Recenze</label></dt>
                                <dd><textarea id="textarea1" name="textarea1" placeholder="Vaše recenze" disabled required><?php echo $row_recenze[obsah]; ?></textarea></dd>

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd></dd>

                                <dt><a href="./formulare_admin.php?id=neschvalit_recenze&ad=<?php echo $_GET[ad]; ?>" class="button_zamitnout">Zamítnout recenzi</a></dt>
                                <dd><input type="submit" class="button" name="submit1" id="text-input-1-submit" value="Schválit recenzi"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } elseif ($_GET[id] == "neschvalit_recenze") {
                    echo "<h1>Zamítnout recenzi</h1>";
                    if ($_GET[action] == "send") {
                        $arr_rec = array('zverejnit' => 'smazat');
                        dibi::query('UPDATE [pjh_recenze] SET', $arr_rec, 'WHERE [id] = %i', $_GET[ad]);

                        $query2 = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_recenze] = %i', $_GET[ad]);
                        while ($row2 = $query2->fetch()) {
                            if ($row2[cil] != "./img/nofoto.png")
                                unlink($row2[cil]);
                            dibi::query('DELETE FROM [pjh_fotky] WHERE [id] = %i', $row2[id]);
                        }

                        $query_recenze = dibi::query('SELECT * FROM [pjh_recenze] WHERE [id] = %i', $_GET[ad]);
                        $row_recenze = $query_recenze->fetch();

                        $arr_temp = array('id_cil' => $row_recenze[id_autor], 'date' => time(), 'obsah' => "<em>Odmítli jsme Vaší recenzi na podnik <u>" . $row_recenze[nazevpodnik] . "</u></em> a odůvodňujeme to takto:<br/>" . $_POST[textarea1], 'stav' => "nova");
                        dibi::query('INSERT INTO [pjh_zpravy]', $arr_temp);

                        $body = "Dobrý den,\n\nOdmítli jsme přidat recenzi podniku <u>" . $row_rec[nazevpodnik] . "</u></em>, kterou jste navrh, do naší databáze na serveru Pijem, jíme, hodnotíme. Oodůvodňujeme to takto:\n" . $_POST[textarea1] . " \n\nTěšíme se, že se s náma poldělité o Vaše kulinářské zážitky.\n\nS pozdravem\nPijem, jíme, hodnotíme.";
                        send_mail_kovar(get_email_uzivatele($row_recenze[id_autor]), "Odmítnutá recene na serveru pjhvysocina.cz", $body);

                        echo "<div class=\"msg information\"><h2>Recenze byla zamítnuta!</h2></div>";
                        ?>
                        <script>
                            close_fancybox_redirect_parent('./administrace.php', 3000);
                        </script>
                        <?php
                    }
                    if ($_GET[action] != "send") {
                        ?>                  
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=neschvalit_recenze&ad=<?php echo $_GET[ad]; ?>&action=send" enctype="multipart/form-data" >
                            <dl class="inline">
                                <dt><label for="textarea1">Odůvodnění zamítnutí *</label></dt>
                                <dd><textarea id="textarea1" name="textarea1" placeholder="Vaše odůvodnění se odešle autorovi recenze" required></textarea></dd>

                                <dt><input type="submit" class="button_zamitnout" id="text-input-1-submit" value="Zamítnout recenzi"></dt>
                                <dd><label for="">(*) označuje povinné položky</label></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } if ($_GET[id] == "doporucit") {
                    echo "<h1>Doporučit podnik</h1>";
                    if ($_GET[action] == "send") {
                        $arr_rec = array('doporucujeme' => "ano", 'date_doporuceno' => time(), "naserecenze" => $_POST[textarea1]);
                        dibi::query('UPDATE [pjh_podnik] SET', $arr_rec, 'WHERE [id] = %i', $_GET[ad]);
                        echo "<div class=\"msg information\"><h2>Podnik byl doporučen!</h2></div>";
                        ?>
                        <script>
                            close_fancybox_redirect_parent('./administrace.php?id=podniky&ad=<?php echo $_GET[sekce]; ?>', 3000);
                        </script>
                        <?php
                    }
                    if ($_GET[action] != "send") {
                        ?>                  
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=doporucit&ad=<?php echo $_GET[ad]; ?>&action=send&sekce=<?php echo $_GET[sekce]; ?>" enctype="multipart/form-data" >
                            <dl class="inline">
                                <dt><label for="textarea1">Text doporučení *</label></dt>
                                <dd><textarea id="textarea1" name="textarea1" placeholder="Napište text doporučení" required></textarea></dd>

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="text-input-1-submit" value="Doporučit podnik"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } elseif ($_GET[id] == "upravit") {
                    echo "<h1>Upravit podnik</h1>";
                    $ulozit = true;

                    if ($_GET[action] == "send") {
                        if ($_POST[submit1]) {
                            $arr_podnik = array('nazev' => $_POST['nazevpodniku'], 'adresa' => $_POST['adresa'], 'okres' => $_POST['okres'], 'obsah' => $_POST['textarea_popis'], 'typ' => $_POST['radio1'], 'zverejnit' => 'ano');
                            dibi::query('UPDATE [pjh_podnik] SET ', $arr_podnik, 'WHERE [id] = %i', $_GET[ad]);

                            echo "<div class=\"msg information\"><h2>Podnik upraven!</h2></div>";
                            ?>
                            <script>
                                close_fancybox_redirect_parent('./administrace.php?id=podniky&ad=<?php echo $_GET['sekce']; ?>', 3000);
                            </script>
                            <?php
                        } elseif ($_POST[submit3] || $_POST[submit2] || $_GET[editfotek]) {
                            if (count($_FILES['fileToUpload']['name']) > 0 && $_FILES['fileToUpload']['name'][0] != "") {
                                for ($i = 0; $i < count($_FILES['fileToUpload']['name']); $i++) {
                                    if ($_FILES["fileToUpload"]["size"][$i] > 1000000) {
                                        echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - je příliš velká!</h2><p>Zřejmě je příliž veliká - maximální velikost je 1 MB.</p></div>";
                                    } else {
                                        $timestamp = time();
                                        $nazev_souboru_tmp = $_FILES['fileToUpload']['tmp_name'][$i];
                                        $nazev_souboru = $timestamp . "_" . $_FILES['fileToUpload']['name'][$i];
                                        $cil = "./obrazky/" . $timestamp . "_" . $_FILES['fileToUpload']['name'][$i];
                                        $imageFileType = pathinfo($cil, PATHINFO_EXTENSION);
                                        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                                            echo "<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii - nepovolený formát!</h2><p>Povolené formáty jsou JPG, PNG, JPEG, GIF</p></div>";
                                        } else {
                                            move_uploaded_file($nazev_souboru_tmp, $cil) or die("<div class=\"msg err\"><h2>Nepovedlo se nahrát fotografii!</h2><p>Chyba je na straně serveru, nahašte jí prosím správci.</p></div>");

                                            $query_fotky = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik', "AND [zverejnit] = %s", "ano");
                                            $pocet_fotek = $query_fotky->count();
                                            $x = $pocet_fotek + 1;

                                            $arr_fotka = array('nazev' => get_nazev_podniku($_GET[ad]), 'cil' => $cil, 'typ' => "podnik", 'id_cil' => $_GET[ad], 'poradi' => $x, 'id_autor' => get_id_uzivatele($_SESSION['jmeno']), 'popis' => get_nazev_podniku($_GET[ad]), 'date' => time(), 'zverejnit' => "ano");
                                            dibi::query('INSERT INTO [pjh_fotky]', $arr_fotka);
                                        }
                                    }
                                }
                            }

                            echo "<h2>Spravovat fotografie</a>";
                            if ($_POST[submit2]) {
                                $arr_podnik = array('nazev' => $_POST['nazevpodniku'], 'adresa' => $_POST['adresa'], 'okres' => $_POST['okres'], 'obsah' => $_POST['textarea_popis'], 'typ' => $_POST['radio1']);
                                dibi::query('UPDATE [pjh_podnik] SET ', $arr_podnik, 'WHERE [id] = %i', $_GET[ad]);
                            } elseif ($_GET[editfotek] == "ano" || $_POST[submit3]) {
                                if ($_GET['nahoru'])
                                    nahoru_fotky();
                                if ($_GET['dolu'])
                                    dolu_fotky();

                                if ($_GET['delete'] != "") {
                                    $result = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id] = %i', $_GET['delete']);
                                    $row = $result->fetch();
                                    if ($row[cil] != "./img/nofoto.png")
                                        unlink($row[cil]);
                                    dibi::query('DELETE FROM [pjh_fotky] WHERE [id] = %i', $_GET['delete']);
                                    $result = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik', "AND [zverejnit] = %s", "ano", 'ORDER BY %by', "poradi", "ASC");
                                    $i = 1;
                                    while ($row = $result->fetch()) {
                                        dibi::query('UPDATE [pjh_fotky] SET [poradi] = %i', $i, 'WHERE [id] = %i', $row['id']);
                                        $i++;
                                    }
                                }
                            }
                            echo "<dl class=\"inline\">";
                            ?>
                            <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>

                            <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=upravit&ad=<?php echo $_GET[ad]; ?>&action=send&sekce=<?php echo $_GET[sekce]; ?>" enctype="multipart/form-data" >
                                <dt><label for="fileToUpload">Přidat fotografie</label><br /><a href="#" onclick="add_more_foto()">přidat další fotografii</a></dt>
                                <dd>
                                    <div id="addfoto">
                                        <input type="file" class="big" name="fileToUpload[]" id="fileToUpload" required>
                                    </div>
                                </dd>    

                                <dt></dt>
                                <dd><input type="submit" class="button" name="submit3" id="submit3" value="Nahrát fotky"></dd>

                            </form>    
                            <?php
                            $query_upfoto = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik', 'AND [zverejnit] = %s', 'ano', 'ORDER BY %by', 'poradi', 'ASC');
                            $pocet = $query_upfoto->count();
                            $iterace = 1;
                            if ($pocet > 0) {
                                echo "<p>Horní fotografie je profilová<p>";
                                while ($row_upfoto = $query_upfoto->fetch()) {
                                    echo "<dt><a name=\"lnk$iterace\"></a>";
                                    if ($iterace == 1 && $pocet != 1) {
                                        echo "<a href=\"./formulare_admin.php?id=upravit&ad=" . $_GET[ad] . "&action=send&sekce=" . $_GET['sekce'] . "&editfotek=ano&dolu=" . $row_upfoto['id'] . "#lnk$iterace\">dolu</a>";
                                    } elseif ($iterace == $pocet && $pocet != 1) {
                                        echo "<a href=\"./formulare_admin.php?id=upravit&ad=" . $_GET[ad] . "&action=send&sekce=" . $_GET['sekce'] . "&editfotek=ano&nahoru=" . $row_upfoto['id'] . "#lnk$iterace\">nahoru</a>";
                                    } elseif ($pocet != 1) {
                                        echo "<a href=\"./formulare_admin.php?id=upravit&ad=" . $_GET[ad] . "&action=send&sekce=" . $_GET['sekce'] . "&editfotek=ano&nahoru=" . $row_upfoto['id'] . "#lnk$iterace\">nahoru</a> | <a href=\"./formulare_admin.php?id=upravit&ad=" . $_GET[ad] . "&action=send&sekce=" . $_GET['sekce'] . "&editfotek=ano&dolu=" . $row_upfoto['id'] . "#lnk$iterace\">dolu</a>";
                                    }
                                    echo " (<a href=\"./formulare_admin.php?id=upravit&ad=" . $_GET[ad] . "&action=send&sekce=" . $_GET['sekce'] . "&editfotek=ano&delete=" . $row_upfoto['id'] . "\" onclick=\"if(confirm('Skutečně smazat?')) location.href='./formulare_admin.php?id=upravit&ad=" . $_GET[ad] . "&action=send&sekce=" . $_GET['sekce'] . "&editfotek=ano&delete=" . $row_upfoto['id'] . "'; return(false);\">smazat</a>)";
                                    echo "</dt>";
                                    echo "<dd><img src=\"" . $row_upfoto[cil] . "\" height=\"100px\" class=\"img_nahrano\" /></dd>";
                                    $iterace++;
                                }
                            }
                                echo "<br />";
                            echo "<dt></dt><dd><a href=\"./formulare_admin.php?id=upravit&ad=$_GET[ad]&sekce=" . $_GET['sekce'] . "\" class=\"button\">Zpět do úpravav podniku</a></dd>";
                            echo "</dl>";
                        }
                    }

                    if ($_GET[action] != "send") {
                        $query = dibi::query('SELECT * FROM [pjh_podnik] WHERE [id] = %i', $_GET[ad]);
                        $row = $query->fetch();
                        $query_recenze = dibi::query('SELECT * FROM [pjh_recenze] WHERE [id_podnik] = %i', $row[id]);
                        $row_recenze = $query_recenze->fetch();
                        ?>                  
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=upravit&ad=<?php echo $_GET[ad]; ?>&action=send&sekce=<?php echo $_GET[sekce]; ?>" enctype="multipart/form-data" >

                            <dl class="inline">
                                <?php
                                if ($row[typ] == "restaurace") {
                                    $select_kategorie[restaurace] = "checked";
                                } elseif ($row[typ] == "kavarny") {
                                    $select_kategorie[kavarny] = "checked";
                                }
                                ?>
                                <dt><label for="radio-1">Kategorie</label></dt>
                                <dd>
                                    <input name="radio1" type="radio" id="radio-1" value="restaurace" <?php echo $select_kategorie[restaurace]; ?> required><label for="radio-1">Restaurace</label><br />
                                    <input name="radio1" type="radio" id="radio-2" value="kavarny" <?php echo $select_kategorie[kavarny]; ?>><label for="radio-2">Kavárna</label><br />
                                    <input name="radio1" type="radio" id="radio-3" disabled><label for="radio-3"><span class="non_active">Rychlé občerstvení</span></label>
                                </dd>      

                                <dt><label for="nazevpodniku">Název podniku</label></dt>
                                <dd><input type="text" class="big" id="nazevpodniku" name="nazevpodniku" value="<?php echo $row[nazev]; ?>" placeholder="Název podniku" required></dd>                        

                                <dt><label for="adresa">Adresa podniku</label></dt>
                                <dd><input type="text" class="big" id="adresa" name="adresa" value="<?php echo $row[adresa]; ?>" placeholder="Adresa podniku" required></dd>

                                <?php
                                if ($row[okres] == "ZnS") {
                                    $select_okres[ZnS] = "selected";
                                } elseif ($row[okres] == "T") {
                                    $select_okres[T] = "selected";
                                } elseif ($row[okres] == "P") {
                                    $select_okres[P] = "selected";
                                } elseif ($row[okres] == "HB") {
                                    $select_okres[HB] = "selected";
                                } elseif ($row[okres] == "J") {
                                    $select_okres[J] = "selected";
                                }
                                ?>

                                <dt><label for="okres">Okres</label></dt>
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
                                <dd><textarea id="textarea_popis" name="textarea_popis" placeholder="Popis podniku" required><?php echo $row[obsah]; ?></textarea></dd>

                                <?php
                                $query_upfoto = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $row[id], 'AND [typ] = %s', 'podnik', 'AND [zverejnit] = %s', 'ano', 'ORDER BY %by', 'poradi', 'ASC');
                                if ($query_upfoto->count() > 0) {
                                    echo "<dt><label for=\"fileToUpload\">Nahrané fotografie</label><br /><input type=\"submit\" class=\"spravovat_fotografie\" name=\"submit2\" id=\"text-input-2-submit\" value=\"Spravovat fotografie\"></dt>"
                                    . "<dd>";
                                    while ($row_upfoto = $query_upfoto->fetch()) {
                                        echo "<img src=\"" . $row_upfoto[cil] . "\" height=\"100px\" class=\"img_nahrano\" />";
                                    }
                                    echo "</dd>";
                                } else {
                                    echo "<dt><label for=\"fileToUpload\">Nahrané fotografie</label><br /><input type=\"submit\" class=\"spravovat_fotografie\" name=\"submit2\" id=\"text-input-2-submit\" value=\"Spravovat fotografie\"></dt>";
                                    echo "<dd></dd>";
                                }
                                ?>

                                <div class="cara"> </div>

                                <dt><label for="jidlo">Jídlo / Kávy (0 - 10 bodů)</label></dt>
                                <dd><input type="range" class="range" name="jidlo" id="jidlo" min="0.0" max="10.0" value="<?php echo $row[jidlo]; ?>" step="0.1" oninput="jidlovalue.value=value" disabled/><output id="jidlovalue" class="range_output"><?php echo $row[jidlo]; ?></output></dd>


                                <dt><label for="obsluha">Obsluha (0 - 10 bodů)</label></dt>
                                <dd><input type="range" class="range" name="obsluha" id="obsluha" min="0.0" max="10.0" value="<?php echo $row[obsluha]; ?>" step="0.1" oninput="obsluhavalue.value=value" disabled/><output id="obsluhavalue" class="range_output"><?php echo $row[obsluha]; ?></output></dd>


                                <dt><label for="prostredi">Prostředí (0 - 10 bodů)</label></dt>
                                <dd><input type="range" class="range" name="prostredi" id="prostredi" min="0.0" max="10.0" value="<?php echo $row[prostredi]; ?>" step="0.1" oninput="prostredivalue.value=value" disabled/><output id="prostredivalue" class="range_output"><?php echo $row[prostredi]; ?></output></dd>

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd></dd>

                                <dt></dt>
                                <dd><input type="submit" class="button" name="submit1" id="text-input-1-submit" value="Uložit úpravy"></dd>
                            </dl>
                        </form>
                        <?php
                    }
                } elseif ($_GET[id] == "novypodnik") {
                    echo "<h1>Nový podnik</h1>";

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
                            $hodnoceni = 0;

                            $arr_podnik = array('nazev' => $_POST['nazevpodniku'], 'adresa' => $_POST['adresa'], 'okres' => $_POST['okres'], 'obsah' => $_POST['textarea_popis'], 'date' => time(), 'typ' => $_POST['radio1'], 'jidlo' => 0, 'obsluha' => 0, 'prostredi' => 0, 'hodnoceni' => $hodnoceni, 'vlozil' => $_SESSION['jmeno'], 'zverejnit' => 'ano');
                            dibi::query('INSERT INTO [pjh_podnik]', $arr_podnik);

                            $query_chci_id = dibi::query('SELECT * FROM [pjh_podnik] WHERE [nazev] = %s', $_POST[nazevpodniku]);
                            $row_chci_id = $query_chci_id->fetch();

                            // recenze
                            //foto
                            $query_upfoto = dibi::query('SELECT * FROM [pjh_temp] WHERE [id_hash] = %i', $_GET[hash], 'AND [znacka] = %s', 'novefoto', 'ORDER BY %by', 'id', 'ASC');
                            $i = 1;
                            while ($row_upfoto = $query_upfoto->fetch()) {
                                $newname = "./obrazky/" . $row_upfoto[value1];
                                $oldname = $row_upfoto[value2];
                                rename($oldname, $newname);

                                $arr_fotka = array('nazev' => $_POST['nazevpodniku'], 'cil' => $newname, 'typ' => "podnik", 'id_cil' => $row_chci_id['id'], 'poradi' => $i, 'id_autor' => get_id_uzivatele($_SESSION['jmeno']), 'popis' => $_POST['nazevpodniku'], 'date' => time(), 'zverejnit' => "ano");
                                dibi::query('INSERT INTO [pjh_fotky]', $arr_fotka);

                                dibi::query('DELETE FROM [pjh_temp] WHERE [id] = %i', $row_upfoto[id]);

                                $i++;
                            }

                            if ($no_error_foto_aspon_neco) {
                                //$no_error_foto = false;
                                $arr_fotka = array('nazev' => $_POST['nazevpodniku'], 'cil' => "./img/nofoto.png", 'typ' => "podnik", 'id_cil' => $row_chci_id['id'], 'poradi' => $i, 'id_autor' => get_id_uzivatele($_SESSION['jmeno']), 'popis' => $_POST['nazevpodniku'], 'date' => time(), 'zverejnit' => "ano");
                                dibi::query('INSERT INTO [pjh_fotky]', $arr_fotka);
                            }

                            echo "<div class=\"msg information\"><h2>Podnik s názvem '" . $_POST[nazevpodniku] . "' byl úspěšně přidán.</h2></div>";
                            echo "                    <script>
                        close_fancybox_redirect_parent('./administrace.php?id=podniky&ad=$_GET[ad]', 3000);
                    </script>";
                        }
                    }

                    if ($_GET[action] != "send" || $no_error_foto == false || $no_error_jmeno == false) {
                        ?>                
                        <div id="load" style="display:none;"><h2>Odesílám formulář... vyčkejte.</h2> <img src="./img/loading.gif"></div>
                        <form id="formul" name="formul" method="post" action="./formulare_admin.php?id=novypodnik&ad=<?php echo $_GET[ad]; ?>&hash=<?php echo $_GET[hash]; ?>&action=send" enctype="multipart/form-data" >

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

                                <dt><label for="">(*) označuje povinné položky</label></dt>
                                <dd><input type="submit" class="button" id="text-input-1-submit" value="Přidat podnik"></dd>
                            </dl>
                        </form>
            <?php
        }
    }
} else {
    ?>
                <div class="msg err"><h2>Nemáš tu co dělat! Nejsi admin!</h2><p>Jdi na stránky <a href="./index.php">Pijem, jíme, hodnotíme</a>!</p></div>
                <?php
            }
            ?>
        </div>
    </body>
</html>        


