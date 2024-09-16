<?php
/* ============================ */
/*         SalesMan CRM         */
/* ============================ */
/* (C) 2016 Vladislav Andreev   */
/*       SalesMan Project       */
/*        www.isaler.ru         */
/*        ver. 2017.x           */
/* ============================ */

error_reporting( E_ERROR );

global $rootpath;
require_once $rootpath."/inc/head.card.php";
flush();

$settings              = $db -> getOne( "SELECT settings FROM ".$sqlname."modcatalog_set WHERE identity = '$identity'" );
$settings              = json_decode( (string)$settings, true );
$settings[ 'mcSklad' ] = 'yes';

if ( $settings[ 'mcSkladPoz' ] != "yes" ) $pozzi = " and status != 'out'";

$n_id   = (int)$_REQUEST[ 'n_id' ];
$action = $_REQUEST[ 'action' ];

//---названия полей прайса. start---//
$dname  = $dvar = $don = [];
$result = $db -> getAll( "SELECT * FROM ".$sqlname."field WHERE fld_tip='price' AND fld_on='yes' and identity = '$identity' ORDER BY fld_order" );
foreach ( $result as $data ) {

    $dname[ $data[ 'fld_name' ] ] = $data[ 'fld_title' ];
    $dvar[ $data[ 'fld_name' ] ]  = $data[ 'fld_var' ];
    $don[]                        = $data[ 'fld_name' ];

}
//---названия полей прайса. end---//

$state  = [
    '0' => 'Нет в наличии',
    '1' => 'Можно заказать',
    '2' => 'Приобретен',
    '3' => 'В наличии',
    '4' => 'Нет в наличии'
];
$colors = [
    '0' => 'gray',
    '1' => 'broun',
    '2' => 'идгу',
    '3' => 'green',
    '4' => 'red'
];

$result   = $db -> getRow( "SELECT * FROM ".$sqlname."price WHERE n_id='".$n_id."' and identity = '$identity'" );
$artikul  = $result[ "artikul" ];
$title    = $result[ "title" ];
$descr    = $result[ "descr" ];
$datum    = $result[ "datum" ];
$price_in = $result[ "price_in" ];
$price_1  = $result[ "price_1" ];
$price_2  = $result[ "price_2" ];
$price_3  = $result[ "price_3" ];
$price_4  = $result[ "price_4" ];
$price_5  = $result[ "price_5" ];
$nds      = $result[ "nds" ];
$edizm    = $result[ "edizm" ];

$result  = $db -> getRow( "select * from ".$sqlname."modcatalog where prid='".$n_id."' and identity = '$identity'" );
$content = htmlspecialchars_decode( $result[ "content" ] );
$status  = $result[ "status" ];
$kol     = $result[ "kol" ];
$id      = $result[ "id" ];
$file    = $result[ "files" ];
$sklad   = $result[ "sklad" ];

if ( $kol == '' ) $kol = '?';

?>
<div class="fixx">

    <DIV id="head">
        <DIV id="ctitle">
            <div class="back2menu"><a href="index.php" title="Рабочий стол"><i class="icon-home"></i></a></div>
            <span class="blue">Каталог:</span>&nbsp;<b><span class="elipsis"><?= $title ?></span></b>
            <input type="hidden" name="isCard" id="isCard" value="yes">
            <DIV id="close" onclick="window.close();">Закрыть</DIV>
        </DIV>
    </DIV>
    <DIV id="dtabs">
        <UL>
            <LI class="current" id="tb0" onclick="settab('0')"><A href="javascript:void(0)">Информация</A></LI>
        </UL>
    </DIV>

</div>
<DIV class="fixbg"></DIV>

