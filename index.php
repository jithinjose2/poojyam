<?php
$access = "public";


include "/var/www/poojyam.in//inc/common.php";
if(isset($_POST['submit_login'])){
    if($user = User::login($_POST['email'],$_POST['password'])){
        $_SESSION['user_id']    = $user['user_id'];
    }else{
        $error = $l[1];
    }
}



if(isset($_POST['submit_signup'])){
    if(!empty($_POST['email']) && !empty($_POST['name']) && !empty($_POST['password']) && !empty($_POST['rpassword'])){
        if($_POST['password']!=$_POST['rpassword']){
            $error = $l[2];
        }
        if(!User::EmailAvailable($_POST['email'])){
            $error = $l[3];
        }
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $error = $l[4];
        }
        if(!isset($error)){
            if(User::createUser($_POST['email'],$_POST['name'],$_POST['password'])){
                $message = $l[5];
            }
        }
    }
}

if(isset($_SESSION['user_id'])){
    if(isset($_REQUEST['from_page'])){
        header('location:'.$_REQUEST['from_page']);
    }else{
        header('location:'.HOME.'home');
    }
    die();
}

if($_SERVER['HTTP_REFERER']=="https://www.facebook.com/" && !isset($_SESSION['user_id'])){
    header('location:'.HOME.'openid/facebook');
}

include "header.php";
?>
<script>
    var home = "<?php echo HOME?>";
    
    function popitup(url) {
        newwindow=window.open(url,'name','height=650,width=900,display=popup');
        newwindow.popoppo = true;
        if (window.focus) {newwindow.focus()}
        return false;
    }
        
    $(document).ready(function(){
        
        $(".fb_login").click(function(){
            popitup(home+'openid/facebook');
            return false;
        });
        
        $(".google_login").click(function(){
            popitup(home+'openid/google');
            return false;
        });
        
    });
    
    <?php if(isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],'facebook.com')!==false){ ?>
    //window.location="<?php echo HOME?>/openid/facebook";
    <?php } ?>
</script>
<script src="<?php echo HOME?>js/ctm.js"></script>
    <section id="signin_alt">
        <div class="section-content">
          <div>
              <div class="container">
                    <div class="row" data-animation="fadeInUp" style="opacity: 1">
                        <?php showAlert() ?>
                      <div class="col-md-4 card">
                            
                            <div class="normal-signin">
                                
                                <div class="title">
                                    <h4><?php echo $l[6]?></h4>
                                </div>
                              <div class="form-main">
                                  <form name="login" method="post" action="index.php">
                                      <div class="form-group">
                                            <div class="un-wrap">
                                                <input type="text" class="form-control" placeholder="<?php echo $l[7]?>" required="required" name="email">
                                            </div>
                                            <div class="pw-wrap">
                                                <input type="password" class="form-control" placeholder="<?php echo $l[8]?>" required="required" name="password">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="icheckbox_minimal-orange checked" style="position: relative;">
                                                        <input type="checkbox" checked=""><label style="font-size:12px;"><?php echo $l[9]?></label></div>
                                                </div>
                                            </div>
                                            <div class="row top-buffer">
                                                <div class="col-md-4 col-md" style="width: 100%">
													<button name="submit_login" type="submit" class="btn btn-block"><?php echo $l[10]?></button>
													<div class="sns-signin" style="margin:10px 10px 10px 0px; float: left;">
														<a href="http://poojyam.in/#" class="facebook fb_login"><img src="/img/fb-64.png" height="25" width="25"></a>
													</div>
													<div class="sns-signin" style="margin:10px 10px 10px 0px; float: left;">
														<a href="http://poojyam.in/#" class="google-plus google_login"><img src="/img/gplus-64.png" height="25" width="25"></a>
													</div>
                                                </div>
                                            </div>
                                    </div>
                                    </form>	
                                </div>		
                        </div>
                        </div>
                        <div class="col-md-1">
                            <div class="horizontal-divider"></div>
                        </div>
                        <div class="col-md-4 card" style="margin-bottom: 50px">
                            <div class="normal-signin">
                                
                                <div class="title">
                                    <h4><?php echo $l[11]?></h4>
                                </div>
                              <div class="form-main">
                                  <form  name="registartion"  method="post" action="index.php">
                                      <div class="form-group">
                                            <div class="un-wrap">
                                                <input type="text" class="form-control" placeholder="<?php echo $l[12]?>" name="email" required="required" >
                                            </div>
                                            <div class="un-wrap">
                                                <input type="text" id="chat_text" class="form-control" placeholder="<?php echo $l[13]?>" name="name" required="required">
                                            </div>
                                            <div class="pw-wrap">
                                                <input type="password" class="form-control" placeholder="<?php echo $l[8]?>" name="password" required="required">
                                            </div>
                                            <div class="pw-wrap">
                                                <input type="password" class="form-control" placeholder="<?php echo $l[14]?>" name="rpassword"required="required">
                                            </div>
                                            <div class="row top-buffer">
                                                <div class="col-md-4 col-md" style="width: 100%">                                                    
													<button type="submit" name="submit_signup" class="btn btn-block signin"><?php echo $l[15]?></button>
													<div class="sns-signin" style="margin:10px 10px 10px 0px; float: left;">
														<a href="http://poojyam.in/#" class="facebook fb_login"><img src="/img/fb-64.png" height="25" width="25"></a>
													</div>
													<div class="sns-signin" style="margin:10px 10px 10px 0px; float: left;">
														<a href="http://poojyam.in/#" class="google-plus google_login"><img src="/img/gplus-64.png" height="25" width="25"></a>
													</div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>	
                                </div>		
                            </div>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                    
                </div>
            </div>
        </div>
    </section>
<?php include "footer.php"; ?>
