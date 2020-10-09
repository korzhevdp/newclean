
myMap = null;
Polygon = null;
clusterer  = null;
GeoObjects = null;
ObjectCount = 0;
savedGeoObjects = null;
actionType = 'new';

ymaps.ready(function() {
    init(g_options,arDistrictsData,g_messages,null,null,actionType);
});


function init(main_options,districtsData,messages,clusterer,polygonarea,actionType) {
    if(!myMap)
    {
        myMap = new ymaps.Map('map', {
            center: [64.539393, 40.516939],
            zoom: 10,
                controls: ['zoomControl','searchControl']
        }, {
                provider: 'yandex#search',
                noPlacemark: true,
                suppressMapOpenBlock: true,
                restrictMapArea: true
        });
	

    
    }
    
    
    GeoObjects = null;
    savedGeoObjects = null;
    
    //console.log(main_options);
    $(document).find('.message-list .line-info').remove();
    if(!messages)
    {

        $('.message-list').append('<p class="line-info">Нет сообщений по выбранным параметрам</p>');
        $('.message-list').find('.line-info').css('display','block');
        return false;
    }
    else
    {
        if(messages.length===0)
        {
            $('.message-list').append('<p class="line-info">Нет сообщений по выбранным параметрам</p>');
            $('.message-list').find('.line-info').css('display','block');
        }
        else
        {
            $('.message-list').find('.line-info').html(null);
            $('.message-list').find('.line-info').css('display','none');
        }
        
        
        //console.log(myMap.geoObjects.get(0));
        if(myMap.geoObjects.get(0))
        {
            //console.log(1);
            GeoObjects = myMap.geoObjects.get(0).getGeoObjects();
            savedGeoObjects = GeoObjects;
            ObjectCount = GeoObjects.length;
            myMap.geoObjects.removeAll();
            $('.message-list a[data-id]').remove();
        }
    }
    
    mesMenu = $('<div/>');
    createMenuGroup(main_options,districtsData,messages,clusterer);
    SaveGeoObjects = myMap.geoObjects.get(0).getGeoObjects();
    ObjectCount = SaveGeoObjects.length;

    
    if(actionType=='new')
    {
        if(ObjectCount>0)
        {
            myMap.setBounds(myMap.geoObjects.getBounds(),{checkZoomRange:true,preciseZoom:false});
        }
        else
        {
            myMap.setBounds(myMap.geoObjects.getBounds(),{checkZoomRange:false,preciseZoom:false});
        }
    }

    $('.message-list > div').mCustomScrollbar();
    
    var lineInfoExist = $('.message-list').find('.line-info').length;
    if(!lineInfoExist)
    {
        $('.message-list').append('<p class="line-info"></p>');   
    }
    
}



