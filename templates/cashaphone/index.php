<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.cashaphone
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
/** @var JDocumentHtml $this */
$app = JFactory::getApplication();
$user = JFactory::getUser();
// Output as HTML5
$this->setHtml5(true);
// Getting params from template
$params = $app->getTemplate(true)->params;
$templateDir = JURI::base() . 'templates/' . $app->getTemplate();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="<?php echo $templateDir; ?>/css/bootstrap.min.css">
        <link href="<?php echo $templateDir; ?>/css/custom.css" rel="stylesheet">
        <link href="<?php echo $templateDir; ?>/css/responsive.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">
        <link rel="shortcut icon" type="image/png" href="<?php echo $templateDir; ?>/images/favicon.png"/>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    	<jdoc:include type="head" />
	</head>
	<body>	
    	<header>
        	<section class="header-top">
            	<div class="container">
                	<div class="row">
                    	<!-- logo --> 
                    	<div class="col-2">
                        	<a href="home" class="logo"><img class="img-responsive" src="images/cashaphone/logo.png" alt=""/></a>
                    	</div>
                    	<!-- logo -->
                    	<div class="col-10">
                        	<div class="social-bar">
	                            <div class="top_search">
	                                <form class="header_search">
	                                    <div class="form-group">
	                                        <input id="top_search" class="form-control" type="text" name="model" value="" placeholder="SEARCH YOUR MODEL HERE">
	                                        <button><img src="images/cashaphone/top_search_new.png" alt="" ></button>
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
        	<jdoc:include type="modules" name="banner" style="none" />
    	</header>	
	    <?php
	    $menu = $app->getMenu(); // Load the JMenuSite Object
	    $active = $menu->getActive(); // Load the Active Menu Item as an stdClass Object
	    $jinput = JFactory::getApplication()->input;
	    $articleId = JRequest::getVar('id');

	    if ($jinput->get('option') == 'com_wrapper') {
	        $currentMenuId = JSite::getMenu()->getActive()->id;
	        $menuitem = $app->getMenu()->getItem($currentMenuId);
	        $manufacturer = $menuitem->params['menu-meta_keywords'];
	    }
	    ?>

    <?php if($articleId == '14'){ ?>
        <jdoc:include type="component" />    
    <?php } else if (isset($manufacturer) && $manufacturer == 'manufacturer') { ?>
        <section class="gadget-area">		
            <jdoc:include type="modules" name="fullwidth" style="none" />

            <div class="container">
                <div class="row">
                    <?php if ($this->countModules('left')) { ?>
                        <jdoc:include type="modules" name="left" style="none" />
                    <?php } ?>
                    <div class="col-12 col-sm-12 col-md-9 col-lg-9 col-xl-8"
                         <jdoc:include type="component" />
                    </div>
                    <?php if ($this->countModules('right')) { ?>
                        <jdoc:include type="modules" name="right" style="none" />
                    <?php } ?>
                </div>
            </div>

        </section>
    <?php } else if (isset($articleId) && $articleId != '') { ?>
        <section class="<?php echo $active->alias; ?>-sec">		
            <jdoc:include type="modules" name="fullwidth" style="none" />			
            <div class="container">
                <div class="<?php echo $active->alias; ?>-cont-area">
                    <?php if ($articleId == 8 || $articleId == 9) { ?>
                     <div class="row">
                       	<?php } elseif (!($this->countModules('left')) && !($this->countModules('left'))) { ?>
                    <div class="row justify-content-center">
                        <?php } else { ?>
	                <div class="row">
		            <?php } ?>					
                        <jdoc:include type="modules" name="left" style="none" />
                        <jdoc:include type="component" />
                        <jdoc:include type="modules" name="middle" style="none" />
                        <jdoc:include type="modules" name="right" style="none" />
                    </div>
                </div>
            </div>		
        </section>
    <?php } else { ?>
        <section class="<?php echo $active->alias; ?>-sec">		
            <jdoc:include type="modules" name="fullwidth" style="none" />
            <?php if (($this->countModules('left') || $this->countModules('middle') || $this->countModules('right')) && !($jinput->get('option'))) { ?>
	            <div class="container">
	                <div class="row">
	                    <jdoc:include type="modules" name="left" style="none" />
	                    <jdoc:include type="modules" name="middle" style="none" />
	                    <jdoc:include type="modules" name="right" style="none" />
	                </div>
	            </div>
          	<?php } ?>
        </section>
    <?php } ?>
    <?php //echo "<pre>"; print_r($articleId); echo "</pre>";?>
	    <footer>
	        <div class="container">
	            <div class="footer-top">
	                <div class="row">
	                    <div class="col- col-sm-12 col-md-6 col-lg-6 col-xl-6">
	                        <div class="footer-left-nav">
	                            <div class="row">
	                                <div class="col- col-sm-12 col-md-12 col-lg-5 col-xl-5 offset-xl-1 offset-lg-1">
	                                    <h2>Navigation</h2>
	                                    <jdoc:include type="modules" name="footer-1"/>
	                                </div>
	                                <div class="col- col-sm-12 col-md-12 col-lg-5 col-xl-5 offset-xl-1 offset-lg-1">
	                                    <h2>Information</h2>
	                                    <jdoc:include type="modules" name="footer-2"/>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="col- col-sm-12 col-md-6 col-lg-6 col-xl-6 align-self-center">
	                        <div class="fotter-social">
	                            <div class="row">
	                                <div class="col- col-sm-12 col-md-12 col-lg-5 col-xl-5 align-self-center">
	                                    <h2>Follow Us With</h2>
	                                </div>
	                                <div class="col- col-sm-12 col-md-12 col-lg-5 col-xl-5 align-self-center">
	                                    <jdoc:include type="modules" name="footer-3"/>
	                                    <a href="#" class="news-blog-btn">NEWS BLOG</a>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="footer-bottom">
	                <div class="row">
	                    <div class="col-12">
	                        <jdoc:include type="modules" name="copyright" style="xhtml" />		
	                    </div>
	                </div>
	            </div>
	        </div>
	    </footer>
    </body>
</html>
<script src="<?php echo $templateDir; ?>/js/jquery.min.js"></script>
<script src="<?php echo $templateDir; ?>/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="<?php echo $templateDir; ?>/js/custom.js"></script>