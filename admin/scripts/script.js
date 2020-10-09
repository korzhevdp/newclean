
$(function() {
    
    $('.message-list > div').mCustomScrollbar();
    
    
    if($('#mapcont').length>0)
        $('#mapcont').append('<div class="copyright">© МУ &laquo;Центр информационных технологий&raquo; 2019</div>');
    //else $('body').append('<div class="copyright">© МУ &laquo;Центр информационных технологий&raquo; 2017</div>');
    
    $(document).on('keyup','.messages .search input',function () {
        var value = $(this).val().toLowerCase();
        mlist = $('.message-list');
        var ActiveCcount = 0;
        mlist.find('a[data-id]').each(function(index) {
            if($(this).attr('data-display')!='none') {
                thisVal = $(this).html().toLowerCase();
                if(false == thisVal.indexOf(value)+1) {
                    $(this).css('display','none');
                } else {
                    $(this).css('display','block');
                    ActiveCcount++;
                }
            }
        });
        
        ShowInfoLine(ActiveCcount,'По вашему запросу не найдено ни одного сообщения');
    });
    
    
});




$(document).on('click','#change_password_submit', function(e) {
    var form = $(this).closest('.form-container');
    var error = form.find('.error-line');
    var success = form.find('.success-line');
    
    if(!checkDate(form)) return false; // проверка формы
    
    var currentPassword = form.find('input[name="password"]');
    var newPassword = form.find('input[name="password1"]');

    
    var Data = {
        page: 104,
        current_password: currentPassword.val(),
        new_password: newPassword.val()
    };
    
    var changePassword = SendDataJSON(Data);
                        
    changePassword.done(function(res) {
        if(res.status==true)
        {
            success.html(res.message);
            error.html(null);
            form.find('input').val(null);
        }
        else
        {
            error.html(res.message);
            success.html(null);
        }
                            
    });
                        
    changePassword.fail(function(jqXHR, code, textStatus) {
        alert("При изменении пароля произошла ошибка: [ "+textStatus+" ]. Пожалуйста, обратитесь к системному администратору.");
    });
    e.preventDefault();
});

$(document).on('keyup','input[type="password"]', function(e) {
    if(event.keyCode == 13){
        $('a.submt#auth').click();
        e.preventDefault();
    }
});


/*
$(document).on('click','a.tumbler', function(e) {
    
    var message_id = 0;
    var user_id = 0;
    var depart_id = 0;
    var org_id = 0;
    var type = 0;
    var obj = $(this).parent();
    
    if(obj.is('[data-id]'))
    {
        message_id = obj.attr('data-id');
    }
    
    if(obj.is('[data-user-id]'))
    {
        user_id = obj.attr('data-user-id');
    }
    
    if(obj.is('[data-depart-id]'))
    {
        depart_id = obj.attr('data-depart-id');
    }
    
    if(obj.is('[data-org-id]'))
    {
        org_id = obj.attr('data-org-id');
    }
    
    var data = {};
    
    if($(this).hasClass('active'))
    {
        chatSetDeactive(data,$(this));
    }
    else
    {
        chatSetActive(data,$(this));
    }
    e.preventDefault();
});
*/


$(document).on('click','#instruction', function(e) {
    var panelCaption = '<i class="icon-info">Справка по системе</i>';
    var data = { page: 6 };
    ShowRightPanel(panelCaption,data);
});

$(document).on('click','#options', function(e) {
    var panelCaption = '<i class="icon-widget">Настройки (в разработке)</i>';
    var data = { page: 9 };
    ShowRightPanel(panelCaption,data);
});

$(document).on('click','.chat-panel-open', function(e) {
    var message_id = 0;
    var user_id = 0;
    var depart_id = 0;
    var org_id = 0;
    var type = 0;
    var obj = $(this).parent();
    
    if(obj.is('[data-id]'))
    {
        message_id = obj.attr('data-id');
    }
    
    if(obj.is('[data-user-id]'))
    {
        user_id = obj.attr('data-user-id');
    }
    
    if(obj.is('[data-depart-id]'))
    {
        depart_id = obj.attr('data-depart-id');
    }
    
    if(obj.is('[data-org-id]'))
    {
        org_id = obj.attr('data-org-id');
    }

    
    var panelCaption = '<i class="icon-comments">Чат в рамках сообщения № '+message_id+'</i>';
    var data = {
        page: 130,
        message_id: message_id,
        user_id: user_id,
        depart_id: depart_id,
        org_id: org_id,
        noscroller: true
    };
    
    ShowRightPanel(panelCaption,data);
});

$(document).on('click','#statistic', function(e) {
    var panelCaption = '<i class="icon-chart-bar">Статистика обращений (в разработке)</i>';
    var data = { page: 8, type: 'long' };
    ShowRightPanel(panelCaption,data);
});