function createMenuGroup(main_options,districtsData,messages,clusterer) {
    if(!clusterer)
    {
        clusterer = new ymaps.Clusterer({
            groupByCoordinates: true,
            clusterDisableClickZoom: false,
            clusterHideIconOnBalloonOpen: false,
            geoObjectHideIconOnBalloonOpen: false,
            preset: 'islands#invertedOrangeClusterIcons',
            clusterIconColor: '#0954a0'
        });
        myMap.geoObjects.add(clusterer);
    }
    
    
    $.each(messages, function(index, message) {
        createPlaceMarks(main_options, districtsData, message, clusterer,actionType);
    });
    
    

    //----------------- DISTRICT POLYGON -----------------//
    
    if(!main_options[4])
    {
        if(getCoordFromOSM && linkToOSM!='')
        {
            var url = linkToOSM;
            var regionName = districtFullName;
            $.getJSON(url, {q: regionName, format: "json", polygon_geojson: 1})
                .then(function (data) {
                    $.each(data, function(ix, place) {
                        if ("relation" == place.osm_type) {
                           
                            var osmCoord = place.geojson.coordinates[0];
                            var yandexCoord = [];
                            var newCoord = [];
                            
                            $.each(osmCoord, function(ix, coord) {
                                var oneCoord = [coord[1],coord[0]];
                                newCoord.push(oneCoord);
                            });
                            yandexCoord.push(newCoord);
                            
                            var Polygon = new ymaps.GeoObject({
                                geometry: {
                                    type: "Polygon",
                                    coordinates: yandexCoord,
                                    fillRule: "nonZero"
                                },
                                properties:{
                                    balloonContent: districtName
                                }
                            }, {
                                fillColor: '#e91919', //
                                strokeColor: '#0c2581',
                                opacity: 0.2,
                                strokeWidth: 2,
                                //strokeStyle: 'shortdash'
                            });
        
                            myMap.geoObjects.add(Polygon);
        
                        }
                    });
                }, function (err) {
                    console.log(err);
                });
        }
        else
        {
           //console.log(polygonarea);
            if(districtCoord!=='' && districtName!=='')
            {
            
                //console.log(districtCoord);
                Polygon = new ymaps.GeoObject({
                    geometry: {
                        type: "Polygon",
                        coordinates: districtCoord,
                        fillRule: "nonZero"
                    },
                    properties:{
                        balloonContent: districtName
                    }
                }, {
                    fillColor: '#2c52de', //
                    strokeColor: '#071857',
                    opacity: 0.2,
                    strokeWidth: 2,
                    //strokeStyle: 'shortdash'
                });
    
                myMap.geoObjects.add(Polygon);
            }
        }
    }
    else
    {
        if(districtsData.length)
        {
            $.each(districtsData.data, function(i, district) {
                var districtCoord = JSON.parse(district.coordinates);
                var AllPolygon = new ymaps.GeoObject({
                geometry: {
                    type: "Polygon",
                    coordinates: districtCoord,
                    fillRule: "nonZero"
                },
                properties:{
                    balloonContent: district.name
                }
                }, {
                    //fillColor: district.color, //
                    strokeColor: '#162457',
                    opacity: 0.2,
                    strokeWidth: 2,
                    //strokeStyle: 'shortdash'
                });
                myMap.geoObjects.add(AllPolygon);

            });
        }
    }

    
}



