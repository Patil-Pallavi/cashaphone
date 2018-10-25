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
                            <form action="search-result" method="get" class="header_search" >
                                <div class="form-group">
                                    <input id="top_search" class="form-control" type="text" name="model" value="" placeholder="SEARCH YOUR MODEL HERE">
                                    <input type="hidden" name="make" value="">
                                    <input type="hidden" name="Search" value="search">
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