$(document).on('click','.show-history', function(e) {
    var hintId = 'hint-history';
    var headerText = 'Информация для пользователя';
    var typeText = 'История изменений';
    var infoText = 'Данный функционал находится на стадии разработки.';
    ShowGlobalHint(headerText,typeText,infoText,hintId); 
});

$(document).on('click','.show-print', function(e) {
    /*var hintId = 'hint-print';
    var headerText = 'Информации для пользователя';
    var typeText = 'Печать сообщения';
    var infoText = 'Вывод на печать или сохранение в .PDF содержимого сообщения. Пока не доступно, в разработке.';
    ShowGlobalHint(headerText,typeText,infoText,hintId); */
    var messageCont = $('.message-cont');
    var messageId = messageCont.attr('data-message-id');
    var messageCatName = messageCont.attr('data-message-category-name');
    var messageCreateTime = messageCont.attr('data-message-time');
    var messageAddress = messageCont.attr('data-address');
    var messageText = messageCont.find('.txt-cont').html();
    var messageStatus = messageCont.find('.status').html();
    var printCont = $('<div/>', { id: 'print-container', html: messageCont.html() });
    
    printCont.find('.txt-cont').remove();
    printCont.find('.m-address').remove();
    printCont.find('.status').remove();
    
    printCont.prepend('<br><br>Статус сообщения: <b>'+messageStatus+'</b>');
    printCont.prepend('<b>Комментарий к сообщению: </b><br/>'+messageText);
    printCont.prepend('<div class="separ"></div><br>');
    printCont.prepend('<div style="padding-right: 270px; margin-bottom: 50px;">Время создания сообщения: <b>'+messageCreateTime+'</b></div>');
    printCont.prepend('<div style="padding-right: 270px;">Номер сообщения в системе: <b>'+messageId+'</b></div>');
    printCont.prepend('<div style="padding-right: 270px; margin-top: 90px;">Категория сообщения: <b>'+messageCatName+'</b></div>');
    
    var sysLogo = $('<div/>', { style: 'width: 250px; position: absolute; top: 0; right: 0; left: auto; bottom: auto;' });
    sysLogo.append('<div style="text-align: center; width: 100%;"><img style="width: 250px;" src="/img/big-logo.jpg"></div>');
    sysLogo.append('<div style="text-align: center; font-size: 1.4em;">Архангельск</div>');
    sysLogo.append('<div style="text-align: center; font-size: 1.1em;">Чистый город</div><br><br>');
    printCont.prepend(sysLogo);
    
    
    printCont.append('<br/><h3><b>Изображения участка:</b></h3>');
    var imgCount = 0;
    $('.ph-cont .ph-item').each(function(i,elem) {
        var messageImg = $('<img>', { class: 'mimg', src: $(this).attr('href') });
        printCont.append(messageImg);
        imgCount++;
    });
    printCont.append('<p>Изображения в кол-ве '+imgCount+' шт. были загружены пользователем при создании сообщения.</p>'+messageAddress);
    
    $('body').append(printCont);
    
    window.setTimeout("window.print();",100);
});

window.onbeforeprint = function() {
    
    
};

window.onafterprint = function() {
    $('#print-container').remove();
};


$(document).on('click','#auth', function(e) {
    var data = {};
    var thisBtn = $(this);
    if(checkDate($(this).closest('.container'))) {
        data = { 
          func: 2,
          data: {
              email: $('input[name="login"]').val(),
              password: $('input[name="password"]').val()
          }
        };
    
        var userAuth = GetServerResult(data);
        userAuth.done(function(res){
            if(!res.status)
            {
                $('.form-error').remove();
                thisBtn.closest('.btn-block').before('<div class="form-error">'+res.message+'</div>');
            }
            else
            {
                
                if(res.key)
                {
                    localStorage.setItem('auth_key', res.key);
                }
                else
                {
                    //alert('Не удалось сохранить ключ авторизации');
                }
                
                var data = { page: '4' };
                LoadPage(data);
                
            }
        });
        
        userAuth.fail(function(jqXHR, code, textStatus) {
            serverResultError(textStatus);
        });
        
   }
   e.preventDefault();
});


$(document).on('click', '#logout', function(e) {
  var data = { 
      page: '0',
      logout: true
  };
  
  localStorage.setItem('auth_key', null);
  LoadPage(data);
  e.preventDefault();
});



$(document).on('click', '#reg', function(e) {
  data = { 
      page: '2'
  };
  LoadPage(data);
  e.preventDefault();
});


$(document).on('click', '#to_auth', function(e) {
  data = { 
      page: '0'
  };
  LoadPage(data);
  e.preventDefault();
});


$(document).on('click', '#password_recovery', function(e) {
    var data = {
        page: '34'
    };
    LoadContent(data);
    e.preventDefault();
});


