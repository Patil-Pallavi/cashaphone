<?php $imagesDir = JURI::base() . 'images/cashaphone'; ?>
<header>
    <section class="header-top">
        <div class="container">
            <div class="row">
                <!-- logo --> 
                <div class="col-2">
                    <a href="home" class="logo" title="Home - Cash A Phone"><img class="img-responsive" src="images/cashaphone/logo.png" alt="Cash A Phone Logo"/></a>
                </div>
                <!-- logo -->
                <div class="col-10">
                    <div class="social-bar">
                        <div class="top_search">
                            <form action="search-result" method="get" class="header_search" >
                                <div class="form-group">
                                    <input id="top_search" class="form-control" type="text" name="model" value="" placeholder="SEARCH YOUR MODEL HERE">
                                    <input type="hidden" name="make" value="">
                                    <input type="hidden" name="Search" value="search">
                                    <button><img src="images/cashaphone/top_search_new.png" alt="Search" ></button>
                                </div>
                            </form>
                        </div>
                        <jdoc:include type="modules" name="header-social-sec" style="none" />
                    </div>
                    <div class="menu_bar">
                        <jdoc:include type="modules" name="header-nav" style="none" />
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
                        <a href="mob_home.html" class="logo" title="Home - Cash A Phone ">
                            <img class="img-responsive" src="images/cashaphone/logo.png" alt="" alt="Cash A Phone Logo"/>
                        </a>
                    </div>
                    <div class="col-4 col-sm-4 offset-2 offset-sm-2">
                        <div class="login_menu_sec">
                            <a href="JavaScript:void(0);" class="res_menu" onclick="openNav()">
                                <img class="img-responsive" src="images/cashaphone/Menu_new.png" alt="Menu"/>
                            </a>
                            <a href="my-account" class="res_login" title="My Account">
                                <img class="img-responsive" src="images/cashaphone/Login_new.png" alt="My Account"/>
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
                            <a href="how-does-it-work" title="How does it Works?">
                                <span><img src="images/cashaphone/how_does_icon.png" alt="How does it Works?"></span> How does it Works?
                            </a>
                        </li>
                        <li>
                            <a href="why-us" title="Why Us?">
                                <span><img src="images/cashaphone/why_icon.png" alt="Why Us?"></span> Why Us?
                            </a>
                        </li>
                        <li>
                            <a href="identification" title="Identification">
                                <span><img src="images/cashaphone/identification_icon.png" alt="Identification"></span> Identification
                            </a>
                        </li>
                        <li>
                            <a href="faq" title="FAQ">
                                <span><img src="images/cashaphone/faq_icon.png" alt="FAQ"></span> F.A.Q.
                            </a>
                        </li>
                        <li>
                            <a href="posting-instruction" title="Posting instructions">
                                <span><img src="images/cashaphone/posting_icon.png" alt="Posting instructions"></span> Posting instructions 
                            </a>
                        </li>
                        <li>
                            <a href="bitcoin" title="Bitcoin">
                                <span><img src="images/cashaphone/bitcoin_icon.png" alt="Bitcoin"></span> Bitcoin 
                            </a>
                        </li>
                        <li>
                            <a href="bulk-buy-back" title="Bulk Buy Back">
                                <span><img src="images/cashaphone/bulk_buy_back_icon.png" alt="Bulk Buy Back"></span> Bulk Buy Back
                            </a>
                        </li>
                        <li>
                            <a href="about-us" title="About Us">
                                <span><img src="images/cashaphone/about_icon.png" alt="About Us"></span> About Us
                            </a>
                        </li>
                        <li>
                            <a href="https://plus.google.com/u/0/b/106314295692683592684/106314295692683592684" title="NewsBlogs">
                                <span><img src="images/cashaphone/news_icon.png" alt="NewsBlogs"></span> NewsBlogs
                            </a>
                        </li>
                        <li>
                            <a href="terms-conditions" title="Terms & Conditions">
                                <span><img src="images/cashaphone/tc_icon.png" alt="Ts & Cs"></span> Ts & Cs
                            </a>
                        </li>
                        <li>
                            <a href="contact-us" title="Contact Us">
                                <span><img src="images/cashaphone/contact_icon.png" alt="Contact Us"></span> Contact Us
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
                                        <li><a href="#" title="Alcatel"><span><img src="<?php echo $imagesDir; ?>/alcatel-icon.png" alt="Alcatel"></span>Alcatel</a></li>
                                        <li><a href="#" title="iPhone"><span><img src="<?php echo $imagesDir; ?>/iphone-icon.png" alt="iPhone"></span>iPhone</a></li>
                                        <li><a href="#" title="iPad"><span><img src="<?php echo $imagesDir; ?>/ipad-icon.png" alt="iPad"></span>iPad</a></li>
                                        <li><a href="#" title="iPod"><span><img src="<?php echo $imagesDir; ?>/iphod-icon.png" alt="iPod"></span>iPod</a></li>
                                        <li><a href="#" title="Apple Watch"><span><img src="<?php echo $imagesDir; ?>/apple-watch.png" alt="Apple Watch"></span>Watch</a></li>
                                        <li><a href="#" title="MacBook"><span><img src="<?php echo $imagesDir; ?>/mac-icon.png" alt="MacBook"></span>MacBook</a></li>
                                        <li><a href="#" title="Blackberry"><span><img src="<?php echo $imagesDir; ?>/blackberry-icon.png" alt="Blackberry"></span>Blackberry</a></li>
                                        <li><a href="#" title="GPixel"><span><img src="<?php echo $imagesDir; ?>/GOOGLE-PIXEL.png" alt="GPixel"></span>GPixel</a></li>
                                        <li><a href="#" title="HTC"><span><img src="<?php echo $imagesDir; ?>/htc-icon.png" alt="HTC"></span>HTC</a></li>
                                        <li><a href="#" title="Huawei"><span><img src="<?php echo $imagesDir; ?>/huwai-icon.png" alt="Huawei"></span>Huawei</a></li>
                                        <li><a href="#" title="LG"><span><img src="<?php echo $imagesDir; ?>/lg-icon.png" alt="LG"></span>LG</a></li>
                                        <li><a href="#" title="Motorola"><span><img src="<?php echo $imagesDir; ?>/Motorola_logo.png" alt="Motorola"></span>Motorola</a></li>
                                        <li><a href="#" title="Nokia"><span><img src="<?php echo $imagesDir; ?>/nokia-icon.png" alt="Nokia"></span>Nokia</a></li>
                                        <li><a href="#" title="Oppo"><span><img src="<?php echo $imagesDir; ?>/Oppo-logo.png" alt="Oppo"></span>Oppo</a></li>
                                        <li><a href="#" title="Samsung"><span><img src="<?php echo $imagesDir; ?>/Samsung-logo.png" alt="Samsung"></span>Samsung</a></li>
                                        <li><a href="#" title="Sony"><span><img src="<?php echo $imagesDir; ?>/sony-icon.png" alt="Sony"></span>Sony</a></li>
                                        <li><a href="#" title="Telstra"><span><img src="<?php echo $imagesDir; ?>/Telstra.png" alt="Telstra"></span>Telstra</a></li>
                                        <li><a href="#" title="Xiaomi"><span><img src="<?php echo $imagesDir; ?>/Xiaomi.png" alt="Xiaomi"></span>Xiaomi</a></li>
                                        <li><a href="#" title="ZTE"><span><img src="<?php echo $imagesDir; ?>/ZTE_logo.png" alt="ZTE"></span>ZTE</a></li>
                                        <li><a href="#" title="Tablets"><span><img src="<?php echo $imagesDir; ?>/Tablets.png" alt="Tablets"></span>Tablets</a></li>
                                        <li><a href="#" title="Data Dongles"><span><img src="<?php echo $imagesDir; ?>/data-dongles.png" alt="Data Dongles"></span>Data Dongles</a></li>
                                    </ul>
                                    <input id="roksearch_search_str" class="form-control" type="text" name="model" value="" placeholder="SEARCH YOUR MODEL HERE">
                                    <button><a href="#" class="mob_srch_icon"><img src="images/cashaphone/search.png" alt=""></a></button>
                                    <input type="hidden" name="make" value="">
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <jdoc:include type="modules" name="banner" style="none" />
</header>