function balloonContentGenerate(main_options,item) {    
    var ballCont = '';
    var baloonCnt = '<div class="message-cont" data-message-id="'+item.id+'" data-address="'+item.address+'" data-message-category-name="'+item.category+'" data-message-time="'+item.create_time+'"><div><div class="temp-cont" data-type="depart"></div><div class="temp-cont" data-type="depart-comment"></div><div class="temp-cont" data-type="org"></div><div class="temp-cont" data-type="status"></div><div class="temp-cont" data-type="district"></div><div class="temp-cont" data-type="time"></div><input type="hidden" name="mesid" value="'+item.id+'"/>';
    var photo_str = '';
    if(item.files.length>0){
        $.each(item.files,function(index,file){
            photo_str+= '<a class="ph-item" href="'+file.path+'" data-w="'+file.w+'" data-h="'+file.h+'" style="background-image: url(../'+file.path+')"></a>';
        });
    } else photo_str = 'Файлов нет';
    var photos = '<div class="ph-cont" >'+photo_str+'</div>';
    
    var btn_status = '';
    var btn_responsible ='';
    var btn_time = '';
    var answerStr = '';
    var answerFilePath = '';
    var btn_responsible_org = '';
    var btn_depart = '';
    var dep_comment = '';
    var comment_datetime = '';
    
    if(law3)
    {
        btn_status = '<a href="#" class="action-btn change-status">Изм. статус</a>';
    }
    
    if(law4)
    {
        btn_responsible_org = '<a href="#" class="action-btn change-org">Изменить</a>';
    }
    
    if(law4_1)
    {
        btn_depart = '<a href="#" class="action-btn change-depart">Изменить</a>';
    }
    
    if(supervisoryAuthority || isAdmin)
    {
        btn_responsible = '<a href="#" class="action-btn change-responsible">Изменить</a>';
    }
    
    if(law8)
    {
        btn_time = '<a href="#" class="action-btn change-time">Изменить</a>';
    }

    if(item.answer!='')
    {
        answerStr = '<p class="answer-caption"><b>Ответ, который видит пользователь:</b></p>';
    }
    
    if(item.answer_file_path!=null && item.answer_file_path!='')
    {
        answerFilePath = '<div class="preview-answer-img no-print" style="background-image: url('+item.answer_file_path+');"></div>';
    }
    
    
    if(item.comment_datetime!='')
    {
        comment_datetime = ' от '+item.comment_datetime;
    }
    
    
    if(item.dep_comment)
    {
        dep_comment = '<div class="answer">'+item.dep_comment+'</div>';
    }
    else
    {
        if(!UserDepartment && !isAdmin)
        {
            dep_comment = '<div class="answer">Нет комментария</div>';
        }
        comment_datetime = '';
    }
    
        
    var orgName = '';
    if(item.org_name_only!=null)
    {
        orgName = ' - '+item.org_name_only;
    }
    else
    {
        orgName = '';
    }
    
    var chatBlock = '';
    var userChat = '';
    var departChat = '';
    var orgChat = '';
    //console.log(g_chat);
    if(law5)
    {
        if(!item.org_id) item.org_id = 0;
        if(!item.depart_id) item.depart_id = 0;
        if(!item.user_id) item.user_id = 0;
        
        console.log('chat');
        var chatActive = false;
        if(g_chat)
        {
    
            if(g_chat[1])
            {
                userChat = '<div data-id="'+item.id+'" data-user-id="'+item.user_id+'" data-value="'+item.user_id+'">Чат с гражданином<a href="#" class="tumbler '+chatActive+'" title=""></a><a href="#" class="icon-comments chat-panel-open">Открыть</a></div>';
            }
            
            if(g_chat[7])
            {
                departChat = '<div data-id="'+item.id+'" data-depart-id="'+item.depart_id+'" data-value="'+item.depart_id+'">Чат с департаментом<a href="#" class="tumbler '+chatActive+'" title=""></a><a href="#" class="icon-comments chat-panel-open">Открыть</a></div>';
            }
            
            if(g_chat[6])
            {
                orgChat = '<div data-id="'+item.id+'" data-org-id="'+item.org_id+'" data-value="'+item.org_id+'">Чат с организацией<a href="#" class="tumbler '+chatActive+'" title=""></a><a href="#" class="icon-comments chat-panel-open">Открыть</a></div>';
            }
        }
        chatBlock = '<div class="chat-block">'+userChat+departChat+orgChat+'</div><div class="separ"></div>';
    }
    
    departComment = '';
    if(UserDepartment || isAdmin)
    {
        var departComment = '<div class="depart-comment option-cont" data-type="depart-comment" data-id="null"><a class="action-btn change-depart-comment">Изменить</a><b>Комментарий департамента для Администрации округа</b>'+dep_comment+'</div>';
    }
    
    
    if(UserResponsibleUnit)
    {
        var departComment = '<div class="depart-comment option-cont"><b>Комментарий департамента'+comment_datetime+'</b>'+dep_comment+'</div>';
    }
    
    var info = '<div class="info-cont"><div class="option-cont" data-type="status" data-color="'+item.status_color+'" data-id="'+item.status_id+'"><div class="status '+item.status_icon+'">'+item.status+'</div>'+btn_status+answerStr+'<div class="answer">'+item.answer+'</div>'+answerFilePath+'</div>'+departComment+'<div class="separ"></div><div class="txt-cont">'+item.text+'</div></div>';
    baloonCnt += info+photos+'<i class="little m-address">'+item.address+'</i><div class="separ no-print"></div>';
    baloonCnt +='<div class="responsible option-cont" data-type="district" data-id="'+item.district_id+'"><i class="little">'+item.responsible+'</i>'+btn_responsible+'</div>';
    baloonCnt +='<div class="separ no-print"></div><div class="org-l-h">Департамент:</div><div class="depart option-cont" data-type="depart" data-id="'+item.depart_id+'"><i class="little">'+item.depart_name+'</i>'+btn_depart+'</div>';
    baloonCnt +='<div class="separ no-print"></div><div class="org-l-h">Ответственная организация:</div><div class="org option-cont" data-type="org" data-id="'+item.org_id+'"><i class="little">'+item.org_name+'</i>'+btn_responsible_org+'</div>';
    baloonCnt +='<div class="separ no-print"></div><div class="result-time option-cont" data-type="time" data-value="'+item.result_time_sys+'"><i class="little">Устранить до: <b>'+item.result_time+'</b></i>'+btn_time+'</div><div class="separ"></div></div></div>';
    baloonCnt += chatBlock;
    
    var btn_archive = '';
    if(law5) {
        btn_archive = '<a href="#" class="link to-archive" title="Поместив сообщение в архив вы уберете его из публичного доступа, но оно будет сохранено в системе, и может быть восстановлено">Отправить в архив</a>';
    }

    return baloonCnt;
}