$(document).on('click', '#repass', function(e) {
    var form = $(this).parent('div');
    if(checkDate(form)) {
        var email = $('input[name="email"]').val();
        
        var data = {
            page: '35',
            email: email,
            type: '2'
        };
        var recoveryResult = SendDataJSON(data);
        recoveryResult.done(function(res){
            if(!res.status || res.status!=true)
            {
                 $('.form-error').html(res.message);
            }
            else
            {
                form.html(null);
                form.html('<p class="form-success-message">'+ res.message +'</p>');
            }
        });
        
        recoveryResult.fail(function(jqXHR, code, textStatus) {
            serverResultError(textStatus);
        });
    }
    e.preventDefault();
});


$(document).on('click', '#new_password', function(e) {
    var form = $(this).parent('div');
    if(checkDate(form)) {
        var recovery_key = 0;
        if($('input[name="recovery_key"]'))
        {
            recovery_key = $('input[name="recovery_key"]').val();
        }
        var data = { 
            page: 37,
            data: {
                recovery_key: recovery_key,
                password: $('input[name="password2"]').val(),
            }
        };
        var recoveryResult = SendDataJSON(data);
        recoveryResult.done(function(res){
            if(!res.status || res.status!=true)
            {
                 $('.form-error').html(res.message);
            }
            else
            {
                form.html(null);
                form.html('<p class="form-success-message">'+ res.message +'</p>');
            }
        });
        
        recoveryResult.fail(function(jqXHR, code, textStatus) {
            serverResultError(textStatus);
        });
    }
    e.preventDefault();
});


$(document).on('click', '#to_lk', function(e) {
    window.location.reload(); 
    e.preventDefault();
});


$(document).on('click','.main-panel .mpanel-cont a[data-code]', function(e) {
    var code = $(this).data('code');
    var refCont = $(this).parent('div');
    refCont.find('a').removeClass('active');
    
    refCont.find('div.one-info').stop().slideUp(200).removeClass('active');
    refCont.find('div[data-code="'+code+'"]').stop().slideDown(200, function() {
        $(this).addClass('active');
    });
    
    $('.main-panel .mpanel-cont a').removeClass('active');
    $(this).addClass('active');
    e.preventDefault();
});


$(document).on('click','.main-panel .pd-panel-cont > a.sl', function(e) {
    var thisObj = $(this);
    
    if(!thisObj.hasClass('active'))
    {
        if(thisObj.find('.stat-detail').html()!='')
        {
            thisObj.addClass('active');
            thisObj.find('i').removeClass('icon-right-open-big').addClass('icon-down-open-big');
            thisObj.find('.stat-detail').addClass('active');
        }
    }
    else
    {
        thisObj.removeClass('active');
        thisObj.find('i').removeClass('icon-down-open-big').addClass('icon-right-open-big');
        thisObj.find('.stat-detail').removeClass('active');
    }
    e.preventDefault();
});



$(document).on('click','.options-panel.active a[data-type]', function(e) {
    var data = {};
    var caption = '';
    var type = 'list';
    var dataType = $(this).data('type');
    
    switch(dataType) {
        case 91: {
            caption = 'Настройки учетных записей пользователей';
            break; 
        }
        case 92: {
            caption = 'Настройка групповой политики';
            break; 
        }
        case 94: {
            caption = 'Персональные настройки';
            type = 'default';
            break; 
        }
        case 95: {
            caption = 'Ответственные подразделения';
            break; 
        }
        case 96: {
            caption = 'Справочник ответственных организаций';
            break; 
        }
        case 97: {
            caption = 'Справочник категорий сообщений';
            break; 
        }
        case 99: {
            caption = 'Справочник статусов сообщений';
            break; 
        }
        case 106: {
            caption = 'Заметки разработчика';
            break; 
        }
        case 107: {
            caption = 'Сообщения в системе';
            break; 
        }
        case 108: {
            caption = 'Настройки почтовых событий';
            break; 
        }
        case 109: {
            caption = 'Обращения в техническую поддержку';
            break; 
        }
        case 100: {
            caption = 'История действий пользователей';
            break; 
        }
        default: caption = 'Выбранный раздел настроек недоступен';
    }
    
    data = {
        page: dataType,
        caption: caption,
        type: type
    };
    ShowModWindow(data,$(this));
});


$(document).on('click','.options-panel.active .checkbox a', function(e) {
    var thisObj = $(this);
    if(!thisObj.hasClass('active'))
    {
        thisObj.addClass('active');
    }
    else
    {
        thisObj.removeClass('active');
    }
});


