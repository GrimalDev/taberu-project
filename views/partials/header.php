<header> <!--TODO implement compote or login from session cookie ton get acces to login page or account page-->
    <img src="../../style/media/TaBeRu-solid-fit.png" alt="logo taberu">
    <nav>
        <div>
            <?php
                function active($current_page){
                    $url_array =  explode('/', $_SERVER['REQUEST_URI']) ;
                    $url = end($url_array);
                    if($current_page == $url){
                        echo 'nav-txt-active'; //class name in css
                    }
                }
                if(isset($_SESSION["loggedin"])){
                    if ($_SESSION["loggedin"]) {
                        echo '<a id="deconnect-nav" href=" /logout" class="underlined">Se deconnecter</a>';
                    }
                }
            ?>
            <a href="/" class="underlined <?php active('');?>">Accueil</a>
            <a href=" /recettes" class="underlined <?php active('recettes');?>">Recettes</a>
            <a href=" /compte" class="underlined <?php active('compte');?>">Compte</a>
            <div id="burger-container">
                <svg id="animated-burger" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 269.988 269.988" style="enable-background:new 0 0 269.988 269.988;" xml:space="preserve">
                    <path d="M64.994,239.913h140c19.299,0,35-15.701,35-35v-14.977h-210v14.977
                            C29.994,224.212,45.695,239.913,64.994,239.913z"/>
                    <path d="M14.76,159.925c0.078-0.001,0.155-0.012,0.234-0.012h240c0.079,0,0.155,0.011,0.234,0.012
                            c8.158-0.124,14.76-6.804,14.76-15.01c0-8.259-6.724-14.979-14.988-14.979H14.987C6.723,129.937,0,136.656,0,144.915
                            C0,153.121,6.602,159.802,14.76,159.925z"/>
                    <path d="M211.067,53.404c-20.11-15.044-47.084-23.329-75.952-23.329c-28.871,0-55.848,8.285-75.959,23.33
                            C41.989,66.246,31.861,82.61,30.223,99.937H240C238.361,82.61,228.234,66.246,211.067,53.404z"/>
                </svg>
                <nav id="burger-nav">
                    <?php
                        if(isset($_SESSION["loggedin"])){
                            if ($_SESSION["loggedin"]) {
                                echo '<a id="deconnect-burger" href=" /logout" class="underlined">Se deconnecter</a>';
                            }
                        }
                    ?>
                    <a href="/" class="underlined <?php active('');?>">Accueil</a>
                    <a href=" /recettes" class="underlined <?php active('recettes');?>">Recettes</a>
                    <a href=" /compte" class="underlined <?php active('compte');?>">Compte</a>
                    <?php
                        if(isset($_SESSION["loggedin"])){
                            if ($_SESSION["loggedin"]) {
                                echo '<a href=" /modifications" class="underlined">modifications</a>';
                            }
                        }
                    ?>
                </nav>
            </div>
        </div>
    </nav>
</header>