function createPlaceMarks(main_options, districtsData, item, collection,actionType) {
    
	var btn_archive = '';
    if(law5) {
        btn_archive = '<a href="#" class="link to-archive" title="Поместив сообщение в архив вы уберете его из публичного доступа, но оно будет сохранено в системе, и может быть восстановлено">Отправить в архив</a>';
    }
    
    var markerCaption = '';
    
    if(!main_options[2])
    {
        if(!item.district)
        {
            markerCaption = '('+item.id+') Назначить ответственного';
        }
        else
        {
            markerCaption = item.id;
        }
    }
    
    var placemark = new ymaps.Placemark(item.center, { 
        balloonContentHeader: '<h4>#'+item.id+' - '+item.category+'</h4><i class="little">Сообщ. зарегистрировано ' + item.create_time + '</i>',
        balloonContentBody: '',
        balloonContentFooter: 'Измен. '+item.update_time+' <a href="#" class="link show-history" title="Данная функция временно недоступна">История</a><a href="#" class="link show-print" title="Данная функция временно недоступна">Печать</a>'+btn_archive,
        id: item.id,
        category: item.category_id,
        district: item.district_id,
        status: item.status_id,
        result_time: item.result_time_sys,
        iconCaption: markerCaption
    },{
        //preset: 'islands#orangeIcon',
        balloonMaxHeight: 600,
        iconCaptionMaxWidth: '250'
    });


    placemark.balloon.events.add('close', function(){
        var id = placemark.properties.get('id');
        $('.message-list').find('a[data-id="'+id+'"]').removeClass('active');
    });
     
    

    placemark.events.add('balloonopen', function(e){
        var geoObject = e.get('target');
        var messageId = geoObject.properties._data.id;
        var balloonContent = '';
        geoObject.properties.set('balloonContentBody', '<div class="gif-loader"></div>');
        var Data = {
            page: 125,
            message_id: messageId,
        };
        
        var msg = $('.message-list').find('a[data-id="'+messageId+'"]');
        $('.message-list a').removeClass('active');
        if(msg.length > 0)
        {
            msg.addClass('active');
            $('.message-list > div').mCustomScrollbar("scrollTo",msg);
        }
        
        
        var getOneMessage = SendDataJSON(Data);
                            
        getOneMessage.done(function(res) {
            if(res.status)
            {
                if(res.data)
                {
                    balloonContent = balloonContentGenerate(main_options,res.data);
                    geoObject.properties.set('balloonContentBody', balloonContent);
                }
            }
            else
            {
                
            } 
        });
        
        getOneMessage.fail(function(jqXHR, code, textStatus) {
            alert("При выполнении действия: [ "+textStatus+" ]. Пожалуйста, обратитесь к системному администратору.");
        });
    });
    
    
    
    if(item.icon_type!='' && item.icon_type!=null)
    {
        placemark.options.set('preset', 'islands#'+item.status_color+item.icon_type);
    }
    else
    {
        placemark.options.set('preset', 'islands#'+item.status_color+'Icon');
    }
    collection.add(placemark);
    
    var listItemColor = '';
    
    if(!main_options[1])
    {
        var list_item_color = item.status_color;
        if(list_item_color=='blue')
            list_item_color = '#085fa9';
        listItemColor = 'style="color: '+list_item_color+';"';
    }

    menuItem = $('<a href="#" '+listItemColor+'  data-id="' + item.id + '"><i>№' + item.id + '</i><b>' + item.category + '</b><div class="min-text">'+item.text+'</div><div data-sys-icon="'+item.status_icon+'" class="min-text status-style">'+item.status+'</div></a>');
    menuItem.appendTo(mesMenu);

    mesMenu.appendTo($('.message-list'));
    

    mlist = $('.message-list');
    
    myMap.events.add('boundschange', function (event) {
        
        GeoObjects = myMap.geoObjects.get(0).getGeoObjects();
        ObjectCount = GeoObjects.length;
        
        ActiveCcount = 0;
        
        if(ObjectCount!==0)
        {
            for(i=0; i<ObjectCount;i++)
            {
                if(!GeoObjects[i].options._mapper.active)
                {
                        mlist.find('a[data-id="'+GeoObjects[i].properties.get('id')+'"]').css('display','none');
                        mlist.find('a[data-id="'+GeoObjects[i].properties.get('id')+'"]').attr('data-display','none');
                }
                else
                {
                        mlist.find('a[data-id="'+GeoObjects[i].properties.get('id')+'"]').css('display','block');
                        mlist.find('a[data-id="'+GeoObjects[i].properties.get('id')+'"]').attr('data-display','block');
                        ActiveCcount++;
                }
            }
        }
        
        
        ShowInfoLine(ActiveCcount,'В текущей зоне видимости не найдено ни одного сообщения');
    });
            
            
            
            
    mesMenu.find('a[data-id="' + item.id + '"]').bind('click',function () {
        if (!placemark.balloon.isOpen())
        {
            $('.message-list a').removeClass('active');
            $(this).addClass('active');
            placemark.balloon.open();
        }
        else
        {
            placemark.balloon.close();
        }
        return false;
    });
    
}