$(document).on('click', '.editable a.info', function(e) {
    var curItem = $(this).parent();
    var input = curItem.find('input');
    var href = $(this).attr('href');
    if(href!=='#' && href!=='')
    {
        if($(href).length>0)
        {
            var mainSelect = $(href).find('select,.select');
            if(mainSelect.length>0)
            {
                var newSelect = mainSelect.clone();
                if(newSelect.hasClass('select'))
                {
                    newSelect.on('mouseleave', function() {
                      $(this).remove();
                    });
                    
                    
                    var currLink = curItem.find('a:first');
                    var org_id = curItem.parent('div').attr('data-row-id');
                    
                    newSelect.find('a').on('click', function() {
                        var action_type = 1;
                        if($(this).hasClass('active'))
                        {
                            $(this).removeClass('active');
                            action_type = 0;
                        }
                        else
                        {
                            $(this).addClass('active');
                            action_type = 1;
                        }
                        
                        var depart_id = $(this).attr('data-depart-id');
                        
                        /*************************  ЗАПРОС НА СОХРАНЕНИЕ ДАННЫХ  ****************************/
                        
                        var Data = {
                            page: 102,
                            depart_id: depart_id,
                            org_id: org_id,
                            action_type: action_type
                        };
                        var setSubOrg = SendDataJSON(Data);
                        
                        setSubOrg.done(function(res) {
                            if(res.status==true)
                            {
                                
                            }
                            else
                            {
                                alert(res.message);
                            }
                            
                        });
                        
                        setSubOrg.fail(function(jqXHR, code, textStatus) {
                            alert("При сохранении данных произошла ошибка [ "+textStatus+" ]. Пожалуйста, обратитесь к системному администратору.");
                        });
                        
                        /*************************  ЗАПРОС НА СОХРАНЕНИЕ ДАННЫХ  ****************************/
                        
                        
                        var objString = '';
                        var i = 0;
                        newSelect.find('a.active').each(function() {
                                if(i>0) objString += ', ';
                                objString += '<b>('+(i+1)+')</b>&nbsp;'+$(this).html();
                                i++;
                        });
                        
                        currLink.html(objString);
                    });
                    
                
                
                if(curItem.find('select,.select').length <= 0) {
                    /*************************  ЗАПРОС НА ПОЛУЧЕНИЕ ДАННЫХ  ****************************/
                        
                        var Data = {
                            page: 103,
                            org_id: org_id
                        };
                        var getSubOrg = SendDataJSON(Data);
                        
                        getSubOrg.done(function(res) {
                            if(res.status==true)
                            {
                                
                                newSelect.find('a').removeClass('active');
                                newSelect.find('input').prop('checked', false);
                                $.each(res.departments,function(index,value){
                                    newSelect.find('a[data-depart-id="'+index+'"]').addClass('active');
                                });
                                curItem.append(newSelect);

                            }
                            else
                            {
                                alert(res.message);
                            }
                            
                        });
                        
                        getSubOrg.fail(function(jqXHR, code, textStatus) {
                            alert("При сохранении данных произошла ошибка [ "+textStatus+" ]. Пожалуйста, обратитесь к системному администратору.");
                        });
                        
                    /*************************  ЗАПРОС НА ПОЛУЧЕНИЕ ДАННЫХ  ****************************/
                    
                }
            } else {
                curItem.append(newSelect);
            }
            }
        }
    }
    
    $(this).closest('.editable').find('a.but.btnno').click();
    var errorInx = 1;
    var isDeleteLink = $(this).hasClass('option-delete-link');
    var select = curItem.find('select');
    if(!isDeleteLink) {
        if(select.length>0)
        {
            if($(this).is('[data-id]'))
            {
                select.find('option[value="'+$(this).attr("data-id")+'"]').attr("selected", "selected");
                $(this).addClass('hide');
                errorInx = 0;
            }
        }
        
        if(input.length>0)
        {
            input.addClass('active');
            input.val($(this).html());
            $(this).addClass('hide');
            errorInx = 0;
        }
    }
    else
    {
        curItem.prepend('<b style="color: #922424;">Удалить?</b>');
        $(this).addClass('hide');
    }
    
    
    if(errorInx != 1 || $(this).hasClass('option-delete-link'))
    {
        var btnOk = $('<a/>', { href: '#', class:'but btnok', text: 'ОК' });
        var btnNo = $('<a/>', { href: '#', class:'but btnno', text: 'Отмена' });
        
        curItem.append(btnOk).append(btnNo);
        
        btnOk.on('click', function() {
            
            var curItem = $(this).parent();
            var input = curItem.find('input[type="text"]');
            var select = curItem.find('select');
            var fieldValue = '';
            var dataField = '';
            var data = {};
            var sysTableId = curItem.closest('.table').data('table-id');
            var rowId = curItem.closest('.table-row').data('row-id');
            var respDataType = '';
            var isDeleteLink = $(this).parent().find('a.info').hasClass('option-delete-link');
            
            if(input.length > 0)
            {
                fieldValue = input.val();
                dataField = input.attr('name');
                respDataType = '1';
            }
            else
            if(select.length > 0)
            {
                var hiddenInput = curItem.find('input[type="hidden"]');
                var selectOption = select.find('option:selected');
                fieldValue = selectOption.val();
                hiddenInput.val(fieldValue);
                dataField = hiddenInput.attr('name');
                respDataType = '2';
            }
            
            if(isDeleteLink) {
                respDataType = '3';
            }
            
            
            
            data = {
                page: 10,
                id: rowId,
                table: sysTableId,
                value: fieldValue,
                field: dataField,
                datatype: respDataType
            };
            
            if(!isDeleteLink)
            {
                SaveDataTable(data,curItem);
            }
            else
            {
                DeleteDataRow(data,curItem);
            }

        });
        
        btnNo.on('click', function() {
            var curItem = $(this).parent();
            editableTableBtnClick(curItem);
        });
        
    }
    else
    {
        var multiple = curItem.find('a[data-type="multiple"]');
        if(multiple.length>0) // Множественный выбор
        {
            //alert('');
        }
        else
        {
            alert('Данное поле невозможно изменить.');
        }
    }
    
    e.preventDefault();
});

