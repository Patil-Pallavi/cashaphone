<?php
// No direct access
defined('_JEXEC') or die;
$imagesDir = JURI::base() . 'images/cashaphone/';
?>
<section class="header-top">
    <div class="container">
        <div class="row">
            <!-- logo --> 
            <div class="col-2">
                <a href="home.html" class="logo"><img class="img-responsive" src="<?php echo $imagesDir; ?>logo.png" alt=""/></a>
            </div>
            <!-- logo -->
            <div class="col-10">
                <div class="social-bar">
                    <div class="top_search">
                        <form class="header_search">
                            <div class="form-group">
                                <input id="top_search" class="form-control" type="text" name="model" value="" placeholder="SEARCH YOUR MODEL HERE">
                                <button><img src="<?php echo $imagesDir; ?>top_search_new.png" alt="" ></button>
                            </div>
                        </form>
                    </div>
                    <ul class="social-sec">
                        <li><a href="https://www.facebook.com/Cashaphone"><img class="" src="<?php echo $imagesDir; ?>face.png" alt=""/></a></li>
                        <li><a href="https://twitter.com/cashaphone"><img class="" src="<?php echo $imagesDir; ?>twitter.png" alt=""/></a></li>
                        <li><a href="https://www.youtube.com/user/cashAphone"><img class="" src="<?php echo $imagesDir; ?>youtube.png" alt=""/></a></li>
                        <li><a href="#"><img class="" src="<?php echo $imagesDir; ?>msg.png" alt=""/></a></li>
                    </ul>
                </div>
                <div class="menu_bar">
                    <ul class="main_menu">
                        <li class="nav-item">
                            <a class="nav-link" href="how_does_it_work.html">How does it work?</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="why_us.html">Why Us?</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="faq.html">FAQ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="my_account.html">My Account</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact_us.html">Contact us</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="mob_header">
    <div class="container">
        <div class="mobile_headaer">
            <div class="row">
                <div class="col-6 col-sm-6">
                    <a href="mob_home.html" class="logo">
                        <img class="img-responsive" src="<?php echo $imagesDir; ?>logo.png" alt=""/>
                    </a>
                </div>
                <div class="col-4 col-sm-4 offset-2 offset-sm-2">
                    <div class="login_menu_sec">
                        <a href="JavaScript:void(0);" class="res_menu" onclick="openNav()">
                            <img class="img-responsive" src="<?php echo $imagesDir; ?>Menu_new.png" alt=""/>
                        </a>
                        <a href="my_account.html" class="res_login">
                            <img class="img-responsive" src="<?php echo $imagesDir; ?>Login_new.png" alt=""/>
                        </a>
                    </div>
                </div>
            </div>
            <div id="mySidenav" class="menu_dropdown ">
                <ul>
                    <li>
                        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">
                            &times;
                        </a>
                    </li>
                    <li>
                        <a href="how_does_it_work.html">
                            <span><img src="<?php echo $imagesDir; ?>how_does_icon.png"></span> How does it Works?
                        </a>
                    </li>
                    <li>
                        <a href="why_us.html">
                            <span><img src="<?php echo $imagesDir; ?>why_icon.png"></span> Why Us?
                        </a>
                    </li>
                    <li>
                        <a href="identification.html">
                            <span><img src="<?php echo $imagesDir; ?>identification_icon.png"></span> Identification
                        </a>
                    </li>
                    <li>
                        <a href="faq.html">
                            <span><img src="<?php echo $imagesDir; ?>faq_icon.png"></span> F.A.Q.
                        </a>
                    </li>
                    <li>
                        <a href="posting_instruction.html">
                            <span><img src="<?php echo $imagesDir; ?>posting_icon.png"></span> Posting instructions 
                        </a>
                    </li>
                    <li>
                        <a href="bitcoin.html">
                            <span><img src="<?php echo $imagesDir; ?>bitcoin_icon.png"></span> Bitcoin 
                        </a>
                    </li>
                    <li>
                        <a href="bulkbuy_form.html">
                            <span><img src="<?php echo $imagesDir; ?>bulk_buy_back_icon.png"></span> Bulk Buy Back
                        </a>
                    </li>
                    <li>
                        <a href="about_us.html">
                            <span><img src="<?php echo $imagesDir; ?>about_icon.png"></span> About Us
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <span><img src="<?php echo $imagesDir; ?>news_icon.png"></span> NewsBlogs
                        </a>
                    </li>
                    <li>
                        <a href="terms_condition.html">
                            <span><img src="<?php echo $imagesDir; ?>tc_icon.png"></span> Ts & Cs
                        </a>
                    </li>
                    <li>
                        <a href="contact_us.html">
                            <span><img src="<?php echo $imagesDir; ?>contact_icon.png"></span> Contact Us
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mob_search">
            <div class="row">
                <div class="col-12 col-sm-12">
                    <div class="searchbox">
                        <form action="search-result" name="valueMobileForm" method="get" class="form-group">

                            <div class="form-group">
                                <button id="dropdown-button" type="button" class="btn btn-primary" data-toggle="dropdown">
                                    <div id="menuIcon"></div>
                                    <div id="caretIcon"></div>
                                </button>
                                <ul id="demolist" class="dropdown-menu" role="menu">
                                    <li><a href="#"><span><img src="<?php echo $imagesDir; ?>blackberry-icon.png"></span>blackberry</a></li>
                                    <li><a href="#"><span><img src="<?php echo $imagesDir; ?>htc-icon.png"></span>HTC</a></li>
                                    <li><a href="#"><span><img src="<?php echo $imagesDir; ?>lg-icon.png"></span>LG</a></li>
                                    <li><a href="#"><span><img src="<?php echo $imagesDir; ?>appo-icon.png"></span>Apple</a></li>
                                    <li><a href="#"><span><img src="<?php echo $imagesDir; ?>Motorola_logo.png"></span>Motorola</a></li>
                                    <li><a href="#"><span><img src="<?php echo $imagesDir; ?>mac-icon.png"></span>MacBook</a></li>
                                </ul>
                                <input id="roksearch_search_str" class="form-control" type="text" name="model" value="" placeholder="SEARCH YOUR MODEL HERE">
                                <button><a href="#" class="mob_srch_icon"><img src="<?php echo $imagesDir; ?>search.png" alt=""></a></button>
                                <input type="hidden" name="make" value="">
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>