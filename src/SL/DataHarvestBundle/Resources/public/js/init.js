            //=================WORKS  FF Safari Chrome=====================

            function resetpage() {
                window.location.reload();
            }
            function openinfo () {
                document.getElementById("infolayer");
                $("#infolayer").removeClass("infolayer-off");

                }
                function closeinfo () {
                document.getElementById("infolayer");
                $("#infolayer").addClass("infolayer-off");
                }

            function showinfo () {
                document.getElementsByClassName("moreinfo");

            $(".moreinfo").addClass("moreinfo-on");

                }
            function hideinfo () {
                document.getElementsByClassName("moreinfo");

            $(".moreinfo").removeClass("moreinfo-on");

                }



            // If the response from the server is XML, and you want to parse it as an XML object, use the responseXML property:
            // (Parsing is splitting up information into its component parts http://xml.silmaril.ie/parsers.html )

            $(function () {  //does not require <body onLoad="getdata()" to fire>

            var xmlHttp = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") :  new XMLHttpRequest();
                xmlHttp.open("GET","{{ asset(assetPath ~ '/xml/TRMMainstreet.xml') }}",false);
                xmlHttp.send(null);
                var xmlDoc = xmlHttp.responseXML;
                var xmlHttp = window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") :  new XMLHttpRequest();
                xmlHttp.open("GET","{{ asset(assetPath ~ '/xml/SLNSWMainStreets.xml') }}",false);
                xmlHttp.send(null);
                var sydxmlDoc = xmlHttp.responseXML;




                    var tw_urllink = xmlDoc.getElementsByTagName("url");
                    var tw_title= xmlDoc.getElementsByTagName("description");
                    var tw_date= xmlDoc.getElementsByTagName("date");
                    var tw_img = xmlDoc.getElementsByTagName("media");

                    /////////////////////////////////////////////////////


                    var syd_title= sydxmlDoc.getElementsByTagName("description");
                    var syd_date= sydxmlDoc.getElementsByTagName("date");
                    var syd_urllink = sydxmlDoc.getElementsByTagName("record_url");
                    var syd_img = sydxmlDoc.getElementsByTagName("image_url");

                    var sydimg = '';
                    var twimg = '';
                    for (i=0;i<syd_title.length;i++)
                   {



                    //////////////////////////////////////////////////////* TWEED *//////////////////////////////////////////////////////

                    var twrecord = (tw_urllink[i].childNodes[0].nodeValue);


                    var countryimg = (tw_img[i].childNodes[0].nodeValue);
                    //alert(countryimg)
                    var twimg =  twimg + '<div id="twitem' +[i]+ '" data-date="' + tw_date[i].childNodes[0].nodeValue + '" style="background: url(' +countryimg+ ')no-repeat 0 0;background-size: auto 100%;"><a href="' +twrecord+ '" target="_blank"><img class="twicon" src="{{ assetPath ~ '/images/info.png' }}"/><img class="placeholder" src="' +countryimg+ '"/></a><a id="twinfo' +[i]+'" href="#" onmouseover="showinfo();" onmouseout="hideinfo();"><div class="moreinfo"><div class="title">' +(tw_title[i].childNodes[0].nodeValue) +'<br/>' +(tw_date[i].childNodes[0].nodeValue) +'</div></div></a></div>';



                    //////////////////////////////////////////////////////*SYDNEY*//////////////////////////////////////////////////////

                var sydrecord = (syd_urllink[i].childNodes[0].nodeValue);

                var cityimg = (syd_img[i].childNodes[0].nodeValue);

                var sydimg =  sydimg + '<div id="syditem' +[i]+ '" data-date="' + syd_date[i].childNodes[0].nodeValue + '" style="background: url(' +cityimg+ ')no-repeat 0 0; background-size: auto 100%;"><a href="' +sydrecord+ '" target="_blank"><img class="sydicon" src="{{ assetPath ~ '/images/info.png' }}"/><img class="placeholder" src="' +cityimg + '"/></a><a id="sydinfo' +[i]+'" href="#" onmouseover="showinfo();" onmouseout="hideinfo();"><div class="moreinfo"><div class="title">' +(syd_title[i].childNodes[0].nodeValue) +'<br/>' +(syd_date[i].childNodes[0].nodeValue) +'</div></div></a></div>';


                    var file1 = twimg;
                    var file2 = sydimg;



                    }



                 document.getElementById("tweed").innerHTML=file1;
                 document.getElementById("sydney").innerHTML=file2;

                 var docWidth = $(document).width();
                 var halfdocWidth = eval(docWidth * 0.5);
                 var onepercent = eval(docWidth * 0.01)



            for (var i=0; i<tw_title.length; i++)


            $("#syditem"+[i]).hover

             (

              function (file2) //syd
              {

                var sydhoveritem = ($(this)[0].id);
                var tweedhoveritem = sydhoveritem.substring(3,10);
                //alert(twhoveritem)

                var sydimgoffset = $(("#")+sydhoveritem).offset();

                var sydimgpos = sydimgoffset.left


                $(("#")+sydhoveritem).toggleClass("eighty",2000, "easeOutSine");
                $(("#tw")+tweedhoveritem).toggleClass("eighty",2000, "easeOutSine");

                var h;
                var m = document.getElementsByClassName("eighty");
                var x = document.getElementsByClassName("eighty").length;

                    if (x == 2)
                    {
                    var sydwidth = $(("#")+sydhoveritem).width();
                    var twwidth = $(("#tw")+tweedhoveritem).width();
                    $("#title-center").css('opacity','0.05');
                    //alert(twwidth); //css width needs to be set to auto to get width

                    if (eval(sydimgpos + sydwidth) > docWidth)
                        {
                    //alert("no room " + imgpos);
                    $(("#")+sydhoveritem).css('position','absolute');
                    $(("#")+sydhoveritem).css('left',(eval(sydimgpos-sydwidth+onepercent))+'px');
                    $(("#")+sydhoveritem).css('top','0');
                    $(("#")+sydhoveritem).css('z-index','1000');


                    //=========================================
                    $(("#tw")+tweedhoveritem).css('position','absolute');
                    $(("#tw")+tweedhoveritem).css('left',(eval(sydimgpos-twwidth+onepercent))+'px');
                    $(("#tw")+tweedhoveritem).css('top','0');
                    $(("#tw")+tweedhoveritem).css('z-index','1000');
                        }



                    }
                    else
                        {
                    $("#title-center").css('opacity','0.1');
                    $(("#")+sydhoveritem).css('position','');
                    $(("#")+sydhoveritem).css('left','');
                    $(("#")+sydhoveritem).css('top','');
                    $(("#")+sydhoveritem).css('z-index','');
                    //=====================================

                    $(("#tw")+tweedhoveritem).css('position','');
                    $(("#tw")+tweedhoveritem).css('left','');
                    $(("#tw")+tweedhoveritem).css('top','');
                    $(("#tw")+tweedhoveritem).css('z-index','');
                        }

              }
              );

            /* =========== */

            for (var j=0; j<tw_title.length; j++)

            $("#twitem"+[j]).hover

            (

              function (file1) //tweed
              {

                var tweedhoveritem = ($(this)[0].id);
                var sydhoveritem = tweedhoveritem.substring(2,9);
            //alert(sydhoveritem);
            //alert(tweedhoveritem);

                var twimgoffset = $(("#")+tweedhoveritem).offset();
                var twimgpos = twimgoffset.left

                //var sydimgoffset = $(("#")+hoveritem).offset();
                //var sydimgpos = sydimgoffset.left

                $(("#")+tweedhoveritem).toggleClass("eighty",2000, "easeOutSine");


                $(("#syd")+sydhoveritem).toggleClass("eighty",2000, "easeOutSine"); //comment this out to see tweed hover working

                var h;
                var m = document.getElementsByClassName("eighty");
                var x = document.getElementsByClassName("eighty").length;





                    if (x == 2)

                    {
                        //alert(x);
                    var twwidth = $(("#")+tweedhoveritem).width();
                    var sydwidth = $(("#syd")+sydhoveritem).width();
                    $("#title-center").css('opacity','0.05');

                    //alert(sydwidth); //css width needs to be set to auto to get width

                    if (eval(twimgpos + twwidth) > docWidth)
                        {
                    //alert("no room " + imgpos);

                    $(("#")+tweedhoveritem).css('position','absolute');
                    $(("#")+tweedhoveritem).css('left',(eval(twimgpos-twwidth+onepercent))+'px');
                    //$(("#")+tweedhoveritem).css('border','solid 1px #c03');
                    $(("#")+tweedhoveritem).css('top','0');
                    $(("#")+tweedhoveritem).css('z-index','1000');

                    //alert(imgpos);
                    //=========================================
                    $(("#syd")+sydhoveritem).css('position','absolute');
                    $(("#syd")+sydhoveritem).css('left',(eval(twimgpos-sydwidth+onepercent))+'px');
                    $(("#syd")+sydhoveritem).css('top','0');
                    $(("#syd")+sydhoveritem).css('z-index','1000');
                        }


                    }
                    else
                        {
                            $("#title-center").css('opacity','0.1');
                    $(("#")+tweedhoveritem).css('position','');
                    $(("#")+tweedhoveritem).css('left','');
                    $(("#")+tweedhoveritem).css('top','');
                    $(("#")+tweedhoveritem).css('z-index','');
                    $(("#")+tweedhoveritem).css('border','none');
                    //======================================
                    $(("#syd")+sydhoveritem).css('position','');
                    $(("#syd")+sydhoveritem).css('left','');
                    $(("#syd")+sydhoveritem).css('top','');
                    $(("#syd")+sydhoveritem).css('z-index','');

                        }
                }

            );


            });

