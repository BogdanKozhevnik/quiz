 var question = 1; var answer=1;
     // // onload event of body element
     // function bodyOnLoad(){
     //     addListItem("body onload");
     //     var teg_form = document.createElement('form');
     //     document.body.appendChild(teg_form);
     // }
     //
     // // window.onload
     // window.onload = function() {
     //     addListItem("window onload");
     //     var teg_form = document.createElement('form');
     //     document.body.appendChild(teg_form);
     // };
     //
     // // event listener
     // document.addEventListener('DOMContentLoaded', function() {
     //     addListItem("event listener DOMContentLoaded2");
     // }, false);
     //
     // // jquery .ready
     // $(document).ready(function(){
     //     addListItem("jquery document ready");
     // });
 // Prevents event bubble up or any usage after this is called.
// function stopEvent(e) {
//      if (!e)
//          if (window.event) e = window.event;
//          else return;
//      if (e.cancelBubble != null) e.cancelBubble = true;
//      if (e.stopPropagation) e.stopPropagation();
//      if (e.preventDefault) e.preventDefault();
//      if (window.event) e.returnValue = false;
//      if (e.cancel != null) e.cancel = true;
//  }
 function stopDefAction(evt) {
     evt.preventDefault();
     evt.stopPropagation();
 }
 //====================================
 var question = 1;
 function addInputQuestion() {
// var q_count = question;
// var q_count_input = '<input >';

     var text_answer_add= '<input type="text" class="add_answ_text" id="add_answ_text'+question+'" name="add_answ_text"/>';
     var btn_answer = '<br><button class="btn_answer" id="answ_id" onclick="addInputAnswer();return false">Добавить ответ</button>';
     // var check_answer = '<input type="checkbox" id="check_answer'+question+'"><label>true/false</label>';
     // var check_answer = '<input type="checkbox" name="check_answer" class="check_answer'+question+'" id="check_answer" value="checked"><label>correct/wrong</label>';
     // var btn_answ_add_txt = btn_answer+text_answer_add+check_answer;
     var btn_answ_add_txt = btn_answer+text_answer_add;
     var quetion_value = document.getElementById('question-input').value;
     // var question_num = "<div>" + value + btn_answer+"</div>"+teg_form;
     // var question_num = "<div><label>№" + value +btn_answer+"<add_answ_text/label></div>";
     // var question_num = "<div id='question_num'>№" +question+") "+"<b id='question_num"+question+"'>"+quetion_value+"</b>"+btn_answ_add_txt+"</div>";
    var q_val = '<b>'+quetion_value+'<b>';
     var question_num = "<div id='question_num"+question+"'>№" +question+") "+"<input name='questions["+question+"][question]' id='question_text' readonly='readonly' value='"+quetion_value+"'/>"+btn_answ_add_txt+"</div>";
     console.log('here', question_num);
     // var btn_answer = '<button class="btn_answer" id="answ_id" onclick="addInputAnswer()">Добавить ответ</button>';
     // var btn_delete_q = '<button class="btn_del_q" id="btn_del_q" onclick="del_question()">Удалить вопрос</button>';
     document.getElementById('questions').insertAdjacentHTML('beforeend', "<li>"+question_num+"</li>");
     //
     var elems = document.getElementsByClassName('.btn_answer');
     for(i=0; i < elems.length; i++) {
         elems[i].style.marginLeft = '10px';
         elems[i].style.marginTop = '4px';
         // elems[i].innerHTML = '+ ответ к вопросу №' + (i+1);

// this is the preferred approach:
//          let elem = document.getElementById('answ_id');
//          elem.addEventListener('click', stopDefAction, false);
//          document.querySelector('button').addEventListener('click', function (e) {
//              e.preventDefault();'click', stopDefAction, false
//              e.stopPropagation();
//          }, false);
//          document.getElementById('answ_id').addEventListener(
//              );
     }
     question ++;
 }
 var id_answ = 0;
 var answer = 1;