/*
function chatSetActive(data,obj) {
    obj.addClass('active');
    obj.attr('title','Заблокировать чат');
}

function chatSetDeactive(data,obj) {
    obj.removeClass('active');
    obj.attr('title','Активировать чат');
}
*/


function editableTableBtnClick(curItem) {
    curItem.find('input.active').removeClass('active');
    curItem.find('select').remove();
    curItem.find('a.info').removeClass('hide');
    curItem.find('.but').remove();
    var isDeleteLink = curItem.find('a.option-delete-link').length;
    if(isDeleteLink)
        curItem.find('b').remove();
}


function SaveDataTable(data,curItem) {
    curItem.find('a.info').load('/admin/handler.php', data, function(response, status, xhr){
        if(status!='error')
        {
            var curData = $(this).parent();
            var select = curData.find('select');
            $(this).removeClass('hide');
            curData.find('input').removeClass('active');
            curData.find('a.but').remove();
            if(select.length > 0)
            {
                $(this).attr('data-id', $(this).html());
                $(this).html(select.find('option[value="'+$(this).html()+'"]').html());
                select.remove();
            }
        }
        else
        {
            
        }
    });
}

function DeleteDataRow(data,curItem) {
    curItem.find('a.info').load('/admin/handler.php', data, function(response, status, xhr){
        if(status!='error' && response!='')
        {
            curItem.closest('.table-row').remove();
        }
        else
        {
            alert('При выполнении операции возникла ошибка.');
        }
    });
}




$(document).on('click', '#map_refresh', function(e) {
    var data = { page: 101 };
    actionType = 'refresh';
    
    var dataResult = GetServerData(data);
            
    dataResult.done(function(res){
        if(res.status)
        {
            init(res.options,res.distr_data,res.data,clusterer,Polygon,actionType);
        }
        $('.pd-panel-cont').addClass('active');
        $('.admin-panel .filter').addClass('active');
    });
    
    dataResult.fail(function(jqXHR, code, textStatus) {
        alert("При выполнении запроса произошла ошибка [ "+textStatus+" ]. Пожалуйста, обратитесь к системному администратору.");
        $('.pd-panel-cont').addClass('active');
    });
    
    e.preventDefault();
});



function setUserOptions(obj) {
    var value = '';
    var value_id = '';
    var option_id = '';
    var thisOption = obj;
    if(obj.closest('.options-panel').length>0)
    {
        var thisOptionPanel = obj.closest('.options-panel');
        thisOptionPanel.removeClass('active');
    }
    
    $('.admin-panel .filter').removeClass('active');
    
    thisOption.addClass('disabled');
    if(!thisOption.is('[data-option-id]'))
    {
        return false;
    }
    else
    {
        option_id = thisOption.attr('data-option-id');
        if(thisOption.find('option:selected').length > 0)
        {
            value_id = thisOption.find('option:selected').attr('data-id');
        }
    }
    
    if(thisOption.hasClass('active'))
    {
        value = 1;
    }
    else
    {
        value = 0;
        if(value_id!=0)
        {
            value = 1;
        }
    }

    
    var Data = {
        page: 105,
        option_id: option_id,
        option_value_id: value_id,
        option_value: value
    };
    
    var setUserOptions = SendDataJSON(Data);
                        
    setUserOptions.done(function(res) {
        
        if(res.status)
        {
            $('#map_refresh').click();
        }
        else
        {
            $('.pd-panel-cont').addClass('active');
            $('.admin-panel .filter').addClass('active');
        }
        
    });
                        
    setUserOptions.fail(function(jqXHR, code, textStatus) {
        console.log("При выполнении действия возникла ошибка на стороне сервера: [ "+textStatus+" ]. Пожалуйста, обратитесь к системному администратору.");
        alert('Ваша сессия устарела. Система направит Вас на форму авторизации.');
        location.reload();
        
        //userAuthByKey();
        $('.pd-panel-cont').addClass('active');
        $('.admin-panel .filter').addClass('active');
    });
    
}