$(document).find('.filter').on('change','#category,#district,#status',function() {
    if (myMap.balloon.isOpen()) {
        myMap.balloon.close();
    }
    
    setUserOptions($(this));
});




$(document).on('change','.option-cont select#status',function() {
    var answer = $(this).find('option:selected').attr('data-answer');
    var file = $(this).find('option:selected').attr('data-file');
    var optionCont = $(this).closest('.option-cont');
    var color = $(this).find('option:selected').attr('data-color');
    var curColor = optionCont.attr('data-color');
    optionCont.attr('data-current-color',curColor);
    optionCont.attr('data-color',color);

    if(answer==1)
    {
        if(file==1)
        {
           
            var answerFile = createAddFile();
            
            if(!optionCont.find('.answer-file').length)
            {
                if(optionCont.find('textarea').length>0)
                {
                    optionCont.find('textarea').after(answerFile);
                }
                else
                {
                    $(this).after(answerFile);
                }
            }
        }
        else
        {
            optionCont.find('.answer-file').remove();
        }
        
        if(optionCont.find('textarea').length==0)
        {
            $(this).after('<p class="answer-caption">Можете добавить текст, который увидит пользователь:</p><textarea name="answer" placeholder="Введите текст ответа..."></textarea>');
            mainCont = optionCont.closest('.message-cont');
        }
    }
    else
    {
        optionCont.find('.answer-file').remove();
        optionCont.find('textarea').remove();
        optionCont.find('.answer-caption').remove();
    }
});


$(document).on('click','.chat-block a.tumbler', function(e) {
    var elData = $(this).parent();
    var message_id = elData.attr('data-id');
    var user_id = elData.attr('data-user-id');
    var data = { page: 136, message_id: message_id, user_id: user_id  };
    var chatTumbler = SendDataJSON(data);
    
    chatTumbler.done(function(result){
        if(result.status)
        {
            if(result.active)
            {
                obj.addClass('active');
                obj.attr('title','Заблокировать чат');
            }
            else
            {
                obj.removeClass('active');
                obj.attr('title','Активировать чат');
            }
        }
        
    });
    
    chatTumbler.fail(function(result){
       alert('При изменении состояния чата возникла ошибка.'); 
    });
    
    e.preventDefault();
});


