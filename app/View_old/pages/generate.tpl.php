<!DOCTYPE html>
<html lang="fr">
<head>
    <?php
    use voku\helper\HtmlMin;
    use WyriHaximus\HtmlCompress\Factory;

    require_once __DIR__ . './../global/head.tpl.php'
    ?>
    <title>Vagerof Prez - <?= $viewData['tv']->name ?></title>
</head>
<body>
<?php require_once __DIR__ . './../global/nav.tpl.php' ?>

<!-- Formulaire de recherche -->
<section class="my-3 container" id="find">
    <?php require_once __DIR__ . './../snippets/search-form.tpl.php' ?>
</section>

<!-- Petit récap de la série -->
<section class="my-3 container">
    <div class="row">
        <div class="col-12">
            <div class="card border rounded border-dark">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8 col-12 d-flex flex-column justify-content-">
                            <!-- Nom de la série/film -->
                            <div class="display-4 font-weight-light align-self-start"><?= $viewData['tv']->name ?></div>
                            <!-- Information série (Saison, Episode, ...) + Catégories -->
                            <div class="mb-4">
                                <?php if (isset($viewData['rendered']->other_season)) echo '<span class="badge badge-pill bg-danger me-1">' . $viewData['rendered']->other_season . '</span>'; ?>
                                <?php if (isset($viewData['rendered']->other_episode)) echo '<span class="badge badge-pill bg-danger me-1">' . $viewData['rendered']->other_episode . '</span>'; ?>
                                <?php foreach ($viewData['tv']->genres as $genre) echo '<span class="badge badge-pill bg-dark me-2">' . $genre->name . '</span>'; ?>
                            </div>

                            <!-- Synopsis -->
                            <div class="text-justify mb-4">
                                <span style="font-size: small; color: #999999; "><?= $viewData['tv']->overview ?></span>
                            </div>

                            <!-- Affichage des 4 acteurs principaux -->
                            <div class="row <?= (sizeof($viewData['generator']['casts']) < 5) ? 'justify-content-start' : 'justify-content-center text-center' ?> mb-4">
                                <?php
                                foreach ($viewData['generator']['casts'] as $perso)
                                    echo '<div class="col-6 col-sm-3 mb-3 px-2"><img src="https://image.tmdb.org/t/p/w500' . $perso['profile_path'] . '" alt="' . $perso['original_name'] . '" class="rounded img-fluid"></div>';
                                ?>
                            </div>

                            <!-- Bandeau d'informations (Format, Codec, Note, Année) -->
                            <div class="d-flex align-items-center justify-content-center p-1 px-2 mb-3 bg-light border-dark rounded ">
                                <?php if (isset($viewData['rendered']->quality)) echo '<span class="badge bg-info px-3">' . $viewData['rendered']->quality . '</span>'; ?>
                                <span class="badge bg-success px-3 mx-1">x264</span>
                                <img class="mx-auto d-none d-sm-inline-block"
                                     src="<?= $viewData['generator']['rating']['image'] ?>"
                                     alt="<?= $viewData['generator']['rating']['note'] ?>">
                                <span class="alert alert-dark d-none d-sm-inline-block h5 py-2 px-3 mb-0"><?= $viewData['generator']['release']->format("Y") ?></span>
                            </div>
                        </div>
                        <!-- Affichage et changement de l'image de couverture -->
                        <div class="col-md-4 ">
                            <img
                                src="https://image.tmdb.org/t/p/w500<?= $viewData['tv']->poster_path ?>"
                                alt=""
                                class="img-fluid rounded w-10"
                                data-rendered-cover=""
                            >
                            <button type="button" class="btn btn-outline-dark w-100 mt-3" data-bs-toggle="modal"
                                    data-bs-target="#posters">
                                Changer l'image de couverture
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Formulaire de rendu -->
<section class="my-3 container" id="container_fillform">
    <form action="" method="POST">
        <input type="hidden" name="cover_url" value="https://image.tmdb.org/t/p/w500<?= $viewData['tv']->poster_path ?>">
        
        <?php
        if ($viewData['isTv']) {
            ?>
            <div class="card mt-3 bg-dark text-light">
                <div class="card-header fst-italic fw-bold">Informations de la série</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-12 mb-4">
                            <div class="form-group">
                                <label for="other_season">Saison</label>
                                <input type="text" class="form-control" id="other_season" name="other_season"
                                       placeholder="01 / Intégrale">
                            </div>
                        </div>
                        <div class="col-md-6 col-12 mb-4">
                            <div class="form-group">
                                <label for="other_episode">Episode</label>
                                <input type="text" class="form-control" id="other_episode" name="other_episode">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>

        <!-- Informations du torrent -->
        <div class="card mt-3 bg-light text-dark">
            <div class="card-header fst-italic fw-bold">Informations du torrent</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-12 mb-4">
                        <div class="form-group">
                            <label for="quality">Qualité</label>
                            <select class="form-select" id="quality" name="quality">
                                <optgroup label="HDTV" class="text-muted">
                                    <option class="text-dark" value="TVRip">TVRip</option>
                                    <option class="text-dark" value="HDTV 720p">HDTV 720p</option>
                                    <option class="text-dark" value="HDTV 1080p">HDTV 1080p</option>
                                    <option class="text-dark" value="HDTV 2160p">HDTV 2160p</option>
                                    <option class="text-dark" value="VCD/SVCD/VHSRip">VCD/SVCD/VHSrip</option>
                                </optgroup>
                                <optgroup label="WEB" class="text-muted">
                                    <option class="text-dark" value="WEB">WEB</option>
                                    <option class="text-dark" value="WEB 720p">WEB 720p</option>
                                    <option class="text-dark" value="WEB 1080p">WEB 1080p</option>
                                    <option class="text-dark" value="WEB 2160p">WEB 2160p</option>
                                </optgroup>
                                <optgroup label="WEBRip" class="text-muted">
                                    <option class="text-dark" value="WEBRip">WEBRip</option>
                                    <option class="text-dark" value="WEBRip 720p">WEBRip 720p</option>
                                    <option class="text-dark" value="WEBRip 1080p">WEBRip 1080p</option>
                                    <option class="text-dark" value="WEBRip 2160p">WEBRip 2160p</option>
                                </optgroup>
                                <optgroup label="DVD" class="text-muted">
                                    <option class="text-dark" value="DVDrip">DVDrip</option>
                                    <option class="text-dark" value="DVD [Full]">DVD [Full]</option>
                                    <option class="text-dark" value="DVD [Remux]">DVD [Remux]</option>
                                </optgroup>
                                <optgroup label="BLURAY" class="text-muted">
                                    <option class="text-dark" value="BDrip/BRrip (SD)">BDrip/BRrip (SD)</option>
                                    <option class="text-dark" value="HDrip 720p">HDrip 720p</option>
                                    <option class="text-dark" value="HDrip 1080p">HDrip 1080p</option>
                                    <option class="text-dark" value="HDrip 2160p">HDrip 2160p</option>
                                    <option class="text-dark" value="Bluray [Full]">Bluray [Full]</option>
                                    <option class="text-dark" value="Bluray [Remux]">Bluray [Remux]</option>
                                    <option class="text-dark" value="Bluray 4K [Full]">Bluray 4K [Full]</option>
                                    <option class="text-dark" value="Bluray 4K [Remux]">Bluray 4K [Remux]</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-12 mb-4">
                        <div class="form-group">
                            <label for="source">Source / Release</label>
                            <input type="text" class="form-control" id="source" name="source"
                                   placeholder="AMZN/DSNP/NF.<TEAM>"/>
                        </div>
                    </div>

                    <div class="col-md-4 col-12 mb-4">
                        <div class="form-group">
                            <label for="video_format">Format vidéo</label>
                            <select class="form-select" id="video_format" name="video_format">
                                <option value="MKV">MKV</option>
                                <option value="MP4">MP4</option>
                                <option value="M2TS">M2TS</option>
                                <option disabled="" class="text-muted">------</option>
                                <option value="AVI">AVI</option>
                                <option value="MOV">MOV</option>
                                <option disabled="" class="text-muted">------</option>
                                <option value="IMG">IMG</option>
                                <option value="ISO">ISO</option>
                                <option value="VOB">VOB</option>
                                <option value="TS">TS</option>
                                <option disabled="" class="text-muted">------</option>
                                <option value="WebM">WebM</option>
                                <option value="OGG">OGG</option>
                                <option value="3GP">3GP</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 mb-4">
                        <div class="form-group">
                            <label for="video_codec">Codec vidéo</label>
                            <select class="form-select" id="video_codec" name="video_codec">
                                <option value="AVC/x264/h.264">AVC/x264/h.264</option>
                                <option value="HEVC/x265/h.265">HEVC/x265/h.265</option>
                                <option value="HEVC/x265/h.265 (10bit)">HEVC/x265/h.265 (10bit)</option>
                                <option value="VVC/x266/h.266">VVC/x266/h.266</option>
                                <option value="MPEG5">EVC/MPEG5</option>
                                <option disabled="" class="text-muted">------</option>
                                <option value="DivX">DivX</option>
                                <option value="XviD">XviD</option>
                                <option value="MPEG1">MPEG1 (VCD)</option>
                                <option value="MPEG2">MPEG2 (SVCD)</option>
                                <option disabled="" class="text-muted">------</option>
                                <option value="VP8">VP8</option>
                                <option value="VP9">VP9</option>
                                <option value="VC1">VC1</option>
                                <option value="AV1">AV1</option>
                                <option disabled="" class="text-muted">------</option>
                                <option value="Theora">Theora</option>
                                <option value="ProRes">ProRes</option>
                                <option value="VOB">VOB</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 mb-4">
                        <div class="form-group">
                            <label for="video_debit">Débit vidéo</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="video_debit" name="video_debit"
                                       placeholder="2500"/>
                                <span class="input-group-text">kb/s</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pistes audio -->
        <div class="card mt-3 bg-dark text-light border-dark">
            <div class="card-header fst-italic fw-bold">Pistes audio</div>
            <div class="card-body text-dark">
                <div id="container_audio">
                    <input type="hidden" name="audio" id="audio" value="1">
                    <div class="row" id="audio_1_form">
                        <!-- Langue -->
                        <div class="col-md-3 col-12 mb-4">
                            <div class="form-group">
                                <label for="audio_1_lang" class="text-white">Langue</label>
                                <select class="form-select" id="audio_1_lang" name="audio_1_lang">
                                    <option value="Français (VFF)">Français (VFF)</option>
                                    <option value="Québécois (VFQ)">Québécois (VFQ)</option>
                                    <option value="Français (VFI)">Français (VFI)</option>
                                    <option value="Anglais">Anglais</option>
                                    <option value="Espagnol">Espagnol</option>
                                    <option value="Allemand">Allemand</option>
                                    <option value="Italien">Italien</option>
                                    <option value="Portugais">Portugais</option>
                                    <option value="Japonais">Japonais</option>
                                    <option value="Coréen">Portugais</option>
                                    <option value="Russe">Russe</option>
                                    <option value="Arabe">Arabe</option>
                                    <option value="Néerlandais">Néerlandais</option>
                                    <option value="Danois"> Danois"> Danois</option>
                                    <option value="Chinois"> Chinois"> Chinois</option>
                                </select>
                            </div>
                        </div>
                        <!-- Codec -->
                        <div class="col-md-3 col-12 mb-4">
                            <div class="form-group">
                                <label for="audio_1_codec" class="text-white">Codec audio</label>
                                <select class="form-select" id="audio_1_codec" name="audio_1_codec">
                                    <option value="AC3">AC3</option>
                                    <option value="EAC3">EAC3</option>
                                    <option disabled="" class="text-muted">------</option>
                                    <option value="AAC">AAC</option>
                                    <option value="AAC-LC">AAC-LC</option>
                                    <option value="HE-AAC">HE-AAC</option>
                                    <option disabled="" class="text-muted">------</option>
                                    <option value="Dolby Digital">Dolby Digital</option>
                                    <option value="Dolby Digital Plus">Dolby Digital Plus</option>
                                    <option value="Dolby TrueHD">Dolby TrueHD</option>
                                    <option disabled="" class="text-muted">------</option>
                                    <option value="DTS">DTS</option>
                                    <option value="DTS HD">DTS HD</option>
                                    <option value="DTS HDMA">DTS HDMA</option>
                                    <option disabled="" class="text-muted">------</option>
                                    <option value="PCM">PCM</option>
                                    <option value="LPCM">LPCM</option>
                                    <option disabled="" class="text-muted">------</option>
                                    <option value="OGG">OGG</option>
                                    <option value="WAV">WAV</option>
                                    <option value="FLAC">FLAC</option>
                                </select>
                            </div>
                        </div>
                        <!-- Pistes -->
                        <div class="col-md-2 col-12 mb-4">
                            <div class="form-group">
                                <label for="audio_1_piste" class="text-white">Pistes</label>
                                <select class="form-select" id="audio_1_piste" name="audio_1_piste">
                                    <option value="mono">1.0 (Mono)</option>
                                    <option value="2.0">2.0 (Stéréo)</option>
                                    <option value="5.1" selected="">5.1 (6CH)</option>
                                    <option value="7.1">7.1 (8CH)</option>
                                </select>
                            </div>
                        </div>
                        <!-- Débit -->
                        <div class="col-md-4 col-12 mb-4">
                            <div class="form-group">
                                <label for="audio_1_debit" class="text-white" >Débit audio</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="audio_1_debit" name="audio_1_debit" placeholder="2500"/>
                                    <span class="input-group-text">kb/s</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Ajouter une piste audio -->
                <div class="col-12 text-center">
                    <input type="button" class="btn btn-outline-light" value="Ajouter une piste audio"
                           id="audio_clone_btn">
                </div>
            </div>
        </div>

        <!-- Sous-titres -->
        <div class="card mt-3 bg-light text-dark">
            <div class="card-header fst-italic fw-bold">Sous-titres</div>
            <div class="card-body">
                <div id="container_txt">
                    <input type="hidden" name="txt" id="txt" value="1">
                    <div class="row" id="txt_1_form">
                        <div class="col-md-6 col-12 mb-4">
                            <div class="form-group">
                                <label for="txt_1_lang">Langue</label>
                                <select class="form-select" id="txt_1_lang" name="txt_1_lang">
                                    <option value="Français (VFF)">Français (VFF)</option>
                                    <option value="Québécois (VFQ)">Québécois (VFQ)</option>
                                    <option value="Français (VFI)">Français (VFI)</option>
                                    <option value="Anglais">Anglais</option>
                                    <option value="Espagnol">Espagnol</option>
                                    <option value="Allemand">Allemand</option>
                                    <option value="Italien">Italien</option>
                                    <option value="Portugais">Portugais</option>
                                    <option value="Japonais">Japonais</option>
                                    <option value="Coréen">Portugais</option>
                                    <option value="Russe">Russe</option>
                                    <option value="Arabe">Arabe</option>
                                    <option value="Néerlandais">Néerlandais</option>
                                    <option value="Danois"> Danois"> Danois</option>
                                    <option value="Chinois"> Chinois"> Chinois</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-12 mb-4">
                            <div class="form-group">
                                <label for="txt_1_format">Format</label>
                                <select class="form-select" id="txt_1_format" name="txt_1_format">
                                    <option value="Text/SRT (forced)">Text/SRT (forced)</option>
                                    <option value="Text/SRT (full)">Text/SRT (Complets)</option>
                                    <option value="Text/SRT (SDH)">Text/SRT (SDH)</option>
                                    <option disabled="" class="text-muted">------</option>
                                    <option value="Text/ASS (forced)">Text/ASS (forced)</option>
                                    <option value="Text/ASS (full)">Text/ASS (Complets)</option>
                                    <option value="Text/ASS (SDH)">Text/ASS (SDH)</option>
                                    <option disabled="" class="text-muted">------</option>
                                    <option value="Text/Timed Text (forced)">Text/Timed Text (forced)</option>
                                    <option value="Text/Timed Text (Complets)">Text/Timed Text (Complets)</option>
                                    <option value="Text/Timed Text (SDH)">Text/Timed Text (SDH)</option>
                                    <option disabled="" class="text-muted">------</option>
                                    <option value="VobSub (forced)">VobSub (forced)</option>
                                    <option value="VobSub (full)">VobSub (Complets)</option>
                                    <option value="VobSub (SDH)">VobSub (SDH)</option>
                                    <option disabled="" class="text-muted">------</option>
                                    <option value="PGS (forced)">PGS (forced)</option>
                                    <option value="PGS (full)">PGS (Complets)</option>
                                    <option value="PGS (SDH)">PGS (SDH)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center">
                    <input type="button" class="btn btn-outline-dark" value="Ajouter des sous-titres"
                           id="txt_clone_btn" name="txt_clone_btn">
                </div>
            </div>
        </div>

        <!-- Informations global -->
        <div class="card mt-3 bg-dark text-light border-dark">
            <div class="card-header fst-italic fw-bold">Informations Globals</div>
            <div class="card-body text-dark">
                <div class="row" id="global_form">
                    <div class="col-md-4 col-12 mb-4">
                        <div class="form-group">
                            <label for="global_debit">Débit global</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="global_debit" name="global_debit"
                                       placeholder="2500"/>
                                <span class="input-group-text">kb/s</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-12 mb-4">
                        <div class="form-group">
                            <label for="global_filescount">Nombre de fichiers</label>
                            <input type="number" class="form-control" id="global_filescount"
                                   name="global_filescount"
                                   placeholder="1">
                        </div>
                    </div>
                    <div class="col-md-4 col-12 mb-4">
                        <div class="form-group">
                            <label for="global_size">Poids total</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="global_size" name="global_size"
                                       placeholder="2,5"/>
                                <select class="input-group-text" id="global_sizeunit" name="global_sizeunit">
                                    <option value="Kb">Kb</option>
                                    <option value="Mo">Mo</option>
                                    <option value="Go" selected="">Go</option>
                                    <option value="To">To</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Bouton valider -->
        <div class="row">
            <div class="col-12 text-center">
                <input type="submit" class="btn btn-lg btn-success mt-3" value="Mettre à jour le rendu">
                <!--                <button class="btn btn-primary btn-lg mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#generated_view" aria-expanded="false" aria-controls="generated_view">-->
                <!--                    Afficher le rendu-->
                <!--                </button>-->
                <a class="btn btn-lg btn-outline-dark mt-3"
                   onclick="copyDivToClipboard('textarea.form-control.copy-code')">Copier le code</a>
                <?php
                $htmlMin = new HtmlMin();
                $htmlMin->useKeepBrokenHtml(true);
                $htmlMin->doSortCssClassNames(true);
                $htmlMin->doRemoveOmittedQuotes(false);

                $parser = Factory::constructSmallest()->withHtmlMin($htmlMin);

                ob_start();
                include(__DIR__ . './../snippets/render-tv.tpl.php');
                $tvContent = ob_get_clean();

                $compressedHtml = $parser->compress($tvContent);
                ?>
            </div>
            <?php
            if (isset($viewData['rendered'])) {
                ?>
                <div class="col-12 mt-3">
                    <textarea class="form-control copy-code" value=<?= $compressedHtml ?>></textarea>
                </div>
            <?php } ?>
        </div>
    </form>
</section>

<!-- Modal pour changer l'image de couverture du rendu -->
<div class="modal fade" id="posters" tabindex="-1" aria-labelledby="postersLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdrop">Liste des posters disponibles</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Cliquez sur une image de couverture pour changer le rendu de votre présentation.</p>
                <div class="row" id="posters_container">
                    <?php
                    $i = 0;
                    foreach ($viewData['images']->posters as $img) {
                        ?>

                        <div class="col-3 p-2">
                            <img data-cover="" src="https://image.tmdb.org/t/p/w500<?= $img->file_path ?>"
                                 alt="<?= $img->iso_639_1 ?>"
                                 class="img-fluid rounded"
                                 style="
                                    object-fit: cover;
                                    height: 100%;
                                    width: 100%;
                                    cursor: pointer;
                                "
                            >
                        </div>
                    <?php
                        $i++;
                        if($i == 24) {
                            sleep(1);
                            $i = 0;
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . './../global/footer.tpl.php' ?>
</body>
</html>