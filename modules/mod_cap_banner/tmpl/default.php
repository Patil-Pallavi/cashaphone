<?php
// No direct access
defined('_JEXEC') or die;
$imagesDir = JURI::base() . 'images/cashaphone';
?>
<section class="banner">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-9">
                <div class="row justify-content-center">
                    <div class="col- col-sm-12 col-md-12 col-lg-9 col-xl-9"> 
                        <div class="searchbox">
                            <form action="search-result" name="valueMobileForm" method="get" class="form-group">
                                <!-- <div class="form-group">
                                    <input id="roksearch_search_str" class="form-control" type="text" name="model" value="" placeholder="SEARCH YOUR MODEL HERE">
                                     <button><a href="#"><img src="images/search.png" alt=""></a></button>
                                    <input type="hidden" name="make" value="">
                                </div> -->
                                <div class="form-group">
                                    <button id="dropdown-button" type="button" class="btn btn-primary" data-toggle="dropdown">
                                        <div id="menuIcon"></div>
                                        <div id="caretIcon"></div>
                                    </button>
                                    <ul id="demolist" class="dropdown-menu" role="menu">
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/alcatel-icon.png"></span>Alcatel</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/iphone-icon.png"></span>iPhone</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/ipad-icon.png"></span>iPad</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/iphod-icon.png"></span>iPod</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/apple-watch.png"></span>Watch</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/mac-icon.png"></span>MacBook</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/blackberry-icon.png"></span>Blackberry</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/GOOGLE-PIXEL.png"></span>GPixel</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/htc-icon.png"></span>HTC</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/huwai-icon.png"></span>Huawei</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/lg-icon.png"></span>LG</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/Motorola_logo.png"></span>Motorola</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/nokia-icon.png"></span>Nokia</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/Oppo-logo.png"></span>Oppo</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/Samsung-logo.png"></span>Samsung</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/sony-icon.png"></span>Sony</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/Telstra.png"></span>Telstra</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/Xiaomi.png"></span>Xiaomi</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/ZTE_logo.png"></span>ZTE</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/Tablets.png"></span>Tablets</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/data-dongles.png"></span>Data Dongles</a></li>
                                    </ul>
                                    <input id="roksearch_search_str" class="form-control" type="text" name="model" value="" placeholder="SEARCH YOUR MODEL HERE">
                                    <button><a href="#"><img src="<?php echo $imagesDir; ?>/search.png" alt=""></a></button>
                                    <input type="hidden" name="make" value="">
                                    <input type="hidden" name="Search" value="search">
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                   <div class="col- col-sm-12 col-md-7 col-lg-5 col-xl-5">
                    <div class="check-btn-sec">
                        <a class="chk-button html5lightbox" href="#mydiv" title=""> 
                            <img class="img-responsive" src="images/cashaphone/green-traingle.png" alt="">
                            <span class="caption">check out <b>intro video</b></span>                   
                        </a>
                    </div>
                    <div id="mydiv" style="display:none;">
                       <iframe width="560" height="315" src="https://www.youtube.com/embed/9XdcTyaSorU?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
</section>