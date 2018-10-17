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
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/blackberry-icon.png"></span>blackberry</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/htc-icon.png"></span>HTC</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/lg-icon.png"></span>LG</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/appo-icon.png"></span>Apple</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/Motorola_logo.png"></span>Motorola</a></li>
                                        <li><a href="#"><span><img src="<?php echo $imagesDir; ?>/mac-icon.png"></span>MacBook</a></li>
                                    </ul>
                                    <input id="roksearch_search_str" class="form-control" type="text" name="model" value="" placeholder="SEARCH YOUR MODEL HERE">
                                    <button><a href="#"><img src="<?php echo $imagesDir; ?>/search.png" alt=""></a></button>
                                    <input type="hidden" name="make" value="">
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col- col-sm-12 col-md-12 col-lg-5 col-xl-5">
                        <div class="check-btn-sec">
                            <a class="chk-button" href="#" title=""> 
                                <img class="img-responsive" src="<?php echo $imagesDir; ?>/green-traingle.png" alt="">
                                <span class="caption">check out <b>intro video</b></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>