$(document).on('click','.to-archive', function(e) {
    var message_id = $(this).closest('div').find('input[name=mesid]').val();
    var data = {
        page: '7',
        id: message_id
    };
    var mlist = $('.message-list');
    
    var SendToArchive = SendDataJSON(data);
    
    SendToArchive.done(function(result){
        if(result.status)
        {
            GeoObjects = myMap.geoObjects.get(0).getGeoObjects();
            ObjectCount = GeoObjects.length;
            if(ObjectCount!==0) {
                for(i=0; i<ObjectCount;i++)
                {
                    if(GeoObjects[i].properties.get('id') == message_id)
                    {
                        myMap.geoObjects.get(0).remove(GeoObjects[i]);
                        mlist.find('a[data-id="'+message_id+'"]').remove();
                    }
                }
            }
            
            var hintId = 'to-archive';
            var headerText = 'Выполнено';
            var typeText = 'Перенос сообщения №'+message_id+' в архив';
            var infoText = result.message;
            ShowGlobalHint(headerText,typeText,infoText,hintId); 
        }
        else
        {
            var hintId = 'to-archive';
            var headerText = 'Перенос сообщения в архив';
            var typeText = 'Операция заблокирована';
            var infoText = result.message;
            ShowGlobalHint(headerText,typeText,infoText,hintId); 
        }
    });
    
    SendToArchive.fail(function(result){
       alert('При отправке сообщения в архив возникли ошибки.'); 
    });
});

/*
    
    var timer = setInterval(function() {
            var data = { page: 101 };
            alert();
            GetServerData(data).done(function(res){
                if(res.status) {
                    init(res.data,clusterer,null,'refresh');
                }
                else
                {
                    alert(res.message);
                }
            });
            
            GetServerData(data).fail(function(jqXHR, code, textStatus) {
                alert("При выполнении запроса произошла ошибка [ "+textStatus+" ]. Пожалуйста, обратитесь к системному администратору.");
            });
    }, 10000); 
*/


function SetIconOption(id,type,value,color,content) {
    var set_type = type;
    if(type == 'time')
    {
        set_type = 'result_time';
    }
    GeoObjects = myMap.geoObjects.get(0).getGeoObjects();
    ObjectCount = GeoObjects.length;
    if(ObjectCount!==0)
    {
        for(i=0; i<ObjectCount;i++)
        {
            if(GeoObjects[i].properties.get('id') == id)
            {
                if(color!=0)
                {
                    GeoObjects[i].options.set('preset','islands#'+color+'Icon');
                }
                
                GeoObjects[i].properties.set('iconCaption',id);
                if(type=='district' && g_options[3])
                {
                    GeoObjects[i].properties.set('iconCaption','Объект исчезнет после обновления');
                }
                
                GeoObjects[i].properties.set(set_type,value);
                GeoObjects[i].properties.set('balloonContentBody',content);
            }
        }
    }
}



function HideFilterMessage(obj) {
    obj.css('display','none');
    obj.attr('data-display','none');
}



