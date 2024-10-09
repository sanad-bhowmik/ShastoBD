 <!--  Start Footer-->
 <div class="app-wrapper-footer">
     <div class="app-footer">
         <div class="app-footer__inner">
             <div class="app-footer-left">
                 <!--  <ul class="nav">
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                Footer Link 1
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                Footer Link 2
                                            </a>
                                        </li>
                                    </ul> -->
             </div>
             <div class="app-footer-right">
                 <ul class="nav">
                     <li class="nav-item">
                         <a href="#" class="nav-link">
                             SasthoBD
                         </a>
                     </li>

                 </ul>
             </div>
         </div>
     </div>
 </div>


 </div>

 </div>
 </div>
 </body>





 <script>
$(document).ready(function() {



    $(document).on("click", ".myImg", function() {

        // Get the image and insert it inside the modal - use its "alt" text as a caption
        $("#img01").attr("src", $(this).attr("src"));
        $("#myImgModal").css({
            "display": "block"
        });


    });


    $(document).on("click", ".Imgclose", function() {

        $("#myImgModal").css({
            "display": "none"
        });

    });



});
 </script>


 <script type="text/javascript">
var pathname = window.location.href;
//console.log(1);
//console.log(pathname);
var partsArray = pathname.split('/');
//console.log(partsArray);
//local
//var url =  partsArray[4];
//server
var url = partsArray[3];
//console.log(url);
var a_s = document.querySelectorAll('li a');
//a_s.removeClass('mm-active');

//console.log(a_s);
$(document).ready(function() {

    //console.log(1);
    if (url != 'dashboard.php') {
        //  console.log(url);
        $.ajax({
            url: "get/check_url_permission.php",
            method: "POST",
            data: {
                url: url
            },

            success: function(data) {
                if (data == 1) {
                    //  alert('thanks');


                } else {
                    //console.log("permission: "+data+"logout");
                    // window.location.href = "index.php?logout=true";
                }


            }
        });


    }


    function toastrErrorAlert(msg) {
        toastr.error(msg).fadeOut(1000);
    }

    function toastrSuccessAlert(msg) {
        toastr.success(msg).fadeOut(1000);
    }

    function toastrWarningAlert(msg) {
        toastr.warning(msg).fadeOut(1000);
    }

    function toastrInfoAlert(msg) {
        toastr.info(msg).fadeOut(1000);
    }




});



for (var i = 0; i < a_s.length; i++) {
    //console.log(a_s[i].getAttribute('href'));
    //console.log(url);
    if (a_s[i].getAttribute('href') == url) {

        a_s[i].classList.add("mm-active");
        a_s[i].parentElement.classList.add("mm-active");
        // console.log(a_s[i].parentElement);

    }

}
 </script>


 </html>
 <!--  end Footer-->