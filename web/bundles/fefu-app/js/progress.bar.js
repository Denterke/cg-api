// $(function(){

//    var options = {
//       beforeSend: function(xhr) { 
//          clearProposalForm();
//          $('input[type=file][max-size]').each(function() {
//             if (this.id !== 'fake_input') {
//                fileSize = this.files[0].size;
//                maxSize = parseInt($(this).attr('max-size'), 10);
//                if (fileSize > maxSize) {
//                   $.fancybox(
//                      '<span style="color: red; font-weight: bold; display: block; margin: 30px;">' + 'Размер каждого файла должен быть меньше 15 мегабайт!' + '</span>',
//                      {
//                         'autoDimensions'  : false,
//                         'width'           : 360,
//                         'height'          : 'auto',
//                         'transitionIn'    : 'none',
//                         'transitionOut'   : 'none'
//                      }
//                   );
//                   xhr.abort();
//                }
//             }
//          });
//       },
//       uploadProgress: function(event, position, total, percentComplete) {
//          console.log('upload progress');
//       },
//       success: function() {
//          console.log('success');
//       },
//       complete: function(response) {
//          console.log('complete');
//          console.log(JSON.stringify(response));
//          console.log(response.responseText);
//          var data = $.parseJSON(response.responseText);

//          if (data.result) {
//             $('#category_choose li').removeClass('active');
//             clearProposalForm();
//             $('#send_window .form-control').each(function() {
//                $(this).val('');
//             });
//             while (rowcnt > 0) {
//                deleteRow(rowcnt);
//             }
//             $.fancybox(
//                '<span style="color: green; font-weight: bold; display: block; margin: 30px;">Заявка отправлена! Спасибо!</span>',
//                {
//                   'autoDimensions'  : false,
//                   'width'           : 360,
//                   'height'          : 'auto',
//                   'transitionIn'    : 'none',
//                   'transitionOut'   : 'none'
//                }
//             );
//          } else {
//             if (data.error_field) {
//                $('#send_window #' + data.error_field).addClass('wrong')
//             }
//             $('#send_window div.error').text(data.message);
//             // $.fancybox(
//             //    '<span style="color: red; font-weight: bold; display: block; margin: 30px;">' + data.message + '</span>',
//             //    {
//             //       'autoDimensions'  : false,
//             //       'width'           : 360,
//             //       'height'          : 'auto',
//             //       'transitionIn'    : 'none',
//             //       'transitionOut'   : 'none'
//             //    }
//             // );
//          }
//       }
//    };

//    $('#proposal').ajaxForm(options);

//    // $('#proposal').submit(function() {

//    // });

//    function fakeInputChange() {
//       rowcnt++;
//       var fileName = document.getElementById("fake_input").value;
//       var cell_classes = ["num", "name", "delete"];
//       var cell_names = [rowcnt, fileName, "Удалить"];
//       var table = document.getElementsByClassName("attachments")[0];
//       var row = table.insertRow(-1);
//       row.setAttribute('id', 'row' + rowcnt);
//       var cells = [];
//       for (var i = 0; i < 3; i++) {
//          cells[i] = document.createElement("td");
//          cells[i].className = cell_classes[i];
//          cells[i].innerHTML = cell_names[i];
//          row.appendChild(cells[i]);
//       }

//       cells[2].onclick = function() {
//          deleteRow(parseInt(cells[2].parentNode.firstChild.innerHTML));
//       };
//       var parent = $(this).parent();
//       $(this).appendTo(row);
//       $(this).toggleClass("file_input");
//       $(this).attr('id', 'fi' + rowcnt);
//       $(this).attr('name', 'fi' + rowcnt);
//       $(this).onchange = function() {
//          var fileName = document.getElementById("fi" + rowcnt).value;
//          if (fileName) {
//             $('#row' + rowcnt).find("td").eq(1).html(fileName);
//             $('#row' + rowcnt).toggleClass("active");
//             $.fancybox.center();
//          }
//       };
//       var fakeInput = document.createElement("input");
//       fakeInput.type="file";
//       fakeInput.setAttribute('id', 'fake_input');
//       fakeInput.setAttribute('max-size', 15728640);
//       fakeInput.onchange = fakeInputChange;
//       parent.append(fakeInput);
//       $.fancybox.center();
//    }

//    $('#fake_input').change(fakeInputChange);

//    $('#add_file').click(function() {
//       $('#fake_input').click();
//    });
// });