$(document).on('click', '.options-panel.active a[data-option-id]', function(e) {
    setUserOptions($(this));
    e.preventDefault();
});


$(document).on('click', '.mwindow-bg', function(e) {
    CloseModWindow($(this).find('.mwindow'));
    e.preventDefault();
});

$(document).on('click', '.mwindow', function(e) {
    
    e.stopPropagation();
});

$(document).on('click', '#reg_user', function(e) {
  var captcha = 0;
  var captchaField = $('input[name="captcha"]');
  $('.form-error').html(null);
  if(captchaField.length>0)
  {
    captcha = captchaField.val();
  }
  
    if(checkDate($(this).parent('div'))) {
        var data = { 
            func: 3,
            data: {
                email: $('input[name="email"]').val(),
                alias: $('input[name="user_name"]').val(),
                phone: '',//$('input[name="phone"]').val(),
                password: $('input[name="password2"]').val(),
                captcha: captcha
            }
        };
        
        var regUser = GetServerResult(data);
        
        regUser.done(function(res){
          if(!res.status)
          {
              $('.reg-captcha img').attr('src','http://gorod.arhcity.ru/captcha/');
              $('.reg-captcha input').val(null);
              $('.form-error').html(res.message);
          }
          else
          {
              $('.container').html('<div class="green-blk">'+res.message+'</div>');
              $('.container').append('<a href="#" class="bl-link icon-key" id="to_auth">В личный кабинет</a><br/><br/>');
          }
        });
        
        
        regUser.fail(function(jqXHR, code, textStatus) {
            $('.reg-captcha img').attr('src','http://gorod.arhcity.ru/captcha/');
            serverResultError(textStatus);
        });
        
    }
  e.preventDefault();
});



function ShowInfoLine(ActiveCcount,caption) {
    lineInfo = mlist.find('.line-info');
    if(ActiveCcount==0) {
        lineInfo.html(caption);
        lineInfo.css('display','block');
    }
    else {
        mlist.find('.line-info').css('display','none');
        lineInfo.html('');
    }
}


function ShowRightPanel(panelCaption,data) {
    if($('.main-panel[id="panel'+data.page+'"]').length <= 0) {
        $('.main-panel').remove();
        panel = $('<div/>', { class: 'main-panel', id: 'panel'+data.page } );
        if(data.type!==undefined && data.type == 'long')
        {
            panel.css('width','90%');
        }
        
        var panelHeader = $('<div/>',{ class: 'mpanel-header', html: panelCaption });
        var panelCloser = $('<a/>', { class: 'mpanel-closer icon-right-open-big', title: 'Скрыть панель' });
        
        panelCloser.on('click',function() {
            CloseRightPanel(panel);
        });
        
        panelHeader.append(panelCloser);
        panel.append(panelHeader);
        panel.append('<div class="mpanel-cont"></div>');
        panel.find('.mpanel-cont').load('/admin/handler.php', data, function(){
            $('#mapcont').append(panel);
            if(!data.noscroller)
            {
                var height = panel.innerHeight()-50;
                $(this).css('height',height);
                $(this).mCustomScrollbar();
            }
            
            window.setTimeout("panel.addClass('active');",5);
        });
    }
}

function CloseRightPanel(obj) {
    var panel = obj;
    panel.removeClass('active');
    setTimeout("panel.remove();",300);
}

function CloseModWindow(obj) {
    obj.parent().remove();
}


function userAuthByKey() {

    var Data = {
        page: 122,
        key: localStorage.getItem('auth_key')
    };
    
    var userAuth = SendDataJSON(Data);
                        
    userAuth.done(function(res) {
        if(res.status)
        {
            location.reload();
        }
    });
                        
    userAuth.fail(function(jqXHR, code, textStatus) {
        serverResultError(textStatus);
    });
}

function SendDataJSON(sendData) {
    return $.ajax({
        type: "POST",
        url: "/admin/handler.php",
        data: sendData,
        dataType: "JSON"
    });
}

function SendData(sendData) {
    return $.ajax({
        type: "POST",
        url: "/admin/handler.php",
        data: sendData
    });
}

function GetServerData(sendData) {
    return $.ajax({
        type: "POST",
        url: "/admin/handler.php",
        data: sendData,
        dataType: "JSON"
    });
}


function GetServerResult(sendData) {
    return $.ajax({
        method: "POST",
        url: "../db/db_functions.php",
        data: sendData,
        dataType: "JSON"
    });
}


function LoadPage(data) {
    $('body').load('../admin/handler.php', data, function(){ //.php #container - для определения источника
        if(data.page == 4)  window.location.reload(); 
    });
}