function addInputAnswer() {
    id_answ ++;
    var answ = (question-1);
    //==========ajax
    // $.ajax({
    //     type: "POST",
    //     url: 'admin.php',
    //     dataType: "json",
    // }).done(function() {
    //
    //     var inp = document.getElementsByClassName('check_answer'+answ+'');
    //     //     for (var i = 0; i < inp.length; i++) {
    //     //         if (inp[i].type == "checkbox" && inp[i].checked) {
    //     //             alert('checked!');
    //     //         } else {
    //     //             alert('unchecked!');
    //     //         }
    //     //     }
    // });

    //==============jquery==працює але тільки на 1 елемент
    // if ($("#check_answer").prop("checked")){
    //     alert('checked!');
    // }else {
    //     alert('unchecked!');
    // }


    var ansver_value = document.getElementById('add_answ_text'+answ+'').value;
    // var teg_form ="<form></form>";";
    // var answerInput = '<input type="radio" name="radio_'+answ+'"  id="" class=""/><input type="text" name="questions['+answ+'][answers]['+answer+']" class="answ_text'+answ+'" id="txt_answer'+id_answ+'" readonly="readonly" value="'+ansver_value+'" placeholder="Ответ *"/>';
    //var answerInput = '<input type="checkbox" id="check_answer'+answ+'" name="radio_'+answer+'"  class=""/><input type="text" name="questions['+answ+'][answers]['+answer+']" class="answ_text'+answ+'" id="txt_answer'+id_answ+'" readonly="readonly" value="'+ansver_value+'" placeholder="Ответ *"/>';
    var answerInput = '<input type="checkbox" id="check_answer'+answ+'" name="questions['+answ+'][checkbox]['+answer+']"  class=""/><input type="text" name="questions['+answ+'][answers]['+answer+']" class="answ_text'+answ+'" id="txt_answer'+id_answ+'" readonly="readonly" value="'+ansver_value+'" placeholder="Ответ *"/>';
    // var answerInput = '<input name="radio_'+answ+'" type="radio" class="answ_text'+answ+'" id="txt_answer'+id_answ+'"/>'+ansver_value+'';
    // var btn_delete_a = '<button class="btn_del_a'+answ+'" id="btn_del_a'+id_answ+'" onclick="del_answer();return false">Удалить ответ</button>';
    // var radio_text = document.getElementById('questions').insertAdjacentHTML('beforeend', "<p>"+answerInput+btn_delete_a+"</p>");
    var radio_text = document.getElementById('questions').insertAdjacentHTML('beforeend', "<p>"+answerInput+"</p>");
    //нужно добавлять ответы перед div вопроса под №, для того чтобы предыдущая кнопка не + ответ в следующий вопрос
    // var radio_text = document.getElementById("question_num"+question+"").insertAdjacentHTML('beforeend', "<p>"+answerInput+btn_delete_a+"</p>");

    // parent.appendChild(radio_text);
    var elems = document.getElementsByClassName('btn_del_a'+answ+'');
    for(i=0; i < elems.length; i++) {
        elems[i].style.marginLeft = '5px';
    }
    answer ++;
    //==========count of question====javascript
    // const listItems = document.querySelectorAll('#questions li');
    // for (let li = 1; li < listItems.length; li++) {
    //     // alert (listItems[i].textContent);
    //     // alert (i);
    // }
    // var dwdw = document.querySelectorAll('li').length;
    // // alert (dwdw);
    // document.createElement('input');

    //=====count of question====jquery
    // $( "li" ).each(function() {
    //     $( this ).addClass( "foo" );
    // });
    //====count of checkbox====jquery

    $("[type=checkbox]").change(function(){
        var count_checkbox = $("input:checkbox").length;
        $("#countcheckbox").attr("value",count_checkbox);
        $("#countcheckbox").attr("name","count_checkbox");
        var check_count = $("input:checked").length;
        $("#countchecked").attr("value",check_count);
        $("#countchecked").attr("name","count_checked");
        var uncheck_count = $("input:checked").length;
        $("#countunchecked").attr("value",(count_checkbox-check_count));
        $("#countunchecked").attr("name",("count_unchecked"));
    });
}

 function addListItem(text){

     var container = document.getElementById("container");
     var li = document.createElement("li");
     li.appendChild(document.createTextNode(text));
     container.appendChild(li);
 }
 // function addTagForm(tagform){
 //     var form_container = document.getElementById("form_container");
 //     var form = document.createElement("form");
 //     // form.appendChild(document.createElement(tagform));
 //     li.appendChild(document.createTextNode(tagform));
 //     form_container.appendChild(form);
 // }
