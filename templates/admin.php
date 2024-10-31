<style>
    body {
        margin: 0;
    }
    iframe {
        display: block;       /* iframes are inline by default */
        background: #fff;
        border: none;         /* Reset default border */
        min-height: calc(100vh - 32px);      /* Viewport-relative units */
        max-height: 200vh;
        overflow: scroll;
        width: 100%;
    }
    #wpcontent {
        padding: 0;
    }
    #wpfooter {
        display: none;
    }
    #wpbody-content {
        padding: 0;
    }
</style>

<?php
    $user_info = get_userdata(1);
    $email = $user_info->user_email;

    echo "
    <script>
        let ifr = document.createElement('iframe');
        ifr.src = 'https://control.qsearch.ai/woocommerce/auth?s=' + window.location.origin + '&email=' + '{$email}';
        ifr.setAttribute('allowtransparency', 'true');
        ifr.setAttribute('scrolling', 'no');
        ifr.setAttribute('frameborder', '0');
        ifr.setAttribute('allowTransparency', 'true');
        ifr.setAttribute('allow', 'encrypted-media');
        document.getElementById('wpcontent').appendChild(ifr);
    </script>
    ";
?>