function SaveOption(obj) {
    var message_id = obj.closest('.message-cont').find('input[name=mesid]').val();
    var color = 0;
    var option_cont = obj.parent();
    var type = option_cont.attr('data-type');
    var answer = '';
    var base64file = '';
    
    if(type!='time')
    {
        value = option_cont.find('option:selected').attr('data-id');
        option_cont.attr('data-id',value);
        if(type=='status')
        {
            color = option_cont.find('option:selected').attr('data-color');
            status_text = option_cont.find('option:selected').val();
            status_icon = option_cont.find('option:selected').attr('data-icon');
            list_message = $('.message-list a[data-id="'+message_id+'"]').find('div[data-sys-icon]');
            answerFile = $('.answer-file input[name="filedata"]');
            //list_message.removeClass(list_message.attr('data-sys-icon')).addClass(status_icon);
            
            //list_message.attr('data-sys-icon',status_icon);
            list_message.html(status_text);
            answer_cont = option_cont.find('textarea[name="answer"]');
            if(answer_cont.length>0)
            {
                answer = option_cont.find('textarea[name="answer"]').val();
            }
            
            if(answerFile.length>0)
            {
                if(answerFile.val()!=='')
                {
                    base64file = answerFile.val();
                }
            }
            
        }
        
        if(type=='depart-comment')
        {
            answer_cont = option_cont.find('textarea[name="answer"]');
            if(answer_cont.length>0)
            {
                answer = option_cont.find('textarea[name="answer"]').val();
            }
        }
            
    }
    else
    {
        value = option_cont.find('input').val();
        option_cont.attr('data-value',value);
    }
    
    //console.log(base64file);
    
    var data = {
        page: 5,
        type: type,
        answer: answer,
        value: value,
        filedata: base64file,
        id: message_id
    };
            
    option_cont.load('../admin/handler.php', data, function(){
        if(color!=0)
        {
            option_cont.attr('data-color',color);
        }
        
        if(type=='district')
        {
            $('.org.option-cont b').html('Не определена');
            var hintId = 'hint-id2';
            var headerText = 'Рекомендации и подсказки';
            var typeText = 'Назначение ответств. подразделения';
            var infoText = 'Обратите внимание, что при изменении ответственного подразделения ответственую организацию необходимо будет назначить повторно.';
            ShowGlobalHint(headerText,typeText,infoText,hintId);
        }
        $('.temp-cont[data-type="'+type+'"]').html('');
        var messCont = option_cont.closest('.message-cont');
        var id = messCont.attr('data-message-id');
        var address = messCont.attr('data-address');
        var catName = messCont.attr('data-message-category-name');
        var time = messCont.attr('data-message-time');
        content = '<div class="message-cont" data-message-id="'+id+'" data-address="'+address+'" data-message-category-name="'+catName+'" data-message-time="'+time+'">'+messCont.html()+'</div>';
        SetIconOption(message_id,type,value,color,content);
    });
}


function DepartCommentChange(obj) {
    var message_id = obj.closest('.message-cont').find('input[name=mesid]').val();
    var optionCont = obj.parent();
    var type = optionCont.attr('data-type');
    var buttons = '<a href="#" class="action-btn save-option">Сохранить</a><a href="#" class="action-btn close-option">Отмена</a>';
    $('a.close-option').click();

    var current_id = optionCont.attr('data-id');
    currentAnswer = optionCont.find('div.answer');
    $('.temp-cont[data-type="'+type+'"]').html(optionCont.html());
    var textAnswer = '';
    if(typeof(currentAnswer.html())!='undefined')
    {
        textAnswer = currentAnswer.html();
    }
    answerInput = '<p class="answer-caption">Комментарий, который будет виден только Администрации округа:</p><textarea name="answer" placeholder="Введите комментарий...">'+textAnswer+'</textarea>';
    optionCont.html(null);
    optionCont.append(answerInput+buttons);
}


function ShowOptionChange(obj) {
    var message_id = obj.closest('.message-cont').find('input[name=mesid]').val();
    var optionCont = obj.parent();
    var type = optionCont.attr('data-type');
    var buttons = '<a href="#" class="action-btn save-option">Сохранить</a><a href="#" class="action-btn close-option">Отмена</a>';
    $('a.close-option').click();
    if(type!='time')
    {
        var current_id = optionCont.attr('data-id');
        var optionlist = $("#"+type).clone();
        if(type=='status')
        {
            optionlist.find('option[data-id="0"]').remove();
        }
        optionlist.find('option[data-id="overdue"]').remove();
        optionlist.find('option[data-id="'+current_id+'"]').attr('selected','selected');
        $('.temp-cont[data-type="'+type+'"]').html(optionCont.html());
        var answerIndex = optionlist.find('option[data-id="'+current_id+'"]').attr('data-answer');
        var fileIndex = optionlist.find('option[data-id="'+current_id+'"]').attr('data-file');
        
        var answerInput = '';
        if(answerIndex==1)
        {
            currentAnswer = optionCont.find('div.answer');
            var textAnswer = '';
            if(typeof(currentAnswer.html())!='undefined')
            {
                textAnswer = currentAnswer.html();
            }
            answerInput = '<p class="answer-caption">Можете добавить текст, который увидит пользователь:</p><textarea name="answer" placeholder="Введите текст ответа...">'+textAnswer+'</textarea>';
            
            var answerFile = '';
            if(fileIndex==1)
            {
                answerFile = createAddFile();
            }
            
        }
        
        optionCont.html(null);
        if(type=="org")
        {
            var orgsearch = $("<input/>", { type: "text" , class: 'balloon-input-text', placeholder: 'Начните вводить название организации...'  } );
            orgsearch.on('keyup', function(e) {
                var value = $(this).val().toLowerCase();
                var ActiveCcount = 0;
                optionlist.find('option').each(function(i,elem) {
                    var thisVal = $(this).html().toLowerCase();
                    if(false != thisVal.indexOf(value)+1)
                    {
                        ActiveCcount++;
                        $(this).css('display','block');
                    }
                    else
                    {
                        if($(this).attr('data-id') != 0)
                            $(this).css('display','none');
                    }
                });
                e.preventDefault();
            });
            optionlist.attr('size','6');
            optionCont.append(orgsearch);
        }
        
        
        optionCont.append(optionlist).append(answerInput+buttons);
        optionCont.find('textarea').after(answerFile);
    
    }
    else
    {
        var current_value = optionCont.attr('data-value');
        var datetimepicker = $("<input/>", { type: "text" , value: current_value } );
        datetimepicker.appendDtpicker();
        $('.temp-cont[data-type="'+type+'"]').html(optionCont.html());
        optionCont.html(datetimepicker).append(buttons);
    }
}