<DIV id="telo">
    <DIV class="leftcol" id="tab-1">
        <fieldset>

            <legend>Описание</legend>

            <DIV class="batton-edit pb10">

                <div class="inline relativ">

                    <a href="javascript:void(0)" class="tagsmenuToggler" title="Действия"><b class="blue">Действия</b>&nbsp;<i class="icon-angle-down" id="mapi"></i></a>

                    <div class="tagsmenu toright hidden">

                        <div class="items noBold fs-09">

                            <div onclick="configpage()" class="item ha hand" title="Обновить">
                                <i class="icon-arrows-cw blue"></i>&nbsp;Обновить информацию
                            </div>
                            <?php
                            if ( in_array( $iduser1, (array)$settings[ 'mcCoordinator' ] ) ) {
                                ?>
                                <div onclick="doLoad('/modules/modcatalog/form.modcatalog.php?n_id=<?= $n_id ?>&action=edit');" class="item ha hand" title="Изменить">
                                    <i class="icon-pencil broun"></i>&nbsp;Изменить позицию
                                </div>
                            <?php } ?>

                        </div>

                    </div>

                </div>

            </DIV>

            <DIV id="info"></DIV>

        </fieldset>
    </DIV>
    <DIV class="rightcol pt15" id="tab-0">

        <div id="clientMore" class="ftabs" data-id="container">

            <div id="ytabs">

                <ul class="gray flex-container blue">

                    <li class="flex-string" data-link="sklad">Наличие</li>
                    <li class="flex-string" data-link="deals">Сделки</li>
                    <?php if ( $setEntry[ 'enShowButtonLeft' ] == 'yes' && $isEntry == 'on' ) { ?>
                        <li class="flex-string" data-link="entry">Обращения</li>
                    <?php } ?>
                    <li class="flex-string" data-link="drive">Движение</li>
                    <li class="flex-string" data-link="log">Лог</li>
                    <li class="flex-string" data-link="file">Файлы</li>

                </ul>

            </div>
            <div id="container" class="fcontainer">

                <div class="sklad cbox">

                    <DIV class="batton-edit">
                        <a href="javascript:void(0)" onclick="sklad()"><i class="icon-arrows-cw"></i>&nbsp;Обновить</a>
                    </DIV>
                    <br/>
                    <DIV id="sklad" class="viewdiv1 bgwhite1"></DIV>

                </div>
                <div class="deals cbox">

                    <DIV class="batton-edit">
                        <a href="javascript:void(0)" onclick="dogs()"><i class="icon-arrows-cw"></i>&nbsp;Обновить</a>
                    </DIV>
                    <br/>
                    <DIV id="tab0" class="viewdiv1 bgwhite1"></DIV>

                </div>
                <?php if ( $setEntry[ 'enShowButtonLeft' ] == 'yes' && $isEntry == 'on' ) { ?>
                    <div class="entry cbox">

                        <DIV class="batton-edit">
                            <a href="javascript:void(0)" onclick="entrys()"><i class="icon-arrows-cw"></i>&nbsp;Обновить</a>
                        </DIV>
                        <br/>
                        <DIV id="entry"></DIV>

                    </div>
                <?php } ?>
                <div class="log cbox">

                    <DIV class="batton-edit">
                        <a href="javascript:void(0)" onclick="logs()"><i class="icon-arrows-cw"></i>&nbsp;Обновить</a>
                    </DIV>
                    <br/>
                    <DIV id="log"></DIV>

                </div>
                <div class="drive cbox">

                    <DIV class="batton-edit">
                        <a href="javascript:void(0)" onclick="drive()"><i class="icon-arrows-cw"></i>&nbsp;Обновить</a>
                    </DIV>
                    <br/>
                    <DIV id="drive"></DIV>

                </div>

                <div class="file cbox">
                    <DIV id="file"> tsdas</DIV>
                </div>

            </div>

        </div>

    </DIV>
</DIV>

<DIV style="height:50px; display:inline-block; width:99%;"></DIV>

