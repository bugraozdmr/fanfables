<?php $footer_path="/anime/" ?>
<footer class="footer">
    <div class="page-up">
        <a href="#" id="scrollToTopButton"><span class="arrow_carrot-up"></span></a>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="footer__logo">
                    <a href="<?php echo $footer_path ?>index.php"><img width="98" height="23" src="<?php echo $footer_path ?>/img/logo.png" alt=""></a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="footer__nav">
                    <ul>
                        <li class="active"><a href="<?php echo $footer_path ?>index.php">Homepage</a></li>
                        <li><a href="./all-categories.php">Categories</a></li>
                        <li><a href="./blogs.php">Blog</a></li>
                        <li><a href="<?php echo $footer_path ?>watch-later.php">Contacts</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3">
                <p>
                    Copyright &copy;<?php echo date("Y"); ?> All rights reserved | This site is made with <i class="fa fa-heart" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</footer>