function LoadContent(data) {
    $('.container').load('../admin/handler.php', data, function(){ //.php #container - для определения источника
        if(data.page == 4)  window.location.reload(); 
    });
}


function ShowModWindow(data,clickObj) {
    $('.mwindow-bg').remove();
    var wWidth = '80';
    if(clickObj.is('[data-width]'))
    {
        wWidth = clickObj.data('width');
    }
    modWindowBg = $('<div/>', { class: 'mwindow-bg' } );
    modWindow = $('<div/>', { class: 'mwindow', id: 'wd'+data.page, style: 'width: '+wWidth+'%;' } );
    var wdHeader = $('<div/>',{ class: 'mwindow-header icon-widget', html: data.caption });
    var wdCloser = $('<a/>', { class: 'mwindow-closer', title: 'Закрыть окно' });
    var wdFullScreen = $('<a/>', { class: 'mwfull-screen', title: 'Развернуть окно на весь экран' });
    var wdAddRow = $('<a/>', { class: 'mwindow-add-row', html: 'Добавить', title: 'Добавить новую запись' });
    var emptyContainer = $('<div/>', { class: 'mwindow-empty-container' });
    var searchContainer = $('<div/>', { class: 'mwindow-search' });
    var searchInput = $('<input/>', { type: 'text', class: 'mwindow-search-input', placeholder: 'Поиск по списку...' });

    wdCloser.on('click',function(e) {
        CloseModWindow(modWindow);
        e.preventDefault();
    });
    
    wdFullScreen.on('click',function(e) {
        if(modWindow.hasClass('full-screen'))
        {
            modWindow.removeClass('full-screen');
        }
        else
        {
            modWindow.addClass('full-screen');
        }
        e.preventDefault();
    });
    
    
    searchInput.on('keyup',function() {
        var value = $(this).val().toLowerCase();
        var mlist = $('.table-scroller .table.body');
        var searchIndex = mlist.find('.table-row a.searchable').length;
        if(searchIndex<=0)
        {
            alert('Для данного справочника нет полей, по которым можно осуществлять поиск.');
            return false;
        }
        
        mlist.find('div.table-row').each(function(index) {
            var thisRow = $(this);
            var ActiveCcount = 0;
            thisRow.find('a.searchable').each(function() {
                var thisVal = $(this).html().toLowerCase();
                if(false != thisVal.indexOf(value)+1)
                {
                    ActiveCcount++;
                }
            });
            
            if(ActiveCcount>0)
            {
                thisRow.css('display','inline-block');
            }
            else
            {
                thisRow.css('display','none');
            }
            
            
        });
    });
    
    
    
    wdAddRow.on('click',function(e) {
        if($('.wd-add-record-panel').length>0)
        {
            return false;
        }
        var addRecordPanel = $('<div/>', { class: 'wd-add-record-panel' });
        var tableRow = $('<div/>', { class: 'table-row' });
        var saveButton = $('<a/>', { html: 'Сохранить' });
        var closeButton = $('<a/>', { html: 'Отменить' });
        var tableId = $('.table.body').attr('data-table-id');
        
        
        closeButton.on('click',function(e) {
           addRecordPanel.removeClass('active');
           window.setTimeout("$('.wd-add-record-panel').remove();",300);
           window.setTimeout("$('.mwindow-cont').removeClass('disabled');",200);
           e.preventDefault();
        });
        
        saveButton.on('click',function(e) {
            var emptyCount = 0;
            $('.wd-add-record-panel input[data-required]').each(function(i,elem) {
                if($(this).val() == '')
                {
                    emptyCount++;
                    $(this).css('border','1px solid #b33b3b');
                }
                else
                {
                    $(this).css('border','1px solid #a9a9a9'); 
                }
            });
            
            if(!emptyCount)
            {
                var arData = $('.wd-add-record-panel input, .wd-add-record-panel select').serializeArray();
                var sData = {
                    page: 98,
                    table_id: tableId,
                    data: arData
                };
                var result = SendData(sData);
                result.done(function(res) {
                    if(res=='true')
                    {
                        var res = SendData(data);
                        res.done(function(contData) {
                            addRecordPanel.removeClass('active');
                            window.setTimeout("$('.wd-add-record-panel').remove();",300);
                            window.setTimeout("$('.mwindow-cont').removeClass('disabled');",200);
                            modWindow.find('.mwindow-cont').html(contData);
                            var tScroller = $('.table-scroller');
                            var wHeight = window.innerHeight-100;
                            tScroller.css('height',(wHeight-80)+'px');
                            tScroller.mCustomScrollbar();
                        });
                        result.fail(function(jqXHR, code, textStatus) {
                            alert("При выполнении запроса произошла ошибка [ "+textStatus+" ]. Пожалуйста, обратитесь к системному администратору.");
                        });
                    }
                    else
                    {
                        alert('Не получилось успешно записать данные в таблицу.');
                    }
                });
                
                result.fail(function(jqXHR, code, textStatus) {
                    alert("При выполнении запроса произошла ошибка [ "+textStatus+" ]. Пожалуйста, обратитесь к системному администратору.");
                });
            }
            else
            {
                alert('Заполните обязательные поля, отмеченные красным цветом');
            }
            
            e.preventDefault();
        });
        
        var topRow = modWindow.find('.table.header .table-row:first');
        topRow.children('div').each(function(i,elem) {
            var curDiv = $(this).clone();
            if(curDiv.is('[data-field]'))
            {
                var dataField = curDiv.attr('data-field');
                var hSelect = curDiv.find('.hidden-select');
                if(hSelect.length>0)
                {
                    var fSelect = hSelect.find('select');
                    fSelect.attr('name',dataField);
                    hSelect.remove();
                    curDiv.find('span:first').remove();
                    curDiv.append(fSelect);
                    
                }
                else
                {
                    if(curDiv.is('[data-required]'))
                        curDiv.html('<input type="text" name="'+dataField+'" placeholder="Введите..." data-required>');
                    else
                        curDiv.html('<input type="text" name="'+dataField+'" placeholder="Введите...">');
                }
            }
            else
            {
                if(curDiv.html()=='#')
                {
                    curDiv.html(null);
                }
            }
            
            tableRow.append(curDiv);
        });
        
        addRecordPanel.append(tableRow);
        addRecordPanel.append(saveButton).append(closeButton);
        $('.mwindow-cont').addClass('disabled');
        
        if(data.type=='list')
        {
            $('.mwindow-cont').prepend(addRecordPanel);
            window.setTimeout("$('.wd-add-record-panel').addClass('active');",10);
        }
        
        e.preventDefault();
    });
    
    
    wdHeader.append(emptyContainer);
    
    searchContainer.append(searchInput);
    wdHeader.append(searchContainer);
    
    
    if(data.type=='list')
    {
        wdHeader.append(wdAddRow);
    }
    wdHeader.append(wdCloser);
    wdHeader.append(wdFullScreen);
    modWindow.append(wdHeader);
    modWindow.append('<div class="mwindow-cont"></div>');
    
    
    modWindow.find('.mwindow-cont').load('/admin/handler.php', data, function(response, status, xhr){
        if ( status != "error" )
        {
            $('body').append(modWindowBg);
            var wHeight = window.innerHeight-100;
            modWindow.css('height',wHeight+'px');
            $(this).css('height',(wHeight-50)+'px');
            modWindowBg.append(modWindow);
            window.setTimeout("modWindow.addClass('active');",10);
            
            var tScroller = $('.table-scroller');
            var wHeight = window.innerHeight-100;
            tScroller.css('height',(wHeight-80)+'px');
            tScroller.mCustomScrollbar();
        }
        else
        {
            // Вывод сообщения в плавающей панельке
            var hintId = 'hint-mwindow';
            var headerText = 'Информация для пользователя';
            var typeText = 'Настройки';
            var infoText = 'Не удается определить настройки, которые вы пытаетесь открыть. Возможно, данный раздел находится на стадии доработки.';
            ShowGlobalHint(headerText,typeText,infoText,hintId); 
        }
        clickObj.removeClass('active');
    });
}