<script>

    //устанавливаем переменную
    //что мы в карточке
    isCard = true;

    //признак того, что открыт фрейм
    isFrame = <?=( $_REQUEST[ 'face' ] ) ? 'true' : 'false';?>;

    $('.ftabs').each(function () {

        $(this).find('li').removeClass('active');
        $(this).find('li:first-child').addClass('active');

        $(this).find('.cbox').addClass('hidden');
        $(this).find('.cbox:first-child').removeClass('hidden');


    });

    var fileDataJson = "";

    function changeCrmSort(field){
        var element = $('#x-' + field);

        if(element){
            var icon = element.find('i');


            if(icon.hasClass('icon-angle-down')){
                var sort = 'asc';
            }
            else {
                var sort = 'desc';
            }

            //console.log(sort);
            // console.log(field);

            console.log(fileDataJson);


            createTableFiles(fileDataJson,{field:field,sort:sort});
        }
    }

    function createTableFiles(data,sortObj = null){

        let dateSortClass = 'icon-angle-down';
        let titleSortClass = 'icon-angle-down';
        let sizeSortClass = 'icon-angle-down';

        console.log('tytut')

        if(sortObj){
            data.sort((a,b) => {
                let sortField = sortObj.field

                let valA = a[sortField];
                let valB = b[sortField];

                if(sortObj.sort === 'asc'){
                    if(valA > valB){
                        return 1;
                    }
                    else if(valA < valB){
                        return -1;
                    }
                    else {
                        return 0;
                    }
                }
                else{
                    if(valA > valB){
                        return -1;
                    }
                    else if(valA < valB){
                        return 1;
                    }
                    else {
                        return 0;
                    }
                }
            });

            switch (sortObj.field){
                case 'datum':
                    dateSortClass = sortObj.sort === 'asc'? 'icon-angle-up':'icon-angle-down'
                    break;
                case 'file_name':
                    titleSortClass = sortObj.sort  === 'asc'? 'icon-angle-up':'icon-angle-down';
                    break;
                case 'size':
                    sizeSortClass = sortObj.sort  === 'asc'? 'icon-angle-up':'icon-angle-down'
                    break;
            }
        }

        let html = '';
        html += '<table>';
        html += '<thead><tr>';
        html += '<th></th>';
        html += '<th class="text-left"><div class="ellipsis hand" id="x-datum"   onclick="changeCrmSort(\'datum\')" title="Изменить порядок вывода">Дата<i class="'+dateSortClass+'"></i></div></th>';
        html += '<th class="text-left"><div class="ellipsis hand" id="x-file_name"   onclick="changeCrmSort(\'file_name\')" title="Изменить порядок вывода">Название<i class="'+titleSortClass+'"></i></div></th>';
        html += '<th>Описание</th>';
        // html += '<th class="text-left"><div class="ellipsis hand" onclick="changeCrmSort(\'size\')" title="Изменить порядок вывода">Размер<i class="'+sizeSortClass+'"></i></div></th>';
        html += '</tr></thead>';
        html += '<tbody>';

        for (var i = 0; i < data.length; i++) {
            const description = data[i].ftag === null ? "" : data[i].ftag;

            let snipperDescr = "";

            if(description){
                snipperDescr = description.substring(0,50);
            }

            html += '<tr style="text-align: left" class="localfile_name"  data-name="' + data[i].file_name + '" data-id="' + data[i].id + '" data-descr="' + snipperDescr + '" id="selectLocalFile">';
            html += '<td><span style="visibility: visible" class="actions"><a target="_blank" href="/content/helpers/get.file.php?fid='+data[i].file_id+'"  class="gray green mpr0 cu--preview" data-id="' + data[i].id + '" data-type="task" title="Просмотр"><i class="icon-eye green"></i></a></span></td>'
            html += '<td>' + data[i].datum + '</td>';
            html += '<td>' + data[i].file_name + '</td>';
            html += '<td class="truncate-text">' + description + '</td>';
            // html += '<td>' + data[i].size + ' Kb</td>';
            html += '</tr>';
        }

        html += '</tbody>';
        html += '</table>';
        $('#file').html(html);

    }


    $(document).on('click', '#ytabs li', function () {



        var link = $(this).data('link');
        var id = $(this).closest('.ftabs').attr('id');

        $('#' + id + ' li').removeClass('active');
        $(this).addClass('active');

        if(link === 'file'){
            let n_id = '<?php echo $n_id?>'
            console.log(n_id);

            $.ajax({
                url: '/content/ajax/get.product.file.php?n_id='+n_id,
                type: 'GET',
                success: function (response) {
                    fileDataJson = JSON.parse(response);
                    console.log(fileDataJson);




                    createTableFiles(fileDataJson);
                }
            })
        }

        $('#' + id + ' .cbox').addClass('hidden');
        $('#' + id + ' .' + link).removeClass('hidden');


    });

    configpage();
    logs();
    resizeImages();
    sklad();
    drive();

    $('#pages div').click(function () {

        var page = $(this).data('page');
        logs(page);

    });

    $(window).on('resizeEnd', function () {
        resizeImages();
    });

    function dogs() {
        $('#tab0').load("/modules/modcatalog/card.php?n_id=<?=$n_id?>&action=getDogs").append('<img src="/assets/images/loading.svg">');
    }

    function dops() {
        $("#dopzat").load("/modules/modcatalog/card.php?n_id=<?=$n_id?>&action=getDopz").append('<img src="/assets/images/loading.svg">');
    }

    function sklad() {
        $("#sklad").load("/modules/modcatalog/card.php?n_id=<?=$n_id?>&action=getSklad").append('<img src="/assets/images/loading.svg">');
    }

    <?php if($setEntry[ 'enShowButtonLeft' ] == 'yes' and $isEntry == 'on'){ ?>
    function entrys() {
        $("#entry").load("/modules/modcatalog/card.entry.php?n_id=<?=$n_id?>").append('<img src="/assets/images/loading.svg">');
    }
    <?php } ?>

    function logs(page) {
        if (!page) page = 1;
        $("#log").load("/modules/modcatalog/card.php?n_id=<?=$n_id?>&action=getLogs&page=" + page).append('<img src="/assets/images/loading.svg">');
    }

    function drive(page) {

        if (!page) page = $('#hpage option:selected').val();

        $('#hpage').val(page);
        $("#drive").load("/modules/modcatalog/card.php?n_id=<?=$n_id?>&action=getDrive&page=" + page).append('<img src="/assets/images/loading.svg">');

    }

    function configpage() {
        $("#info").load("/modules/modcatalog/card.php?n_id=<?=$n_id?>&action=getInfo").append('<img src="/assets/images/loading.svg">');
        dogs();
        $("#dopzat").load("/modules/modcatalog/card.php?n_id=<?=$n_id?>&action=getDopz").append('<img src="/assets/images/loading.svg">');
        entrys();
        $('#formtabs').tabs();
    }

    function settab(id) {

        $('#dtabs li').removeClass('current');
        $('.rightcol').css('display', 'none');
        $('#tab-' + id).css('display', 'block');
        $('#dtabs #tb' + id).addClass('current');
        //$('#tab'+id).load(url).append('<img src="images/loading.svg">');

    }

    function resizeImages() {
        var wd = $(window).width() - $('.rightcol').width() * 1.2;
        $('#htmldiv img').css("width", wd + "px").css("height", "auto");
    }

    function removeReserve(id) {

        var url = '/modules/modcatalog/core.modcatalog.php?id=' + id + '&action=removereserv';
        $('#message').css('display', 'block').append('<div id=loader><img src=/assets/images/loader.gif> Загрузка данных. Пожалуйста подождите...</div>');
        $.get(url, function (data) {

            sklad();

            $('#message').fadeTo(1, 1).css('display', 'block').html(data);
            setTimeout(function () {
                $('#message').fadeTo(1000, 0);
            }, 20000);
        });

    }
</script>
</body>
</html>