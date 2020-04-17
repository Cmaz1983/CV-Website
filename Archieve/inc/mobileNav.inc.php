<div id="mobile-navigation">
    <?php
         if(blog::checkLogin()){
    ?>      
            <div class="user">
                <div class="username">
                    <?= blog::getUsername(); ?>
                </div>
                <div class="links">
                    <ul>
                        <li>
                            <a href="/user.php">Client area</a>
                        </li>
                    
                    </ul>
                </div>
            </div>
    <?php
        }else{
    ?>
        <div class="logout pl-2 pr-2 mt-5">
            <button type="button" class="btn btn-primary w-100" data-toggle="modal" data-target="#loginModal">
                Client Login
            </button>
        </div>
    <?php
        }
    ?>
    
    <?php
        include('nav.inc.php');

        if(blog::checkLogin()){
            ?>
            <div class="logout pl-2 pr-2 mt-5">
                <button class="btn btn-danger w-100" onclick="logout()">Logout</button>
            </div>
            <?php
        };
    ?>
    
</div>