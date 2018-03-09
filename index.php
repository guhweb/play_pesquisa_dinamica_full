<?php

require_once __DIR__ . '/Config.inc.php';

$read = new \CRUD\Read;

$post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

$what = strip_tags(trim($post['what']));
$where = strip_tags(trim($post['where']));

?><!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Pesquisa Dinâmica</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700">
    <link rel="stylesheet" href="Theme/_css/fonticon.css">
    <link rel="stylesheet" href="Theme/_css/style.css">
</head>
<body>

<header class="main_header">
    <div class="main_header_content">
        <div class="main_header_search">
            <h1>Sistema de Pesquisa:</h1>

            <form action="index.php?action=search" method="post" class="main_header_search_form">
                <input type="text" name="what" placeholder="O que você quer hoje? Restaurante? Bar? Pizzaria?" value="<?= (!empty($what) ? $what : ""); ?>">
                <input list="cities" type="text" name="where" placeholder="Onde você está?" value="<?= (!empty($where) ? $where : ""); ?>">

                <datalist id="cities">
                    <?php
                    $read->read('cities');
                    if($read->getResult()){
                        foreach($read->getResult() as $opt){
                            echo "<option value='{$opt['city_name']}'>{$opt['city_name']}";
                        }
                    }
                    ?>
                </datalist>
                <button class="icon-search icon-notext"></button>
            </form>
        </div>
    </div>
</header>

<main>

    <?php

    if (!empty($post)) {

        // VALIDATE FORM
        if (!empty($what) && !empty($where)) {

            $read->readFull("SELECT * FROM cities WHERE city_name LIKE '%{$where}%'");
            $city = ($read->getResult() ? (object) $read->getResult()[0] : null);

            $read->readFull("SELECT * FROM companies 
                              INNER JOIN cities ON city_id = company_city 
                              WHERE company_city = :city AND company_tags LIKE '%{$what}%'", "city={$city->city_id}");
            $companies = ($read->getResult() ? $read->getResult() : null);

        } elseif (!empty($what)) {

            $read->readFull("SELECT * FROM companies 
                              INNER JOIN cities ON city_id = company_city 
                              WHERE company_tags LIKE '%{$what}%'");
            $companies = ($read->getResult() ? $read->getResult() : null);

        } elseif (!empty($where)) {

            $read->readFull("SELECT * FROM cities WHERE city_name LIKE '%{$where}%'");
            $city = ($read->getResult() ? (object) $read->getResult()[0] : null);

            $read->readFull("SELECT * FROM companies 
                              INNER JOIN cities ON city_id = company_city 
                              WHERE company_city = :city", "city={$city->city_id}");
            $companies = ($read->getResult() ? $read->getResult() : null);

        }






        // VERIFICAÇÃO DE REGISTROS DA CONSULTA
        if (!empty($companies)) {
            ?>

            <section class="main_list">
                <div class="main_list_content">

                    <header class="main_list_header">
                        <h1>Você procurou por <?= (!empty($what) ? mb_strtoupper($what) : "QUALQUER COISA"); ?> em <?= (!empty($where) ? mb_strtoupper($where) : "QUALQUER LUGAR"); ?>:</h1>
                    </header>

                    <?php
                    foreach ($companies as $company) {

                        $company = (object) $company;
                        ?>

                        <article class="main_list_item">
                            <header>
                                <h2><?= $company->company_name; ?></h2>
                            </header>

                            <p class="icon-address-book">Endereço: <?= $company->company_location; ?></p>
                            <p class="icon-phone">Telefone: <?= $company->company_phone; ?></p>
                        </article>

                        <?php

                    }
                    ?>

                </div>
            </section>

            <?php
        } else {
            echo "<p style='text-align: center; padding: 40px 20px;'>NÃO ENCONTRAMOS REGISTROS PARA OS TERMOS PESQUISADOS!</p>";
        }
    }

    ?>

    <p style="text-align: center; padding: 40px 20px;">CONTEÚDO DO SITE</p>
</main>
</body>
</html>