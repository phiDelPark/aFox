<?php
if(!defined('__AFOX__')) exit();
?>

<h2>위젯 코드 예제</h2>
<pre>
&lt;img widget="youtube" vid="아이디"&gt;

* 사용 가능한 옵션 *
  width     : 동영상 넓이
  height    : 동영상 높이
  start     : 시작 시간 (초)
  rel       : 동영상이 완료되면 추천 동영상을 표시합니다.
  controls  : 플레이어 컨트롤을 표시합니다.
  showinfo  : 동영상 제목 및 플레이어 작업을 표시합니다.
</pre>

<h3>예제 1</h3>
<pre>
&lt;img widget="youtube" vid="id" start="45" rel="0"&gt;
</pre>
