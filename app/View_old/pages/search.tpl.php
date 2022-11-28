<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . './../global/head.tpl.php' ?>
    <title>Vagerof Prez - Recherche de "<?= $viewData['data_url']['search'] ?? "" ?>"</title>
</head>
<body>
<?php require_once __DIR__ . './../global/nav.tpl.php' ?>

<section class="my-3 container" id="find">
    <?php require_once __DIR__ . './../snippets/search-form.tpl.php' ?>
</section>

<section class="my-3 container" id="list">
    <div class="card bg-secondary my-3" id="establishment">
        <div class="card-body">
            <div class="row d-flex flex-wrap">

                <?php
                if (isset($viewData['list'])) {
                    foreach ($viewData['list'] as $data) {
                        ?>

                        <div class="col-12 col-lg-3 col-sm-4 mb-3">
                            <?php
                            $release = null;
                            $title = null;
                            $class = null;
                            $url = null;
                            if ($viewData['data_url']['type'] == "tv") {
                                $title = $data->original_name ?? "N/A";
                                $release = $data->first_air_date ?? "N/A";
                                $class = "border-info";
                                $url = $viewData['router']->get()->generate('main-generate-tv', ['id' => $data->id]);
                            }
                            if ($viewData['data_url']['type'] == "movie") {
                                $title = $data->original_title ?? "N/A";
                                $release = $data->release_date ?? "N/A";
                                $class = "border-success";
                                $url = $viewData['router']->get()->generate('main-generate-movie', ['id' => $data->id]);
                            }
                            if ($viewData['data_url']['type'] == "multi") {
                                $title = $data->media_type == "tv" ? $data->original_name ?? "N/A" : $data->original_title ?? "N/A";
                                $release = $data->media_type == "tv" ? $data->first_air_date ?? "N/A" : ($data->release_date ?? "N/A");
                                if ($data->media_type == "tv" || $data->media_type == "movie")
                                    $url = $viewData['router']->get()->generate("main-generate-{$data->media_type}", ['id' => $data->id]);
                                else
                                    $url = "#";
                            }
                            ?>
                            <div class="card h-100 <?= $class ?>">
                                <img src="https://image.tmdb.org/t/p/w300_and_h450_bestv2<?= $data->poster_path ?? $data->backdrop_path ?>"
                                     class="card-img-top img-fluid "
                                     style="width: 100%; height: 450px !important; object-fit: cover;" alt="">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="<?= $url ?>">
                                            <?= $title ?>
                                        </a>
                                    </h5>
                                    <h6 class="card-subtitle text-muted"><?= $release ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php }

                } else {
                    ?>
                    <div class="col-12 col-sm-12 col-lg-12 mt-3">
                        <div class="alert alert-warning">Aucun résultats</div>
                    </div>
                    <?php
                } ?>

                <?php
                if (isset($viewData['pagination']) && $viewData['pagination']['total_pages'] > 1) {
                    $a = $viewData['pagination']['pages'];
                    $c = $viewData['pagination']['page'];
                    ?>
                    <div class="d-flex justify-content-center mt-3">
                        <ul class="pagination pagination-lg">
                            <li class="page-item <?= $a->$c->prev ? "" : "disabled" ?>">
                                <a class="page-link" href="<?= $a->$c->prev->url ?? "" ?>">&laquo;</a>
                            </li>
                            <?php
                            foreach ($viewData['pagination']['pages'] as $page) {
                                ?>
                                <li class="page-item <?= $page->current ? "active" : "" ?>">
                                    <a class="page-link" href="<?= $page->url ?>"><?= $page->page ?></a>
                                </li>
                            <?php } ?>
                            <li class="page-item <?= $a->$c->next ? "" : "disabled" ?>">
                                <a class="page-link" href="<?= $a->$c->next->url ?? "" ?>">&raquo;</a>
                            </li>
                        </ul>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . './../global/footer.tpl.php' ?>
</body>
</html>