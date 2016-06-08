<?php
if(!defined('__AFOX__')) exit();
?>

<h1>위젯 코드 예제</h1>
<pre>
&lt;img class="afox_widget" widget="content" module="아이디"&gt;

* 사용 가능한 옵션 *
  type      : 목록 타입 (사용가능한 타입 default 또는 gallery)
  count     : 목록 수
  style     : 스타일
</pre>

<h3>예제 1</h3>
<pre>
&lt;img class="afox_widget" widget="content" type="default" module="id" count="5"&gt;
</pre>

<h3>예제 2</h3>
<pre>
&lt;div style="text-align:justify"&gt;
&lt;img class="afox_widget" widget="content" type="default" module="id_1" style="width:48%;display:inline-block"&gt;
&lt;img class="afox_widget" widget="content" type="default" module="id_2" style="width:48%;display:inline-block"&gt;
&lt;div style="width:48%;display:inline-block;height:1px"&gt;&lt;/div&gt;
&lt;/div&gt;
</pre>