<?php
$access = "public";

include "/var/www/poojyam.in//inc/common.php";

include "header.php";
?>
<section id="signin_alt" class="authenty signin-alt" >
    <div class="section-content">
      <div class="wrap">
          <div class="container">
                <div class="row" data-animation="fadeInUp" style="opacity: 1">
                    <div class="col-md-5 col-md-offset-3"  style="margin-bottom: 50px">
                        <div class="normal-signin">
                            <div class="title">
                                <h3>Forgot password</h3>
                            </div>
                            <div class="form-main">
                                <form  name="registartion"  method="post" action="index.php">
                                    <div class="form-group">
                                        <label>Please Enter your email address we will send new password to your email address</label>
                                        <div class="un-wrap">
                                            <i class="fa fa-user"></i>
                                            <input type="text" class="form-control" placeholder="Email" name="email" required="required" >
                                        </div>
                                    </div>
                                    <div class="row top-buffer">
                                        <div class="col-md-4 col-md" style="width: 130px">
                                        <button name="submit_login" type="submit" class="btn btn-block signin">Sign In</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include "footer.php"; ?>
