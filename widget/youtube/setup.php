<?php
if(!defined('__AFOX__')) exit();
?>

<h5>사용법 :</h5>
<pre class="border rounded p-3">
&lt;img widget="youtube" vid="아이디"&gt;

* 사용 가능한 옵션 *
  width     : 동영상 넓이
  height    : 동영상 높이
  start     : 시작 시간 (초)
  rel       : 동영상이 완료되면 추천 동영상을 표시합니다.
  controls  : 플레이어 컨트롤을 표시합니다.
  showinfo  : 동영상 제목 및 플레이어 작업을 표시합니다.
</pre>

<h5>사용예: </h5>
<pre class="border rounded p-3">
&lt;img widget="youtube" vid="id" start="45" rel="0"&gt;
</pre>
