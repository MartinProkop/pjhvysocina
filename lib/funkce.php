<?php

function format_size($cesta, $round = 3) {
    //Size must be bytes!
    $size = filesize($cesta);
    $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    for ($i = 0; $size > 1024 && $i < count($sizes) - 1; $i++)
        $size /= 1024;
    return round($size, $round) . " " . $sizes[$i];
}

function echo_date($timestamp) {
    echo Date("d. m. Y", $timestamp);
}

function generateRandomString($length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function get_foto_of_user($id) {
    $query = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $id, 'AND [typ] = %s', 'profilova');
    $row = $query->fetch();
    
    if ($row[cil] == "") return "./img/nofoto.png";
    else return $row[cil];
}

function get_foto_of_podnik($id) {
    $query = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $id, 'AND [typ] = %s', 'podnik', 'AND [zverejnit] = %s', 'ano', 'AND [poradi] = %i', '1');
    $row = $query->fetch();

    return $row[cil];
}

function get_id_uzivatele($jmeno) {
    $query = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [jmeno] = %s', $jmeno);
    $row = $query->fetch();

    return $row[id];
}

function get_email_uzivatele($id) {
    $query = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [id] = %i', $id);
    $row = $query->fetch();

    return $row[email];
}

function get_nazev_podniku($id) {
    $query = dibi::query('SELECT * FROM [pjh_podnik] WHERE [id] = %i', $id);
    $row = $query->fetch();

    return $row[nazev];
}

function get_id_autora_podniku($id) {
    $query = dibi::query('SELECT * FROM [pjh_podnik] WHERE [id] = %i', $id);
    $row = $query->fetch();

    $query2 = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [jmeno] = %s', $row[vlozil]);
    $row2 = $query2->fetch();


    return $row2[id];
}

function echo_hodnost($cislo) {
    if ($cislo >= 100)
        return "10. úroveň: Gurmán Vysočiny";
    else if ($cislo >= 90)
        return "9. úroveň: Gurmán Vysočiny";
    elseif ($cislo >= 80)
        return "8. úroveň: Gurmán";
    elseif ($cislo >= 70)
        return "7. úroveň: Gurmán";
    elseif ($cislo >= 60)
        return "6. úroveň: Mlsný jazýček";
    elseif ($cislo >= 50)
        return "5. úroveň: Mlsný jazýček";
    elseif ($cislo >= 40)
        return "4. úroveň: Labužník";
    elseif ($cislo >= 30)
        return "3. úroveň: Labužník";
    elseif ($cislo >= 20)
        return "2. úroveň: Nováček";
    else
        return "1. úroveň: Nováček";
}

function send_msg_to_admins($zprava) {
    $query_chci_id = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [admin] = %s', "ano");
    while ($row_chci_id = $query_chci_id->fetch()) {
        $arr_temp = array('id_cil' => $row_chci_id[id], 'date' => time(), 'obsah' => $zprava, 'stav' => "nova");
        dibi::query('INSERT INTO [pjh_zpravy]', $arr_temp);
    }
}

function echo_pocet_novych_zprav() {
    $query = dibi::query('SELECT * FROM [pjh_zpravy] WHERE [id_cil] = %s', get_id_uzivatele($_SESSION[jmeno]), 'AND [stav] = %s', 'nova');
    if ($query->count() > 0)
        return "<span class=\"varovani\">" . $query->count() . "</span>";
}

function get_hveznicky_na_cislo($cislo) {
    if ($cislo < 0.5) {
        return "./img/stars/0-stars.png";
    } elseif ($cislo < 1.5) {
        return "./img/stars/0-5-stars.png";
    } elseif ($cislo < 2.5) {
        return "./img/stars/1-stars.png";
    } elseif ($cislo < 3.5) {
        return "./img/stars/1-5-stars.png";
    } elseif ($cislo < 4.5) {
        return "./img/stars/2-stars.png";
    } elseif ($cislo < 5.5) {
        return "./img/stars/2-5-stars.png";
    } elseif ($cislo < 6.5) {
        return "./img/stars/3-stars.png";
    } elseif ($cislo < 7.5) {
        return "./img/stars/3-5-stars.png";
    } elseif ($cislo < 8.5) {
        return "./img/stars/4-stars.png";
    } elseif ($cislo < 9.5) {
        return "./img/stars/4-5-stars.png";
    } elseif ($cislo >= 9.5) {
        return "./img/stars/5-stars.png";
    }
}

function send_mail_kovar($to, $subject, $body) {
    $mail = new PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'mail.ldekonom.cz';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'info@pjhvysocina.cz';                 // SMTP username
    $mail->Password = 'kH28ndu2BZDb2GGB';                           // SMTP password
    $mail->SMTPSecure = '';                            // Enable encryption, 'ssl' also accepted

    $mail->From = "info@pjhvysocina.cz";
    $mail->FromName = "Pijem, jíme, hodnotíme (info@pjhvysocina.cz)";
    //$mail->addAddress('xixaom@centrum.cz', 'Joe User');     // Add a recipient
    $mail->addAddress($to);               // Name is optional
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    $mail->WordWrap = 80;                                 // Set word wrap to 50 characters
    //$mail->addAttachment('./prihlasky/' . $ID_PRIHLASKY . '.html');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    $mail->isHTML(true);
    $mail->CharSet = "utf-8"; // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AltBody = $body;

    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        
    }
}

