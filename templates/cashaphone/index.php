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
        <!-- <script src="<?php echo $templateDir; ?>/js/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>         -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <jdoc:include type="head" />
</head>
<body>    	
    <?php
    $menu = $app->getMenu(); // Load the JMenuSite Object
    $active = $menu->getActive(); // Load the Active Menu Item as an stdClass Object
    $jinput = JFactory::getApplication()->input;
    $articleId = JRequest::getVar('id');

    if ($jinput->get('option') == 'com_wrapper' || $jinput->get('option') == 'com_visforms') {
        $currentMenuId = JSite::getMenu()->getActive()->id;        
        $menuitem = $app->getMenu()->getItem($currentMenuId);
        $meta_key = $menuitem->note;
    }
    ?>
    <?php 
        $useragent=$_SERVER['HTTP_USER_AGENT'];        
        if($active->alias == 'home'){
            if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
                    include_once "mob_home_page_header.php";
                }else{
                    include_once "home_page_header.php";
                }             
        }else{
            include_once "header.php"; 
        }
    ?> 
    <?php if ($articleId == '14') { ?>
    <jdoc:include type="component" />    
<?php } else if (isset($meta_key) && $meta_key == 'manufacturer') { ?>
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
<?php } else if ($jinput->get('option') == 'com_visforms') { ?>
    <?php if(isset($meta_key) && $meta_key == 'bulk buy form'){ ?> 
        <section class="bulk_buy_back">
    <?php } if(isset($meta_key) && $meta_key == 'contact form'){ ?> 
        <section class="contact-us-sec">
    <?php } ?>
        <jdoc:include type="modules" name="fullwidth" style="none" />
        <div class="container">
            <div class="row justify-content-center">
                <?php if ($this->countModules('left')) { ?>
                    <jdoc:include type="modules" name="left" style="none" />
                <?php } ?>
                <div class="col- col-sm-12 col-md-8 col-lg-5 col-xl-5">
                    <?php if(isset($meta_key) && $meta_key == 'bulk buy form'){ ?> 
                        <h1>Bulk Buy Back</h1>
                    <?php } if(isset($meta_key) && $meta_key == 'contact form'){ ?> 
                        <h1>Contact Us</h1>
                        <div class="contact-us-cont-area">
                    <?php } ?>                    
                    <jdoc:include type="component" />
                </div>
                <?php if(isset($meta_key) && $meta_key == 'contact form'){ ?>
                        </div>
                    <div class="help_desk">
                        <h2>We are here to help :</h2>
                        <span>Tel : 1300 771 330</span>
                    </div>
                    <?php } ?>
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
                    <?php if($jinput->get('option') == 'com_wrapper'){ ?>
                        <div class="container">
                            <div class="<?php echo $active->alias; ?>-cont-area">
                                <jdoc:include type="component" />
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
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                <div class="footer-left-nav">
                                    <div class="row">
                                        <div class="col-6 col-sm-6 col-md-6 col-lg-5 col-xl-5 offset-xl-1 offset-lg-1">
                                            <h2>Navigation</h2>
                                            <jdoc:include type="modules" name="footer-1"/>
                                        </div>
                                        <div class="col-6 col-sm-6 col-md-6 col-lg-5 col-xl-5 offset-xl-1 offset-lg-1">
                                            <h2>Information</h2>
                                            <jdoc:include type="modules" name="footer-2"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 align-self-center">
                                <div class="fotter-social">
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-5 col-lg-5 col-xl-5 align-self-center">
                                            <h2>Follow Us With</h2>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-7 col-lg-7 col-xl-5 align-self-center">
                                            <jdoc:include type="modules" name="footer-3"/>
                                            <a href="https://plus.google.com/u/0/b/106314295692683592684/106314295692683592684" class="news-blog-btn" title="NewsBlogs" target="_blank">NEWS BLOG</a>
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
            <script src="<?php echo $templateDir; ?>/js/html5lightbox.js"></script>
            <!-- <script src="<?php echo $templateDir; ?>/js/popper-2.min.js"></script> -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
            <script src="<?php echo $templateDir; ?>/js/custom.js"></script>