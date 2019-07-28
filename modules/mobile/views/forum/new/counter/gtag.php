<?php
?>
<!--
Не используется в Компонентах (но это не точно)
<script>
  window.GTAGS=<?=ForumInfoForCounter::getHashByGA()?>;
  window.GTAGS.optimize_id = 'GTM-TS6VZW9';
</script>
-->
<script>
  if(!window._womanData) {window._womanData = {};}
  window._womanData.GTAGS=<?=ForumInfoForCounter::getHashByGA()?>;
  window._womanData.GTAGS.optimize_id = 'GTM-TS6VZW9';
</script>

<script async src="https://www.googletagmanager.com/gtag/js?id=UA-3365753-20"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-3365753-20', window._womanData.GTAGS || {});
</script>
