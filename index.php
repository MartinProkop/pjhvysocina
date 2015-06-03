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
        } elseif ($_GET[id] == "vyhledavani") {
            if ($_GET[lang] == "cs") {
                if ($_GET[ad] == "restaurace") {
                    $title = "Restaurace | PIJEM, JÍME, HODNOTÍME";
                } elseif ($_GET[ad] == "kavarny") {
                    $title = "Kavárny | PIJEM, JÍME, HODNOTÍME";
                }
            }
        }
        ?>

        <title><?php echo $title; ?></title>

        <!-- font -->
        <link href='http://fonts.googleapis.com/css?family=Roboto+Slab' rel='stylesheet' type='text/css'>

        <!-- základ -->
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <meta name="author" content="LDekonom.cz" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="keywords" content="Pijem, jíme, hodnotíme, vysočina, restaurace, kavárny">
        <meta name="description" content="Pijem, jíme, hodnotíme - Vysočina: soukromé hodnocení vybraných restaurací a kaváren v Kraji Vysočina.">
        <link href="./img/favicon.png" rel="icon" type="image/gif" />
        <meta property="og:image" content="http://www.pjhvysocina.cz/img/logo_fb.jpg" />
        <meta property="og:url" content="http://www.pjhvysocina.cz/" />
        <meta property="og:title" content="Pijem, jíme, hodnotíme - Vysočina" />
        <meta property="og:description" content="Pijem, jíme, hodnotíme: soukromé hodnocení vybraných restaurací a kaváren v Kraji Vysočina." />        
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

        <!-- fancybox -->
        <script type="text/javascript" src="./lib/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>
        <script type="text/javascript" src="./lib/fancybox/source/jquery.fancybox.js?v=2.1.5"></script>
        <link rel="stylesheet" type="text/css" href="./lib/fancybox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
        <link rel="stylesheet" type="text/css" href="./lib/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
        <script type="text/javascript" src="./lib/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
        <link rel="stylesheet" type="text/css" href="./lib/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
        <script type="text/javascript" src="./lib/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
        <script type="text/javascript" src="./lib/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

        <!-- inicializace fancyboxu -->
        <script type="text/javascript">
            $(document).ready(function () {
                $('.fancybox').fancybox();

                $('.fancybox_iframe').fancybox({
                    width: '80%',
                    transitionIn: 'elastic',
                    transitionOut: 'elastic',
                    type: 'iframe',
                    autoScale: true,
                    helpers: {
                        title: {
                            type: 'float'
                        }
                    }
                });

                $('.fancybox_iframe_big').fancybox({
                    width: '90%',
                    height: '90%',
                    autoSize: true,
                    transitionIn: 'elastic',
                    transitionOut: 'elastic',
                    type: 'iframe',
                    autoScale: true,
                    helpers: {
                        title: {
                            type: 'float'
                        }
                    }
                });


                $('.fancybox-buttons').fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                    prevEffect: 'none',
                    nextEffect: 'none',
                    closeBtn: false,
                    helpers: {
                        title: {
                            type: 'float'
                        },
                        buttons: {}
                    },
                    afterLoad: function () {
                        this.title = 'Obrázek ' + (this.index + 1) + ' z ' + this.group.length + (this.title ? ' - ' + this.title : '');
                    }
                });
            });
        </script>
    </head>
    <body>
        <?php include_once("./lib/analyticstracking.php"); ?>  
        <?php //show_banner(); ?>
        <!-- hlavní -->
        <div id="hlavni">
            <!-- bramboty a jabka -->
            <?php if (!check_mobile()) { ?>
                <img id="potatoes" src="./img/brambory1.png" alt="PIJEM, JÍME, HODNOTÍME - brambory" />
                <img id="appels" src="./img/jablka1.png" alt="PIJEM, JÍME, HODNOTÍME - jablka" />
            <?php } ?>
            <div>
                <!-- header -->
                <div id="header">
                    <div class="wrapper">
                        <a href="./index.php"><img src="./img/logo.png" class="logo" alt="Pijem, jíme, hodnotíme"/></a>
                        <!-- <a href="./index.php" class="logotext">PIJEM, JÍME, HODNOTÍME</a> -->
                        <div id="main-menu">
                            <ul>
                                <li <?php echo $linksactive[gurmani]; ?>><a href="./index.php?id=gurmani" title="Gurmáni">Gurmáni</a></li>
                                <li <?php echo $linksactive[pravidlahodnoceni]; ?>><a href="./index.php?id=pravidlahodnoceni" title="Pravidla hodnocení">Pravidla hodnocení</a></li>
                                <li <?php echo $linksactive[kontakt]; ?>><a href="./index.php?id=kontakt" title="Kontakt">Kontakt</a></li>
                            </ul>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <!-- user -->
                <div class="wrapper">
                    <div id="user">
                        <?php
                        if (login_check()) {
                            $query = dibi::query('SELECT * FROM [pjh_uzivatele] WHERE [jmeno] = %s', $_SESSION[jmeno], "AND [stav] = %s", "");
                            $row = $query->fetch();

                            if ($row[admin] == "ano") {
                                ?>
                                <a href="./formulare.php?id=gurman&amp;ad=<?php echo $row[id]; ?>" title="Profil uživatele" class="gurmani <?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>"><img src="./img/gurmani.png" alt="gurmáni"> <?php echo $_SESSION[jmeno]; ?></a><img src="./img/gurmani-carka.png" alt="cara"><span class="prihlaseni_text"><a href="./administrace.php" title="Administrace">Administrace</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./formulare.php?id=zpravy" title="Zprávy" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>">Zprávy <?php echo echo_pocet_novych_zprav(); ?></a></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./formulare.php?id=nastaveniuzivatele" title="Nastavení uživatele" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>">Nastavení</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./index.php?action=logout" title="Odhlásit">Odhlásit</a></span>

                                <?php
                            } else {
                                ?>
                                <a href="./formulare.php?id=gurman&amp;ad=<?php echo $row[id]; ?>" title="Profil uživatele" class="gurmani <?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>"><img src="./img/gurmani.png" alt="gurmáni"> <?php echo $_SESSION[jmeno]; ?></a><img src="./img/gurmani-carka.png" alt="cara"><span class="prihlaseni_text"><a href="./formulare.php?id=nastaveniuzivatele" title="Nastavení uživatele" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>">Nastavení</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./formulare.php?id=zpravy" title="Zprávy" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>">Zprávy <?php echo echo_pocet_novych_zprav(); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./index.php?action=logout" title="Odhlásit">Odhlásit</a></span>
                                <?php
                            }
                        } else {
                            ?>
                            <a href="./formulare.php?id=registrace" title="Registrace" class="gurmani <?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>"><img src="./img/gurmani.png" alt="gurmáni"></a><img src="./img/gurmani-carka.png" alt="cara"><span class="prihlaseni_text"><a href="./formulare.php?id=registrace" title="Registrace" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>">Registrace</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./formulare.php?id=prihlaseni" title="Přihlášení" class="<?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>">Přihlášení</a></span>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <!-- zacina content -->
                <div id="content">
                    <?php if ($_GET[id] == null || $_GET[id] == "index") {
                        ?>
                        <div class="wrapper">
                            <div class="main-content full frontpage">
                                <div class="margin_top1">
                                    <div class="grid_4">
                                        <a href="./index.php?id=vyhledavani&amp;ad=restaurace" title="Restaurace"><img src="./img/frontpage/img-restaurace.png" onmouseover="this.src = './img/frontpage/img-restaurace2.png';" onmouseout="this.src = './img/frontpage/img-restaurace.png';" alt="Restaurace" class="imgCenter" />
                                            <h1 class="margin_top1">Restaurace</h1></a>
                                    </div>
                                    <div class="grid_4">
                                        <a href="./index.php?id=vyhledavani&amp;ad=kavarny" title="Kavárny"><img src="./img/frontpage/img-kavarny.png" alt="Kavárny" onmouseover="this.src = './img/frontpage/img-kavarny2.png';" onmouseout="this.src = './img/frontpage/img-kavarny.png';" class="imgCenter" />
                                            <h1 class="margin_top1">Kavárny</h1></a>
                                    </div>
                                    <div class="grid_4 svetle">
                                        <img src="./img/frontpage/pripravujeme.png" alt="Rychlé občerstvení" class="imgCenter" />
                                        <h1 class="margin_top1">Rychlá občerstvení</h1>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div class="margin_top1">
                                    <?php
                                    $query = dibi::query('SELECT * FROM [pjh_global] WHERE [id] = %i', 1);
                                    while ($row = $query->fetch()) {
                                        ?>
                                        <div class="grid_4">
                                            <p><?php echo $row[popis_restaurace]; ?></p>
                                        </div>
                                        <div class="grid_4">
                                            <p><?php echo $row[popis_kavarny]; ?></p>
                                        </div>
                                        <div class="grid_4 svetle">
                                            <p><?php echo $row[popis_rychla_obcerstveni]; ?></p>
                                        </div>
                                        <div class="clear"></div>
                                        <?php
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                        <div class="wrapper">
                            <div class="delici_horizontalni_cara">NEJLEPÉ HODNOCENÉ</div>
                            <div class="main-content full">
                                <?php
                                $query_restaurace = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", "restaurace", "AND [zverejnit] = %s", "ano", "ORDER BY %by", "hodnoceni", "DESC");
                                $i = 1;
                                $arr_rest = array();
                                while ($row_restaurace = $query_restaurace->fetch()) {
                                    if ($i == 4)
                                        break;
                                    $arr_rest[$i] = $row_restaurace[id];
                                    $i++;
                                }

                                $query_kavarny = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", "kavarny", "AND [zverejnit] = %s", "ano", "ORDER BY %by", "hodnoceni", "DESC");
                                $i = 1;
                                $arr_kav = array();
                                while ($row_kavarny = $query_kavarny->fetch()) {
                                    if ($i == 4)
                                        break;
                                    $arr_kav[$i] = $row_kavarny[id];
                                    $i++;
                                }

                                for ($i = 1; $i < 4; $i++) {
                                    $query_resta = dibi::query("SELECT * FROM [pjh_podnik] WHERE [id] = %i", $arr_rest[$i]);
                                    $row_resta = $query_resta->fetch();

                                    $query_kava = dibi::query("SELECT * FROM [pjh_podnik] WHERE [id] = %i", $arr_kav[$i]);
                                    $row_kava = $query_kava->fetch();
                                    ?>
                                    <div class="<?php if ($i > 1) echo "margin_top2"; ?>">
                                        <div class="grid_4">
                                            <?php
                                            if ($row_resta[id] != "") {
                                                ?>
                                                <div class="item-thumbs card_front_page">
                                                    <a class="hover-wrap" href="./index.php?id=vyhledavani&amp;ad=restaurace&amp;bd=profil&amp;cd=<?php echo $row_resta[id]; ?>" title="<?php echo $row_resta[nazev]; ?>">
                                                        <span class="overlay-img"></span>
                                                        <span class="overlay-img-thumb item-thumbs-detail"></span>
                                                    </a>
                                                    <div class="card_vnitrni5">
                                                        <span class="card_text_nazev"><?php echo $row_resta[nazev]; ?></span><br />
                                                        <span class="card_text_procenta"><?php echo $row_resta[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row_resta[hodnoceni]); ?>" alt="obrazek" height="16"/></span>
                                                        <div>
                                                            <div class="grid_4">
                                                                <img class="card_obrazek_podnik100 margin_left5" src="<?php echo get_foto_of_podnik($row_resta[id]); ?>" alt="obrazek"/>
                                                            </div>
                                                            <div class="grid_8">
                                                                <p class="card_text_front_page"><?php echo substr(nl2br($row_resta[obsah]), 0, 50); ?></p>
                                                            </div>
                                                            <div class="clear"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="grid_4">
                                            <?php
                                            if ($row_kava[id] != "") {
                                                ?>
                                                <div class="item-thumbs card_front_page">
                                                    <a class="hover-wrap" href="./index.php?id=vyhledavani&amp;ad=kavarny&amp;bd=profil&amp;cd=<?php echo $row_kava[id]; ?>" title="<?php echo $row_kava[nazev]; ?>">
                                                        <span class="overlay-img"></span>
                                                        <span class="overlay-img-thumb item-thumbs-detail"></span>
                                                    </a>
                                                    <div class="card_vnitrni5">
                                                        <span class="card_text_nazev"><?php echo $row_kava[nazev]; ?></span><br />
                                                        <span class="card_text_procenta"><?php echo $row_kava[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row_kava[hodnoceni]); ?>" alt="obrazek" height="16"/></span>
                                                        <div>
                                                            <div class="grid_4">
                                                                <img class="card_obrazek_podnik100 margin_left5" src="<?php echo get_foto_of_podnik($row_kava[id]); ?>" alt="obrazek"/>
                                                            </div>
                                                            <div class="grid_8">
                                                                <p class="card_text_front_page"><?php echo substr(nl2br($row_kava[obsah]), 0, 50); ?></p>
                                                            </div>
                                                            <div class="clear"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="grid_4">
                                        </div>
                                        <?php
                                        ?>
                                        <div class="clear"></div>
                                    </div>                                
                                    <?php
                                }
                                ?>
                            </div>                    
                        </div>
                        <?php
                    } elseif ($_GET[id] == "gurmani") {
                        if ($_GET[pismeno] == "vse") {
                            $linksactive_vyhledavani_pismeno[vse] = "active first";
                        } elseif ($_GET[pismeno] == "a") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[a] = "active";
                        } elseif ($_GET[pismeno] == "b") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[b] = "active";
                        } elseif ($_GET[pismeno] == "c") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[c] = "active";
                        } elseif ($_GET[pismeno] == "d") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[d] = "active";
                        } elseif ($_GET[pismeno] == "e") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[e] = "active";
                        } elseif ($_GET[pismeno] == "f") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[f] = "active";
                        } elseif ($_GET[pismeno] == "g") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[g] = "active";
                        } elseif ($_GET[pismeno] == "h") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[h] = "active";
                        } elseif ($_GET[pismeno] == "i") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[i] = "active";
                        } elseif ($_GET[pismeno] == "j") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[j] = "active";
                        } elseif ($_GET[pismeno] == "k") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[k] = "active";
                        } elseif ($_GET[pismeno] == "l") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[l] = "active";
                        } elseif ($_GET[pismeno] == "m") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[m] = "active";
                        } elseif ($_GET[pismeno] == "n") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[n] = "active";
                        } elseif ($_GET[pismeno] == "o") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[o] = "active";
                        } elseif ($_GET[pismeno] == "p") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[p] = "active";
                        } elseif ($_GET[pismeno] == "q") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[q] = "active";
                        } elseif ($_GET[pismeno] == "r") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[r] = "active";
                        } elseif ($_GET[pismeno] == "s") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[s] = "active";
                        } elseif ($_GET[pismeno] == "t") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[t] = "active";
                        } elseif ($_GET[pismeno] == "u") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[u] = "active";
                        } elseif ($_GET[pismeno] == "v") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[v] = "active";
                        } elseif ($_GET[pismeno] == "w") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[w] = "active";
                        } elseif ($_GET[pismeno] == "x") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[x] = "active";
                        } elseif ($_GET[pismeno] == "y") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[y] = "active";
                        } elseif ($_GET[pismeno] == "z") {
                            $linksactive_vyhledavani_pismeno[vse] = "first";
                            $linksactive_vyhledavani_pismeno[z] = "active";
                        }
                        ?>
                        <h1 id="content_logo_nadpis">Gurmáni</h1>
                        <div class="wrapper2">
                            <div id="searching_bar_abeceda-menu">
                                <form id="formul" name="formul" method="post" action="./index.php?id=gurmani" >
                                    <ul>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[vse]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=vse" title="">VŠE</a></li>  
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[a]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=a" title="">A</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[b]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=b" title="">B</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[c]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=c" title="">C</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[d]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=d" title="">D</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[e]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=e" title="">E</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[f]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=f" title="">F</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[g]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=g" title="">G</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[h]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=h" title="">H</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[i]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=i" title="">I</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[j]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=j" title="">J</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[k]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=k" title="">K</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[l]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=l" title="">L</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[m]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=m" title="">M</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[n]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=n" title="">N</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[o]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=o" title="">O</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[p]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=p" title="">P</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[q]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=q" title="">Q</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[r]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=r" title="">R</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[s]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=s" title="">S</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[t]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=t" title="">T</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[u]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=u" title="">U</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[v]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=v" title="">V</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[w]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=w" title="">W</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[x]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=x" title="">X</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[y]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=y" title="">Y</a></li>
                                        <li class="<?php echo $linksactive_vyhledavani_pismeno[z]; ?>" ><a href="./index.php?id=gurmani&amp;pismeno=z" title="">Z</a></li>      
                                        <li><input type="text" class="input width_search_field" id="retezec" name="retezec" value="<?php echo $_POST[retezec]; ?>" placeholder="Zadej jméno"></li>
                                        <li><input type="submit" class="najdi_link" id="text-input-1-submit" value="Najdi"></li>
                                    </ul>
                                </form>
                                <div class="clear"></div>   
                            </div>
                        </div>
                        <div id="vyhledavani" class="margin_top1">
                            <div class="wrapper">   
                                <div>

                                    <?php
                                    if ($_GET[pismeno] == "" && $_POST[retezec] == "") {
                                        echo "<div class=\"delici_horizontalni_cara\">TOP10</div>";
                                        $query = dibi::query("SELECT * FROM [pjh_uzivatele] WHERE [stav] = %s", "", "ORDER BY %by", "hodnost", "DESC");
                                        $i = 1;

                                        while ($row = $query->fetch()) {
                                            ?>
                                            <div class="card_prehledy60 margin_top3">
                                                <div class="card_vnitrni5">
                                                    <div>
                                                        <a href="./formulare.php?id=gurman&amp;ad=<?php echo $row[id]; ?>" class="<?php if (!check_mobile()) echo "fancybox_iframe_big fancybox.iframe"; ?>" title="Gurmán - <?php echo $row[jmeno]; ?>">
                                                            <div class="grid_1 card_prehledy_border50">
                                                                <div class="card_vertical_align10 ">
                                                                    <span class="card_text_cislo cerna_barva"><?php echo $i; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="grid_1">
                                                                <div class="card_vertical_align5">
                                                                    <img class="card_obrazek_podnik80" src="<?php echo get_foto_of_user($row[id]); ?>" alt="foto"/>
                                                                </div>
                                                            </div>
                                                            <div class="grid_4 card_prehledy_border50">
                                                                <div class="card_vertical_align5">
                                                                    <span class="card_text_nazev cerna_barva"><?php echo $row[jmeno]; ?></span><br />
                                                                    <span class="card_text_procenta cerna_barva "><?php echo echo_hodnost($row[hodnost]); ?></span>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        <div class="grid_6 ">
                                                            <p class="card_text_bez_paddingu cerna_barva text_11px"><?php echo $row[popis]; ?></p>
                                                        </div>                                                
                                                        <div class="clear"></div>
                                                    </div>
                                                </div>
                                            </div>                                
                                            <?php
                                            if ($i == 10)
                                                break;
                                            $i++;
                                        }
                                    } else {
                                        if ($_GET[pismeno] != "") {
                                            if ($_GET[pismeno] == "vse") {
                                                $query = dibi::query("SELECT * FROM [pjh_uzivatele] WHERE [stav] = %s", "", "ORDER BY %by", "jmeno", "ASC");
                                            } else {
                                                $query = dibi::query("SELECT * FROM [pjh_uzivatele] WHERE [jmeno] LIKE %s", $_GET[pismeno] . '%', "AND [stav] = %s", "", "ORDER BY %by", "jmeno", "ASC");
                                            }
                                        } elseif ($_POST[retezec] != "") {
                                            $query = dibi::query("SELECT * FROM [pjh_uzivatele] WHERE [jmeno] LIKE %s", '%' . $_POST[retezec] . '%', "AND [stav] = %s", "", "ORDER BY %by", "jmeno", "ASC");
                                        }
                                        $i = 1;
                                        if ($query->count() == 0)
                                            echo "<div class=\"msg err\"><h2>Je nám líto, ale není zde gurmán, který by odpovídal zadání!</h2><p>Hledejte dále.</p></div>";
                                        while ($row = $query->fetch()) {
                                            ?>
                                            <div class="card_prehledy60 margin_top3">
                                                <div class="card_vnitrni5">
                                                    <div>
                                                        <a href="./formulare.php?id=gurman&amp;ad=<?php echo $row[id]; ?>" class="<?php if (!check_mobile()) echo "fancybox_iframe_big fancybox.iframe"; ?>" title="Gurmán - <?php echo $row[jmeno]; ?>">
                                                            <div class="grid_1 card_prehledy_border50">
                                                                <div class="card_vertical_align10 ">
                                                                    <span class="card_text_cislo cerna_barva"><?php echo $i; ?></span>
                                                                </div>
                                                            </div>
                                                            <div class="grid_1">
                                                                <img class="card_obrazek_podnik80" src="<?php echo get_foto_of_user($row[id]); ?>" alt="obrazek"/>
                                                            </div>
                                                            <div class="grid_4 card_prehledy_border50">
                                                                <div class="card_vertical_align5">
                                                                    <span class="card_text_nazev cerna_barva"><?php echo $row[jmeno]; ?></span><br />
                                                                    <span class="card_text_procenta cerna_barva "><?php echo echo_hodnost($row[hodnost]); ?></span>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        <div class="grid_6 ">
                                                            <p class="card_text_bez_paddingu cerna_barva text_11px"><?php echo $row[popis]; ?></p>
                                                        </div>                                                
                                                        <div class="clear"></div>
                                                    </div>
                                                </div>
                                            </div>                                
                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                </div>                    
                            </div>
                            <div class="clear"></div>   
                        </div>
                        <?php
                    } elseif ($_GET[id] == "pravidlahodnoceni") {
                        ?>
                        <h1 id="content_logo_nadpis">Pravidla hodnocení</h1>
                        <div class="wrapper">   
                            <div class="card_bez_thumbefektu">
                                <div class="card_vnitrni10">
                                    <div>
                                        <p class="card_text">
                                            <?php
                                            $query = dibi::query('SELECT * FROM [pjh_stranky] WHERE [sekce] = %s', "pravidlahodnoceni");
                                            $row = $query->fetch();
                                            echo nl2br($row[obsah]);
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                    } elseif ($_GET[id] == "kontakt") {
                        ?>
                        <h1 id="content_logo_nadpis">Kontakt</h1>
                        <div class="wrapper">   
                            <div class="card_bez_thumbefektu">
                                <div class="card_vnitrni10">
                                    <div>
                                        <p class="card_text">
                                            <?php
                                            $query = dibi::query('SELECT * FROM [pjh_stranky] WHERE [sekce] = %s', "kontakt");
                                            $row = $query->fetch();
                                            echo nl2br($row[obsah]);
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <?php
                    } elseif ($_GET[id] == "vyhledavani") {
                        if ($_GET[ad] == "restaurace" || $_GET[ad] == "kavarny") {
                            if ($_GET[bd] == "") {
                                $linksactive_vyhledavani_menu[domu] = "class=\"first active\"";
                            } elseif ($_GET[bd] == "top10") {
                                $linksactive_vyhledavani_menu[domu] = "class=\"first\"";
                                $linksactive_vyhledavani_menu[top10] = "class=\"active\"";
                            } elseif ($_GET[bd] == "doporucujeme") {
                                $linksactive_vyhledavani_menu[domu] = "class=\"first\"";
                                $linksactive_vyhledavani_menu[doporucujeme] = "class=\"active\"";
                            } elseif ($_GET[bd] == "hledame" || $_GET[bd] == "profil") {
                                $linksactive_vyhledavani_menu[domu] = "class=\"first\"";
                            }

                            if ($_GET[ad] == "restaurace") {
                                ?>
                                <img id="content_logo" src="img/maso.png" alt="PIJEM, JÍME, HODNOTÍME - restaurace" />
                                <div id="content_logo_text"><img src="img/restaurace.png" height="40" alt="PIJEM, JÍME, HODNOTÍME - restaurace" /><a href="./index.php?id=vyhledavani&amp;ad=restaurace">Restaurace</a></div>
                                <?php
                            } elseif ($_GET[ad] == "kavarny") {
                                ?>
                                <img id="content_logo" src="img/kava.png" alt="PIJEM, JÍME, HODNOTÍME - kavárny" />
                                <div id="content_logo_text"><img src="img/kavarny.png" height="40" alt="PIJEM, JÍME, HODNOTÍME - kavárny" /><a href="./index.php?id=vyhledavani&amp;ad=kavarny">Kavárny</a></div>
                                <?php
                            }

                            //osetreni prazdneho etezce
                            if ($_GET[retezec] == "retezec")
                                $_GET[retezec] = "";

                            if ($_GET[retezec] != "")
                                $_POST[retezec] = $_GET[retezec];
                            if ($_POST[retezec] != "")
                                $_GET[retezec] = $_POST[retezec];

                            //osetreni prazdneho okresu
                            if ($_GET[okres] == "" || $_GET[okres] == null)
                                $_GET[okres] = "vysocina";
                            ?>

                            <div class="wrapper3">
                                <div id="searching_bar-menu">
                                    <form id="formul" name="formul" method="post" action="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame" >
                                        <ul>
                                            <li <?php echo $linksactive_vyhledavani_menu[domu]; ?>><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>" title="">Domů</a></li>
                                            <li <?php echo $linksactive_vyhledavani_menu[top10]; ?>><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=top10" title="">Top10</a></li>
                                            <li <?php echo $linksactive_vyhledavani_menu[doporucujeme]; ?>><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=doporucujeme" title="">Doporučujeme</a></li>
                                            <li><input type="text" class="input width_search_field" id="retezec" name="retezec" value="<?php echo $_POST[retezec]; ?>" placeholder="Nějaký text"></li>
                                            <li><input type="submit" class="najdi_link" id="text-input-1-submit" value="Najdi"></li>
                                            <li><a class="napis_recenzi <?php if (!check_mobile()) echo "fancybox_iframe fancybox.iframe"; ?>" href="./formulare.php?id=napisrecenzi&amp;ad=<?php echo $_GET[ad]; ?>&amp;cd=<?php echo $_GET[cd]; ?>" title="Napiš recenzi" />Napiš recenzi</a></li>
                                        </ul>
                                    </form>
                                    <div class="clear"></div>   
                                </div>
                            </div>
                            <?php
                            if ($_GET[bd] == "" || $_GET[bd] == "hledame") {
                                if ($_GET[okres] == "" || $_GET[okres] == "vysocina") {
                                    $linksactive_vyhledavani_okres = "mapa1.png";
                                } elseif ($_GET[okres] == "ZnS") {
                                    $linksactive_vyhledavani_okres = "ZnS1.png";
                                } elseif ($_GET[okres] == "T") {
                                    $linksactive_vyhledavani_okres = "T1.png";
                                } elseif ($_GET[okres] == "P") {
                                    $linksactive_vyhledavani_okres = "P1.png";
                                } elseif ($_GET[okres] == "HB") {
                                    $linksactive_vyhledavani_okres = "HB1.png";
                                } elseif ($_GET[okres] == "J") {
                                    $linksactive_vyhledavani_okres = "J1.png";
                                }
                                ?>
                                <div id="vyhledavani" class="margin_top1">
                                    <div class="wrapper">   
                                        <div>
                                            <div class="grid_3">
                                                <img id="arrow" src="./img/mapa/vyber-okres.png" alt="PIJEM, JÍME, HODNOTÍME - mapa" />
                                                <img id="vyber_mapa" src="./img/mapa/<?php echo $linksactive_vyhledavani_okres; ?>" border="0" width="230" height="212" orgWidth="230" orgHeight="212" usemap="#vyber_mapa" alt="obrazek" />
                                                <map name="vyber_mapa" id="vyber_mapa">
                                                    <area id="ZnS" alt="Žďár nad Sázavou" title="Žďár nad Sázavou" href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=ZnS&amp;pismeno=vse" shape="poly" coords="152,45,137,79,151,116,197,132,216,115,214,91,215,70,173,41" target="_self"  onmouseover="if (document.images)
                                                                            document.getElementById('vyber_mapa').src = './img/mapa/ZnS1.png';" onmouseout="if (document.images)
                                                                                        document.getElementById('vyber_mapa').src = './img/mapa/<?php echo $linksactive_vyhledavani_okres; ?>';"  />
                                                    <area id="T" alt="Třebíč" title="Třebíč" href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=T&amp;pismeno=vse" shape="poly" coords="141,129,193,154,191,174,148,185,116,201,105,193,119,149" target="_self"  onmouseover="if (document.images)
                                                                            document.getElementById('vyber_mapa').src = './img/mapa/T1.png';" onmouseout="if (document.images)
                                                                                        document.getElementById('vyber_mapa').src = './img/mapa/<?php echo $linksactive_vyhledavani_okres; ?>';"  />
                                                    <area id="P" alt="Pelhřimov" title="Pelhřimov" href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=P&amp;pismeno=vse" shape="poly" coords="18,66,9,122,55,141,67,106,67,71" target="_self"  onmouseover="if (document.images)
                                                                            document.getElementById('vyber_mapa').src = './img/mapa/P1.png';" onmouseout="if (document.images)
                                                                                        document.getElementById('vyber_mapa').src = './img/mapa/<?php echo $linksactive_vyhledavani_okres; ?>';"  />
                                                    <area id="HB" alt="Havlíčkův Brod" title="Havlíčkův Brod" href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=HB&amp;pismeno=vse" shape="poly" coords="88,11,139,41,120,67,113,78,95,83,84,58,63,54,54,32" target="_self"  onmouseover="if (document.images)
                                                                            document.getElementById('vyber_mapa').src = './img/mapa/HB1.png';" onmouseout="if (document.images)
                                                                                        document.getElementById('vyber_mapa').src = './img/mapa/<?php echo $linksactive_vyhledavani_okres; ?>';"  />
                                                    <area id="J" alt="Jihlava" title="Jihlava" href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=J&amp;pismeno=vse" shape="poly" coords="71,138,105,159,111,127,135,108,120,93,82,99" target="_self"  onmouseover="if (document.images)
                                                                            document.getElementById('vyber_mapa').src = './img/mapa/J1.png';" onmouseout="if (document.images)
                                                                                        document.getElementById('vyber_mapa').src = './img/mapa/<?php echo $linksactive_vyhledavani_okres; ?>';"  />
                                                </map>
                                            </div>
                                            <?php
                                            if ($_GET[bd] == "") {
                                                ?>
                                                <div class="grid_6">    
                                                    <?php
                                                    $query_aktuality = dibi::query("SELECT * FROM [pjh_aktuality] WHERE [sekce] = %s", $_GET[ad], "AND [zverejnit] = %s", "ano", "ORDER BY %by", "date", "desc");
                                                    $i = 1;
                                                    while ($row_aktuality = $query_aktuality->fetch()) {
                                                        if ($i == 4)
                                                            break;
                                                        ?>
                                                        <div class="card_bez_thumbefektu margin_top3">
                                                            <?php
                                                            if ($row_aktuality[id_podnik] != 0) {
                                                                ?>
                                                                <a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=profil&amp;cd=<?php echo $row_aktuality[id_podnik]; ?>" >
                                                                    <?php
                                                                }
                                                                ?>
                                                                <div class="card_vnitrni10">
                                                                    <div>
                                                                        <span class="card_text_nazev"><?php echo $row_aktuality[nazev]; ?></span><br />
                                                                        <span class="card_text_misto"><?php echo echo_date($row_aktuality[date]); ?></span><br />
                                                                        <p class="card_text"><?php echo nl2br($row_aktuality[obsah]); ?></p>
                                                                        <?php
                                                                        $query_fotky = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $row_aktuality[id], 'AND [typ] = %s', 'aktualita', "AND [zverejnit] = %s", "ano", 'ORDER BY %by', 'poradi', 'ASC');
                                                                        $pocet_fotek = $query_fotky->count();
                                                                        $i = 1;
                                                                        while ($row_fotky = $query_fotky->fetch()) {
                                                                            if ($i == 1) {
                                                                                ?> 
                                                                                <a href="<?php echo $row_fotky[cil]; ?>" class="fancybox-buttons" data-fancybox-group="<?php echo $row_fotky[nazev]; ?>" title="<?php echo $row_fotky[nazev]; ?>"><img class="width_100" src="<?php echo $row_fotky[cil]; ?>" alt="obrazek"/></a>
                                                                                <?php
                                                                            } else {
                                                                                ?> 
                                                                                <a href="<?php echo $row_fotky[cil]; ?>" class="fancybox-buttons" data-fancybox-group="<?php echo $row_fotky[nazev]; ?>" title="<?php echo $row_fotky[nazev]; ?>"><img class="width_32" src="<?php echo $row_fotky[cil]; ?>" alt="obrazek"/></a>
                                                                                <?php
                                                                            }
                                                                            $i++;
                                                                        }
                                                                        ?>                                                                   

                                                                        <div class="clear"></div>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                                if ($row_aktuality[id_podnik] != 0) {
                                                                    ?>
                                                                </a>
                                                                <?php
                                                            }
                                                            ?>
                                                        </div>

                                                        <?php
                                                        $i++;
                                                    }
                                                    ?>                                                         
                                                </div>
                                                <div class="grid_3">

                                                    <?php
                                                    $query_recenze = dibi::query("SELECT * FROM [pjh_recenze] WHERE [typ] = %s", $_GET[ad], "AND [zverejnit] = %s", "ano", "ORDER BY %by", "date", "desc");
                                                    $i = 1;
                                                    while ($row_recenze = $query_recenze->fetch()) {
                                                        if ($i == 4)
                                                            break;
                                                        ?>
                                                        <div class="recenze">
                                                            <span class="recenze_nazev"><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=profil&amp;cd=<?php echo $row_recenze[id_podnik]; ?>"><?php echo $row_recenze[nazevpodnik]; ?></a></span>
                                                            <p class="recenze_text"><?php echo substr(nl2br($row_recenze[obsah]), 0, 400); ?>... <a href="./formulare.php?id=recenze&amp;ad=<?php echo $row_recenze[id]; ?>" class="<?php if (!check_mobile()) echo "fancybox fancybox.iframe"; ?> recenze_odkaz_normal" title="<?php echo $row_recenze[nazevpodniku]; ?>">[celá recenze]</a></p>
                                                            <span class="recenze_autor"><a href="./formulare.php?id=gurman&amp;ad=<?php echo $row_recenze[id_autor]; ?>" class="<?php if (!check_mobile()) echo "fancybox_iframe_big fancybox.iframe"; ?>" title="<?php echo $row_recenze[nazevautor]; ?>"><?php echo $row_recenze[nazevautor]; ?> <span class="text_11px">(<?php echo echo_date($row_recenze[date]); ?>)</span></a></span>
                                                        </div>                                    

                                                        <?php
                                                        $i++;
                                                    }
                                                    ?>                                                    
                                                </div>

                                                <?php
                                            } elseif ($_GET[bd] == "hledame") {


                                                if ($_GET[pismeno] == "vse") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "active first";
                                                } elseif ($_GET[pismeno] == "a") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[a] = "active";
                                                } elseif ($_GET[pismeno] == "b") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[b] = "active";
                                                } elseif ($_GET[pismeno] == "c") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[c] = "active";
                                                } elseif ($_GET[pismeno] == "d") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[d] = "active";
                                                } elseif ($_GET[pismeno] == "e") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[e] = "active";
                                                } elseif ($_GET[pismeno] == "f") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[f] = "active";
                                                } elseif ($_GET[pismeno] == "g") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[g] = "active";
                                                } elseif ($_GET[pismeno] == "h") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[h] = "active";
                                                } elseif ($_GET[pismeno] == "i") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[i] = "active";
                                                } elseif ($_GET[pismeno] == "j") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[j] = "active";
                                                } elseif ($_GET[pismeno] == "k") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[k] = "active";
                                                } elseif ($_GET[pismeno] == "l") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[l] = "active";
                                                } elseif ($_GET[pismeno] == "m") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[m] = "active";
                                                } elseif ($_GET[pismeno] == "n") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[n] = "active";
                                                } elseif ($_GET[pismeno] == "o") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[o] = "active";
                                                } elseif ($_GET[pismeno] == "p") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[p] = "active";
                                                } elseif ($_GET[pismeno] == "q") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[q] = "active";
                                                } elseif ($_GET[pismeno] == "r") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[r] = "active";
                                                } elseif ($_GET[pismeno] == "s") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[s] = "active";
                                                } elseif ($_GET[pismeno] == "t") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[t] = "active";
                                                } elseif ($_GET[pismeno] == "u") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[u] = "active";
                                                } elseif ($_GET[pismeno] == "v") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[v] = "active";
                                                } elseif ($_GET[pismeno] == "w") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[w] = "active";
                                                } elseif ($_GET[pismeno] == "x") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[x] = "active";
                                                } elseif ($_GET[pismeno] == "y") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[y] = "active";
                                                } elseif ($_GET[pismeno] == "z") {
                                                    $linksactive_vyhledavani_pismeno[vse] = "first";
                                                    $linksactive_vyhledavani_pismeno[z] = "active";
                                                }


                                                //osetreni prazdneho etezce
                                                if ($_GET[retezec] == "" || $_GET[retezec] == null)
                                                    $_GET[retezec] = "retezec";
                                                ?>
                                                <div class="grid_9">
                                                    <div id="abeceda">
                                                        <ul>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[vse]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=vse" title="">VŠE</a></li> 
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[a]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=a" title="">A</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[b]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=b" title="">B</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[c]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=c" title="">C</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[d]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=d" title="">D</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[e]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=e" title="">E</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[f]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=f" title="">F</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[g]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=g" title="">G</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[h]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=h" title="">H</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[i]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=i" title="">I</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[j]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=j" title="">J</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[k]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=k" title="">K</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[l]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=l" title="">L</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[m]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=m" title="">M</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[n]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=n" title="">N</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[o]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=o" title="">O</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[p]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=p" title="">P</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[q]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=q" title="">Q</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[r]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=r" title="">R</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[s]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=s" title="">S</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[t]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=t" title="">T</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[u]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=u" title="">U</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[v]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=v" title="">V</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[w]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=w" title="">W</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[x]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=x" title="">X</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[y]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=y" title="">Y</a></li>
                                                            <li class="<?php echo $linksactive_vyhledavani_pismeno[z]; ?>" ><a href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=hledame&okres=<?php echo $_GET[okres]; ?>&retezec=<?php echo $_GET[retezec]; ?>&amp;pismeno=z" title="">Z</a></li>  
                                                        </ul>
                                                    </div>
                                                    <div class="clear"></div>

                                                    <?php
                                                    //osetreni prazdneho etezce
                                                    if ($_GET[retezec] == "retezec")
                                                        $_GET[retezec] = "";

                                                    //osetreni prazdneho okresu
                                                    if ($_GET[okres] == "vysocina")
                                                        $_GET[okres] = "";

                                                    if ($_GET[pismeno] != "" && $_GET[okres] != "") {
                                                        if ($_GET[pismeno] == "vse") {
                                                            $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", $_GET[ad], "AND [zverejnit] = %s", "ano", "AND [okres] = %s", $_GET[okres], "ORDER BY %by", "nazev", "ASC");
                                                        } else {
                                                            $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", $_GET[ad], "AND [zverejnit] = %s", "ano", "AND [nazev] LIKE %s", $_GET[pismeno] . '%', "AND [okres] = %s", $_GET[okres], "ORDER BY %by", "nazev", "ASC");
                                                        }
                                                    } elseif ($_GET[pismeno] != "") {

                                                        if ($_GET[pismeno] == "vse") {
                                                            $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", $_GET[ad], "AND [zverejnit] = %s", "ano", "ORDER BY %by", "nazev", "ASC");
                                                        } else {
                                                            $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", $_GET[ad], "AND [zverejnit] = %s", "ano", "AND [nazev] LIKE %s", $_GET[pismeno] . '%', "ORDER BY %by", "nazev", "ASC");
                                                        }
                                                    } elseif ($_POST[retezec] != "" && $_GET[okres] != "") {
                                                        $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", $_GET[ad], "AND [zverejnit] = %s", "ano", "AND [nazev] LIKE %s", '%' . $_POST[retezec] . '%', "AND [okres] = %s", $_GET[okres], "ORDER BY %by", "nazev", "ASC");
                                                    } elseif ($_POST[retezec] != "") {
                                                        $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", $_GET[ad], "AND [zverejnit] = %s", "ano", "AND [nazev] LIKE %s", '%' . $_POST[retezec] . '%', "ORDER BY %by", "nazev", "ASC");
                                                    } elseif ($_GET[okres] != "") {
                                                        $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", $_GET[ad], "AND [zverejnit] = %s", "ano", "AND [okres] = %s", $_GET[okres], "ORDER BY %by", "nazev", "ASC");
                                                    } else {
                                                        $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", $_GET[ad], "AND [zverejnit] = %s", "ano", "ORDER BY %by", "nazev", "ASC");
                                                    }

                                                    $i = 1;
                                                    if ($query->count() == 0)
                                                        echo "<div class=\"msg err\"><h2>Je nám líto, ale nemáme v databázi žádný podnik, který by odpovídal zadání!</h2><p>Hledejte dále.</p></div>";

                                                    while ($row = $query->fetch()) {
                                                        ?>
                                                        <div class="card_bez_thumbefektu margin_top3 card_prehledy_border200_bez_cary">
                                                            <a class="" href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=profil&amp;cd=<?php echo $row[id]; ?>" title="Lorem ipsum dolor sit amet, consectetur adipiscing elit">
                                                                <div class="card_vnitrni5">
                                                                    <div>
                                                                        <div class="grid_8">
                                                                            <span class="card_text_nazev"><?php echo $row[nazev]; ?></span><br />
                                                                            <span class="card_text_procenta"><?php echo $row[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row[hodnoceni]); ?>" alt="obrazek" height="16"/></span>
                                                                            <p class="card_text"><?php echo substr(nl2br($row[obsah]), 0, 250); ?>...</p>
                                                                        </div>
                                                                        <div class="grid_4">
                                                                            <img class="card_obrazek_podnik100" src="<?php echo get_foto_of_podnik($row[id]); ?>" alt="obrazek"/>
                                                                        </div>
                                                                        <div class="clear"></div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </div>                                                   
                                                        <?php
                                                        $i++;
                                                    }
                                                    ?>
                                                </div>
                                            </div>                    
                                        </div>
                                        <div class="clear"></div>   
                                    </div>

                                    <?php
                                }
                                ?>
                            </div>                    
                        </div>
                        <div class="clear"></div>   
                    </div>


                    <?php
                } elseif ($_GET[bd] == "doporucujeme") {
                    ?>
                    <div id="vyhledavani" class="margin_top1">
                        <div class="wrapper">   
                            <div>                    
                                <?php
                                $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", $_GET[ad], "AND [zverejnit] = %s", "ano", "AND [doporucujeme] = %s", "ano", 'ORDER BY %by', 'hodnoceni', 'DESC');
                                $i = 1;
                                while ($row = $query->fetch()) {
                                    if ($i == 11)
                                        break;
                                    ?>
                                    <div class="card_prehledy70 margin_top3">
                                        <div class="card_vnitrni5">
                                            <div>
                                                <a class="" href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=profil&amp;cd=<?php echo $row[id]; ?>" title="<?php echo $row[nazev]; ?>">
                                                    <div class="grid_1 card_prehledy_border70_bez_cary">
                                                        <div class="card_vertical_align15 ">
                                                            <span class="card_text_cislo cerna_barva"><?php echo $i; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="grid_2">
                                                        <img class="card_obrazek_podnik100" src="<?php echo get_foto_of_podnik($row[id]); ?>" alt="obrazek"/>
                                                    </div>
                                                    <div class="grid_4 card_prehledy_border70">
                                                        <div class="card_vertical_align10">
                                                            <span class="card_text_nazev cerna_barva"><?php echo $row[nazev]; ?></span><br />
                                                            <span class="card_text_procenta "><?php echo $row[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row[hodnoceni]); ?>" alt="obrazek" height="16"/></span>
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="grid_5 ">
                                                    <?php ?>
                                                    <p class="card_text_bez_paddingu cerna_barva text_11px"><span class="bold">Naše recenze (<?php echo echo_date($row[date_doporuceno]); ?>):</span> <?php echo substr(nl2br($row[naserecenze]), 0, 200); ?>...<a href="./formulare.php?id=naserecenze&amp;ad=<?php echo $row[id]; ?>" class="<?php if (!check_mobile()) echo "fancybox fancybox.iframe"; ?>" title="<?php echo $row[nazev]; ?>">[celý text]</a></p>
                                                </div>                                                

                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>                                
                                    <?php
                                    $i++;
                                }
                                ?>
                            </div>                    
                        </div>
                        <div class="clear"></div>   
                    </div>
                    <?php
                } elseif ($_GET[bd] == "top10") {
                    ?>
                    <div id="vyhledavani" class="margin_top1">
                        <div class="wrapper">   
                            <div>                    
                                <?php
                                $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [typ] = %s", $_GET[ad], "AND [zverejnit] = %s", "ano", 'ORDER BY %by', 'hodnoceni', 'DESC');
                                $i = 1;
                                while ($row = $query->fetch()) {
                                    if ($i == 11)
                                        break;
                                    ?>
                                    <div class="card_prehledy70 margin_top3">
                                        <div class="card_vnitrni5">
                                            <div>
                                                <a class="" href="./index.php?id=vyhledavani&amp;ad=<?php echo $_GET[ad]; ?>&amp;bd=profil&amp;cd=<?php echo $row[id]; ?>" title="<?php echo $row[nazev]; ?>">
                                                    <div class="grid_1 card_prehledy_border70_bez_cary">
                                                        <div class="card_vertical_align15 ">
                                                            <span class="card_text_cislo cerna_barva"><?php echo $i; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="grid_2">
                                                        <img class="card_obrazek_podnik100" src="<?php echo get_foto_of_podnik($row[id]); ?>" alt="obrazek"/>
                                                    </div>
                                                    <div class="grid_4 card_prehledy_border70">
                                                        <div class="card_vertical_align10">
                                                            <span class="card_text_nazev cerna_barva"><?php echo $row[nazev]; ?></span><br />
                                                            <span class="card_text_procenta "><?php echo $row[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row[hodnoceni]); ?>" alt="obrazek" height="16"/></span>
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="grid_5 ">
                                                    <p class="card_text_bez_paddingu cerna_barva text_11px"><?php echo substr(nl2br($row[obsah]), 0, 200); ?>...<a href="./formulare.php?id=popis&amp;ad=<?php echo $row[id]; ?>" class="<?php if (!check_mobile()) echo "fancybox fancybox.iframe"; ?>" title="<?php echo $row[nazev]; ?>">[celý popis]</a></p>
                                                </div>                                                

                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>                                
                                    <?php
                                    $i++;
                                }
                                ?>
                            </div>                    
                        </div>
                        <div class="clear"></div>   
                    </div>
                    <?php
                } elseif ($_GET[bd] == "profil") {
                    //prictu banner
                    spocist_banner();
                    ?>
                    <div id="vyhledavani" class="margin_top1">
                        <div class="wrapper">   
                            <div>
                                <div class="grid_3">
                                    <?php
                                    $query = dibi::query("SELECT * FROM [pjh_podnik] WHERE [id] = %i", $_GET[cd], "AND [zverejnit] = %s", "ano");
                                    $row = $query->fetch();

                                    $query_fotky = dibi::query('SELECT * FROM [pjh_fotky] WHERE [id_cil] = %i', $_GET[cd], 'AND [typ] = %s', 'podnik', "AND [zverejnit] = %s", "ano", 'ORDER BY %by', 'poradi', 'ASC');
                                    ?> 
                                    <?php
                                    $pocet_fotek = $query_fotky->count();
                                    $i = 1;
                                    while ($row_fotky = $query_fotky->fetch()) {
                                        if ($i == 1) {
                                            ?> 
                                            <a href="<?php echo $row_fotky[cil]; ?>" class="fancybox-buttons" data-fancybox-group="<?php echo $row[nazev]; ?>" title="<?php echo $row[nazev]; ?> - <?php echo $row_fotky[nazev]; ?>"><img class="width_100" src="<?php echo $row_fotky[cil]; ?>" alt="obrazek"/></a>
                                            <?php
                                        } else {
                                            ?> 
                                            <a href="<?php echo $row_fotky[cil]; ?>" class="fancybox-buttons" data-fancybox-group="<?php echo $row[nazev]; ?>" title="<?php echo $row[nazev]; ?> - <?php echo $row_fotky[nazev]; ?>"><img class="width_32" src="<?php echo $row_fotky[cil]; ?>" alt="obrazek"/></a>
                                            <?php
                                        }
                                        $i++;
                                    }
                                    ?>
                                </div>
                                <div class="grid_6">
                                    <div class="card_bez_ohraniceni">
                                        <div class="card_vnitrni5">
                                            <div>
                                                <span class="card_text_nazev_vetsi"><?php echo $row[nazev]; ?></span><br />
                                                <span class="card_text_procenta_vetsi"><?php echo $row[hodnoceni]; ?>/10 <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row[hodnoceni]) ?>" alt="obrazek" height="18"/></span><br />
                                                <span class="card_text_misto"><?php echo $row[adresa]; ?>,
                                                    <?php
                                                    if ($row[okres] == "HB")
                                                        echo "Havlíčkův Brod";
                                                    elseif ($row[okres] == "P")
                                                        echo "Pelhřimov";
                                                    elseif ($row[okres] == "J")
                                                        echo "Jihlava";
                                                    elseif ($row[okres] == "ZnS")
                                                        echo "Žďár nad Sázavou";
                                                    elseif ($row[okres] == "T")
                                                        echo "Třebíč";
                                                    ?></span><br />
                                                <p class="card_text"><?php echo nl2br($row[obsah]); ?></p>
                                                <div class="clear"></div>
                                            </div>
                                        </div>
                                    </div>   
                                    <div class="delici_horizontalni_cara_mala">RECENZE</div>
                                    <div id="sezazeni">
                                        <a name="hodnoceni"></a>
                                        <ul>
                                            <?php
                                            if (($_GET[order] == "nazevautor" && $_GET[by] == "desc") || $_GET[order] == "") {
                                                $_GET[order] = "nazevautor";
                                                $_GET[by] = "desc";
                                                echo "Seřadit podle: <li class=\"first active\"><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=nazevautor&by=asc#hodnoceni\">Jména autora <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li><li><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=hodnoceni&by=desc#hodnoceni\">Hodnocení</a></li><li><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=date&by=desc#hodnoceni\">Data</a></li>";
                                            } elseif (($_GET[order] == "nazevautor" && $_GET[by] == "asc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first active\"><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=nazevautor&by=desc#hodnoceni\">Jména autora <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li><li><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=hodnoceni&by=desc#hodnoceni\">Hodnocení</a></li><li><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=date&by=desc#hodnoceni\">Data</a></li>";
                                            } elseif (($_GET[order] == "hodnoceni" && $_GET[by] == "desc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=nazevautor&by=desc#hodnoceni\">Jména autora</a></li><li class=\"active\"><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=hodnoceni&by=asc#hodnoceni\">Hodnocení <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li><li><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=date&by=desc#hodnoceni\">Data</a></li>";
                                            } elseif (($_GET[order] == "hodnoceni" && $_GET[by] == "asc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=nazevautor&by=desc#hodnoceni\">Jména autora</a></li><li class=\"active\"><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=hodnoceni&by=desc#hodnoceni\">Hodnocení <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li><li><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=date&by=desc#hodnoceni\">Data</a></li>";
                                            } elseif (($_GET[order] == "date" && $_GET[by] == "desc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=nazevautor&by=desc#hodnoceni\">Jména autora</a></li><li><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=hodnoceni&by=desc#hodnoceni\">Hodnocení</a></li><li class=\"active\"><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=date&by=asc#hodnoceni\">Data <img src=\"img/arrow-down.png\" height=\"10px\"/></a></li>";
                                            } elseif (($_GET[order] == "date" && $_GET[by] == "asc") || $_GET[order] == "") {
                                                echo "Seřadit podle: <li class=\"first\"><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=nazevautor&by=desc#hodnoceni\">Jména autora</a></li><li><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=hodnoceni&by=desc#hodnoceni\">Hodnocení</a></li><li class=\"active\"><a href=\"./index.php?id=vyhledavani&amp;ad=$_GET[ad]&amp;bd=profil&amp;cd=$_GET[cd]&order=date&by=desc#hodnoceni\">Data <img src=\"img/arrow-up.png\" height=\"10px\"/></a></li>";
                                            }
                                            ?>

                                        </ul>
                                        <div class="clear"></div>   
                                    </div>
                                    <?php
                                    $query2 = dibi::query("SELECT * FROM [pjh_recenze] WHERE [id_podnik] = %i", $_GET[cd], "AND [zverejnit] = %s", "ano", "ORDER BY %by", $_GET[order], $_GET[by]);
                                    $i = 1;
                                    while ($row2 = $query2->fetch()) {
                                        ?>
                                        <div class="card_bez_thumbefektu card_prehledy_border60_bez_cary margin_top3">
                                            <div class="card_vnitrni">
                                                <div>
                                                    <div class="grid_2 card_prehledy_border60_bez_cary">
                                                        <div class="card_vertical_align15 ">
                                                            <span class="card_text_cislo_hodnoceni cerna_barva"><?php echo $row2[hodnoceni]; ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="grid_5">
                                                        <div class="recenze_hodnoceni_mala margin_top3">
                                                            <div class="grid_5">
                                                                <?php
                                                                if ($_GET[ad] == "restaurace") {
                                                                    echo "Jídlo";
                                                                } elseif ($_GET[ad] == "kavarny") {
                                                                    echo "Kávy";
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="grid_4">
                                                                <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row2[jidlo]); ?>" alt="obrazek" height="10"/>
                                                            </div>
                                                            <div class="grid_1">
                                                                <?php echo $row2[jidlo]; ?>/10
                                                            </div>
                                                            <div class="grid_1">

                                                            </div>                                        
                                                        </div>
                                                        <div class="recenze_hodnoceni_mala">
                                                            <div class="grid_5">
                                                                Obsluha
                                                            </div>
                                                            <div class="grid_4">
                                                                <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row2[obsluha]); ?>" alt="obrazek" height="10"/>
                                                            </div>
                                                            <div class="grid_1">
                                                                <?php echo $row2[obsluha]; ?>/10
                                                            </div>
                                                            <div class="grid_1">

                                                            </div>
                                                        </div>
                                                        <div class="recenze_hodnoceni_mala">

                                                            <div class="grid_5">
                                                                Prostředí
                                                            </div>
                                                            <div class="grid_4">
                                                                <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row2[prostredi]); ?>" alt="obrazek" height="10"/>
                                                            </div>
                                                            <div class="grid_1">
                                                                <?php echo $row2[prostredi]; ?>/10
                                                            </div>
                                                            <div class="grid_1">

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="grid_5">
                                                        <p class="card_text cerna_barva text_11px"><a href="./formulare.php?id=gurman&amp;ad=<?php echo $row2[id_autor]; ?>" class="<?php if (!check_mobile()) echo "fancybox_iframe_big fancybox.iframe"; ?> bold" title="Gurmán"><?php echo $row2[nazevautor]; ?> <span class="text_11px">(<?php echo echo_date($row2[date]); ?>)</span></a>: <?php echo substr($row2[obsah], 0, 60); ?>... <a href="./formulare.php?id=recenze&amp;ad=<?php echo $row2[id]; ?>" class="<?php if (!check_mobile()) echo "fancybox fancybox.iframe"; ?> recenze_odkaz_normal male" title="<?php echo $row[nazev]; ?>">[celá recenze]</a></p>
                                                    </div>
                                                    <div class="clear"></div>
                                                </div>
                                            </div>
                                        </div> 
                                        <?php
                                        $i++;
                                    }
                                    ?>           
                                </div>
                                <div class="grid_3">
                                    <div class="recenze_hodnoceni">
                                        <div class="grid_5">
                                            <?php
                                            if ($_GET[ad] == "restaurace") {
                                                echo "Jídlo";
                                            } elseif ($_GET[ad] == "kavarny") {
                                                echo "Kávy";
                                            }
                                            ?>
                                        </div>
                                        <div class="grid_5">
                                            <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row[jidlo]); ?>" alt="obrazek" height="18"/>
                                        </div>
                                        <div class="grid_2">
                                            <?php echo $row[jidlo]; ?>/10
                                        </div>
                                    </div>
                                    <div class="recenze_hodnoceni">
                                        <div class="grid_5">
                                            Obsluha
                                        </div>
                                        <div class="grid_5">
                                            <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row[obsluha]); ?>" alt="obrazek" height="18"/>
                                        </div>
                                        <div class="grid_2">
                                            <?php echo $row[obsluha]; ?>/10
                                        </div>
                                    </div>
                                    <div class="recenze_hodnoceni">

                                        <div class="grid_5">
                                            Prostředí
                                        </div>
                                        <div class="grid_5">
                                            <img class="card_obrazek_procent" src="<?php echo get_hveznicky_na_cislo($row[prostredi]); ?>" alt="obrazek" height="18"/>
                                        </div>
                                        <div class="grid_2">
                                            <?php echo $row[prostredi]; ?>/10
                                        </div>
                                    </div>

                                    <div class="recenze_hodnoceni">
                                        <br /><br /><br /><br /><h2 class="bold">Vybrané recenze<h2>
                                                </div>


                                                <?php
                                                $query_recenze = dibi::query("SELECT * FROM [pjh_recenze] WHERE [id_podnik] = %i", $_GET[cd], "AND [zverejnit] = %s", "ano", "ORDER BY %by", "hodnoceni", "desc");
                                                while ($row_recenze = $query_recenze->fetch()) {
                                                    ?>
                                                    <div class="recenze">
                                                        <p class="recenze_text"><?php echo substr(nl2br($row_recenze[obsah]), 0, 400); ?>... <a href="./formulare.php?id=recenze&amp;ad=<?php echo $row_recenze[id]; ?>" class="<?php if (!check_mobile()) echo "fancybox fancybox.iframe"; ?> recenze_odkaz_normal" title="<?php echo $row_recenze[nazevpodniku]; ?>">[celá recenze]</a></p>
                                                        <span class="recenze_autor"><a href="./formulare.php?id=gurman&amp;ad=<?php echo $row_recenze[id_autor]; ?>" class="<?php if (!check_mobile()) echo "fancybox_iframe_big fancybox.iframe"; ?>" title="<?php echo $row_recenze[nazevautor]; ?>"><?php echo $row_recenze[nazevautor]; ?> <span class="text_11px">(<?php echo echo_date($row_recenze[date]); ?>)</span></a></span>
                                                    </div>                                    

                                                    <?php
                                                }
                                                ?>   
                                                </div>
                                                </div>
                                                </div>
                                                </div>
                                                <div class="clear"></div>
                                                <?php
                                            }
                                            ?>

                                            <?php
                                        }
                                    }
                                    ?>
                                    </div>


                                    </div>
                                    </div>
                                    <div id="footer">
                                        <div class="wrapper">
                                            <div class="left">
                                                Copyright © 2014 - <a href="http://ldekonom.cz/">Programování a grafický design LDEkonom.cz</a>
                                            </div>
                                            <div class="right">
                                                <ul>
                                                    <li <?php echo $linksactive[gurmani]; ?>><a href="./index.php?id=gurmani">Gurmáni</a></li>
                                                    <li <?php echo $linksactive[pravidlahodnoceni]; ?>><a href="./index.php?id=pravidlahodnoceni">Pravidla hodnocení</a></li>
                                                    <li <?php echo $linksactive[kontakt]; ?>><a href="./index.php?id=kontakt">Kontakt</a></li>
                                                    <li><a id="scroll-to-top" href="#top">Zpátky nahoru &raquo;</a></li>
                                                </ul>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </div>
                                    </body>
                                    </html>
