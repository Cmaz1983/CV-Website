<?php
    if(!isset($_COOKIE['cookie-consent'])){
?>
    <div id="cookie-consent">
        <div class="container">
            <div class="cookie-consent-text">
                This website uses cookies to enhance your browsing experience. <a href="/privacy.php">Learn more</a>
            </div>
            <div class="btn btn-light float-right" id="cookie-consent-btn">
                Dismiss
            </div>
            <div style="clear:both;"></div>
        </div>
    </div> 
<?php
    };
?>