function checkDate(form) {
    var res = false;
    var err_count = 0;
    var err_index = 0;
    var err_mess = '';
    var pass = '';
    var data = form.find('input[type=text],input[type=password],input[type=tel]');
    form.find('.input-error').remove();
    
    $.each(data, function(i) {
        err_index = 0; err_mess = '';
        if($(this).is('[data-type]')) {
            if($(this).attr('data-type')=='email')
                if(!validateEmail($(this).val())) {
                    err_count++;
                    err_index=1;
                    err_mess = 'Проверьте email';
                }
        } else {
            if($(this).val() == '') {
                err_count++; 
                err_index=1;
                err_mess = 'Поле ожидает ввода данных';
            } else {
                if($(this).attr('type')=='password') {
                    if($(this).attr('name')=='password1') 
                        pass = $(this).val();
                    if($(this).attr('name')=='password2') 
                        if((pass!='') && $(this).val() != pass) {
                            err_count++; 
                            err_index=1;
                            err_mess = 'Введённые пароли не совпадают';
                        }
                }
            }
            
        }
        
        if(err_index) {
            if($(this).parent().hasClass('field')) {
                $(this).parent().after('<p class="input-error">'+err_mess+'</p>');
            } else {
                $(this).after('<p class="input-error">'+err_mess+'</p>');
            }
        }
    });
    if(err_count==0)
        res = true;
    return res;
}

function getBase64(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = error => reject(error);
  });
}

function serverResultError(textStatus) {
    alert("При выполнении запроса произошла ошибка [ "+textStatus+" ]. Пожалуйста, обратитесь к системному администратору.");
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