function createAddFile() {
    $('.answer-file').remove();
    var answerFile = $('<div/>', { class: 'answer-file' });
    var addAnswerFile = $('<a/>', { href: '#', id: 'screenshot', class: 'link blue mrg-none icon-plus', html: 'Прикрепить файл' });
    var fileInput = $('<input/>', { type: "file", id: 'inputfile', name: 'name' });
    var datafileInput = $('<input/>', { type: "hidden", name: 'filedata' });
    var imgPreview = $('<div/>', { class: 'preview-answer-img' });
    
    
    fileInput.on('change', function() {
        var currentText = addAnswerFile.html();
        var text = $(this).val() || currentText;
        var file = $(this);
        var uploadFile = file[0].files[0];
        
        
        getBase64(uploadFile).then(function(data) {
            datafileInput.val(data);
            var removeFileBtn = $('<a\>', { href: '#', class: 'line-link link', html: 'Удалить' });
            removeFileBtn.on('click', function(e) {
                addAnswerFile.addClass('icon-plus')
                .removeClass('icon-check')
                .html('Прикрепить файл');
                datafileInput.val(null);
                fileInput.val(null);
                answerFile.find('.img-bottom-info').remove();
                imgPreview.remove();
                $(this).remove();
            });
            
            if(data.length > 0)
            {
                imgPreview.css('background-image','url("'+data+'")');
                imgPreview.css('display','block');
            }
            else
            {
                imgPreview = '';
            }
            
            
            addAnswerFile
                .removeClass('icon-plus')
                .addClass('icon-check')
                .html('Файл прикреплен')
                .after(imgPreview)
                .after('<p class="img-bottom-info">Изображение будет отображаться после сохранения.</p>')
                .after(removeFileBtn);
        });
        
    });
    
    addAnswerFile.on('click',function(e) {
        if(datafileInput.val()=='')
        {
            fileInput.click();
        }
        e.preventDefault();    
    });
    
    answerFile.append(fileInput).append(datafileInput).append(addAnswerFile);
    return answerFile;
}


function CloseOption(obj) {
    var optionCont = obj.parent();
    var type = optionCont.attr('data-type');
    var save_contant = $('.temp-cont[data-type="'+type+'"]').html();
    $('.temp-cont[data-type="'+type+'"]').html('');
    optionCont.attr('data-color',optionCont.attr('data-current-color'));
    optionCont.html(save_contant);
}


$(document).on('click','a.change-depart-comment',function(e) {
    DepartCommentChange($(this));
    e.preventDefault();
});


$(document).on('click','a.change-status, a.change-responsible, a.change-time, a.change-org, a.change-depart',function(e) {
    ShowOptionChange($(this));
    e.preventDefault();
});

    
$(document).on('click','a.close-option',function(e) {
    CloseOption($(this));
    e.preventDefault();
});
    
$(document).on('click','a.save-option',function(e) {
    SaveOption($(this));
    e.preventDefault();
});
