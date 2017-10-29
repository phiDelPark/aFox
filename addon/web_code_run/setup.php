<?php
if(!defined('__AFOX__')) exit();
?>
<h3>사용 예제:</h3>
<pre>
&lt;blockquote web-code-run="area"&gt;
&lt;cite&gt;Code Run&lt;/cite&gt;&lt;hr&gt;
&lt;pre&gt;CSS:&lt;code web-code-run="css"&gt; 
/* 이 아래로 코드 입력 */

&lt;/code&gt;&lt;/pre&gt;
&lt;pre&gt;SCRIPT:&lt;code web-code-run="script"&gt; 
/* 이 아래로 코드 입력 */

&lt;/code&gt;&lt;/pre&gt;
&lt;pre&gt;HTML:&lt;code web-code-run="html"&gt; 
&lt;!--/ 이 아래로 코드 입력 /--&gt; 

&lt;/code&gt;&lt;/pre&gt;
&lt;button web-code-run="run"&gt;코드 실행&lt;/button&gt;
&lt;/blockquote&gt;
</pre>
<h3>* 참고 :</h3>
<pre>
1. 입력을 쉽게 하시려면 Web Code Run 에디터 콤포넌트를 사용하세요.
2. Script의 load 이벤트는 작동하지 않습니다, 대신 ready 이벤트를 사용하세요.
</pre>
