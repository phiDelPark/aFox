<?php
if(!defined('__AFOX__')) exit();
?>

<h5>사용법 :</h5>
<pre class="border rounded p-3">
&lt;img widget="content" module="아이디"&gt;

* 사용 가능한 옵션 *
  type          : 목록 타입 (default, gallery)
  count         : 목록 수
  order         : 정렬 필드
  category      : 분류 선택
  title         : 제목
  class         : CSS 클래스
  style         : 스타일
  mstyle        : 모바일 화면에서의 스타일
</pre>

<h5>사용예 1</h5>
<pre class="border rounded p-3">
&lt;img widget="content" module="id_1" type="default" count="5"&gt;
&lt;img widget="content" module="id_2" count="5" class="color-swatch gray"&gt;
</pre>

<h5>사용예 2</h5>
<pre class="border rounded p-3">
&lt;div style="text-align:justify"&gt;
&lt;img widget="content" module="id_1" style="width:48.5%;display:inline-block" mstyle="width:100%"&gt;
&lt;img widget="content" module="id_2" style="width:48.5%;display:inline-block" mstyle="width:100%"&gt;
&lt;div style="width:48.5%;display:inline-block;height:1px"&gt;&lt;/div&gt;
&lt;/div&gt;
</pre>
