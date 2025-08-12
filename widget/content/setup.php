<?php
if(!defined('__AFOX__')) exit();
?>

<h5>사용법 :</h5>
<pre class="border rounded p-3">
&lt;widget module="모듈(ID)"&gt;content&lt;/widget&gt;

* 사용 가능한 옵션 *
  type          : 목록 타입 (default, gallery)
  count         : 목록 수
  order         : 정렬 필드
  category      : 분류 선택
  title         : 제목
  class         : CSS 클래스
  style         : 스타일
  mstyle        : 모바일 화면에서의 스타일

* 주의사항 *
  타입이 gallery 이면 해당 모듈에 썸네일 설정을 해주세요.
</pre>

<h5>사용예</h5>
<pre class="border rounded p-3">
&lt;widget module="id_1" count="5" class="col-4"&gt;content&lt;/widget&gt;
&lt;widget module="id_2" type="gallery" count="5"&gt;content&lt;/widget&gt;
</pre>
