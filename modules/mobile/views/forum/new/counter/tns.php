<script type="text/javascript">
    (function (win, doc, cb) {
        (win[cb] = win[cb] || []).push(function () {
            try {
                tnsCounterTimeout_ru = new TNS.TnsCounter({
                    'account': 'timeout_ru',
                    'tmsec': 'woman_total'
                });
            } catch (e) {
            }
        });

        var tnsscript = doc.createElement('script');
        tnsscript.type = 'text/javascript';
        tnsscript.async = true;
        tnsscript.src = ('https:' == doc.location.protocol ? 'https:' : 'http:') +
            '//www.tns-counter.ru/tcounter.js';
        var s = doc.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(tnsscript, s);
    })(window, this.document, 'tnscounter_callback');
</script>
<noscript>
    <img src="//www.tns-counter.ru/V13a****timeout_ru/ru/UTF-8/tmsec=woman_total/" width="0" height="0" alt=""/>
</noscript>