function nahoru_fotky() {
    $result = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id] = %i', $_GET['nahoru']);
    $row = $result->fetch();
    $poradi_new = $row['poradi'] - 1;
    $result2 = dibi::query('SELECT * FROM [pjh_fotky] WHERE [poradi] = %i', $poradi_new, 'AND [id_cil] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik');
    $row2 = $result2->fetch();
    $dolu = $row2['id'];

    $arr = array('poradi' => $poradi_new);
    dibi::query('UPDATE [pjh_fotky] SET', $arr, 'WHERE [id] = %i', $_GET['nahoru']);
    $arr = array('poradi' => ($poradi_new + 1));
    dibi::query('UPDATE [pjh_fotky] SET', $arr, 'WHERE [id] = %i', $dolu);
}

function dolu_fotky() {
    $result = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id] = %i', $_GET['dolu']);
    $row = $result->fetch();
    $poradi_new = $row['poradi'] + 1;
    $result2 = dibi::query('SELECT * FROM [pjh_fotky] WHERE [poradi] = %i', $poradi_new, 'AND [id_cil] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik');
    $row2 = $result2->fetch();
    $nahoru = $row2['id'];

    $arr = array('poradi' => $poradi_new);
    dibi::query('UPDATE [pjh_fotky] SET', $arr, 'WHERE [id] = %i', $_GET['dolu']);
    $arr = array('poradi' => ($poradi_new - 1));
    dibi::query('UPDATE [pjh_fotky] SET', $arr, 'WHERE [id] = %i', $nahoru);
}

function nahoru_fotky_recenze() {
    $result = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id] = %i', $_GET['nahoru']);
    $row = $result->fetch();
    $poradi_new = $row['poradi'] - 1;
    $result2 = dibi::query('SELECT * FROM [pjh_fotky] WHERE [poradi] = %i', $poradi_new, 'AND [id_recenze] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik');
    $row2 = $result2->fetch();
    $dolu = $row2['id'];

    $arr = array('poradi' => $poradi_new);
    dibi::query('UPDATE [pjh_fotky] SET', $arr, 'WHERE [id] = %i', $_GET['nahoru']);
    $arr = array('poradi' => ($poradi_new + 1));
    dibi::query('UPDATE [pjh_fotky] SET', $arr, 'WHERE [id] = %i', $dolu);
}

function dolu_fotky_recenze() {
    $result = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id] = %i', $_GET['dolu']);
    $row = $result->fetch();
    $poradi_new = $row['poradi'] + 1;
    $result2 = dibi::query('SELECT * FROM [pjh_fotky] WHERE [poradi] = %i', $poradi_new, 'AND [id_recenze] = %i', $_GET[ad], 'AND [typ] = %s', 'podnik');
    $row2 = $result2->fetch();
    $nahoru = $row2['id'];

    $arr = array('poradi' => $poradi_new);
    dibi::query('UPDATE [pjh_fotky] SET', $arr, 'WHERE [id] = %i', $_GET['dolu']);
    $arr = array('poradi' => ($poradi_new - 1));
    dibi::query('UPDATE [pjh_fotky] SET', $arr, 'WHERE [id] = %i', $nahoru);
}

function show_banner() {
    $query = dibi::query("SELECT * FROM [pjh_bannery] WHERE [zverejnit] = %s", "ano", "ORDER BY %by", "id", "ASC");
    $i = 0;
    $pole = array();
    while ($row = $query->fetch()) {
        $i++;
        $pole[$i] = $row[id];
    }

    if ($i > 0) {
        if ($i == 1)
            $vol_reklamu = 1;
        else
            $vol_reklamu = rand(1, $i);


        $querya = dibi::query("SELECT * FROM [pjh_bannery] WHERE [id] = %i", $pole[$vol_reklamu]);
        $rowa = $querya->fetch();

        echo "<a href=\"./index.php?id=vyhledavani&ad=$rowa[sekce]&bd=profil&cd=$rowa[id_podnik]&banner=$rowa[id]\"><img class=\"\" width=\"100px\" src=\"$rowa[cil]\" title=\"" . get_nazev_podniku($rowa[id_podnik]) . ": $rowa[nazev]\" alt=\"" . get_nazev_podniku($rowa[id_podnik]) . ": $rowa[nazev]\" /></a>";

        $arr = array('zobrazeni' => $rowa[zobrazeni] + 1);
        dibi::query('UPDATE [pjh_bannery] SET', $arr, 'WHERE [id] = %i', $pole[$vol_reklamu]);
    }
}

function spocist_banner() {
    if ($_GET[banner] != "") {
        $querya = dibi::query("SELECT * FROM [pjh_bannery] WHERE [id] = %i", $_GET[banner]);
        $rowa = $querya->fetch();

        $arr = array('kliku' => $rowa[kliku] + 1);
        dibi::query('UPDATE [pjh_bannery] SET', $arr, 'WHERE [id] = %i', $_GET[banner]);
    }
}
?>


