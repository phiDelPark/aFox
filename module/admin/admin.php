<?php
if(!defined('__AFOX__')) exit();

$_MENU_ICON = ['default'=>'dashboard', 'theme'=>'home', 'menu'=>'menu-hamburger', 'member'=>'user', 'content'=>'list-alt', 'page'=>'list-alt', 'board'=>'list-alt', 'document'=>'list-alt', 'comment'=>'list-alt', 'file'=>'list-alt', 'trash'=>'trash', 'module'=>'th-large', 'addon'=>'random', 'widget'=>'import', 'setup'=>'cog', 'visit'=>'globe'];
$admin = empty($_DATA['disp']) ? 'default' :  $_DATA['disp'];
$is_admin = isAdmin();
?>

<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="bi-speedometer2" viewBox="0 0 16 16">
  <path d="M8 4a.5.5 0 0 1 .5.5V6a.5.5 0 0 1-1 0V4.5A.5.5 0 0 1 8 4M3.732 5.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707M2 10a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 10m9.5 0a.5.5 0 0 1 .5-.5h1.5a.5.5 0 0 1 0 1H12a.5.5 0 0 1-.5-.5m.754-4.246a.39.39 0 0 0-.527-.02L7.547 9.31a.91.91 0 1 0 1.302 1.258l3.434-4.297a.39.39 0 0 0-.029-.518z"/>
  <path fill-rule="evenodd" d="M0 10a8 8 0 1 1 15.547 2.661c-.442 1.253-1.845 1.602-2.932 1.25C11.309 13.488 9.475 13 8 13c-1.474 0-3.31.488-4.615.911-1.087.352-2.49.003-2.932-1.25A8 8 0 0 1 0 10m8-7a7 7 0 0 0-6.603 9.329c.203.575.923.876 1.68.63C4.397 12.533 6.358 12 8 12s3.604.532 4.923.96c.757.245 1.477-.056 1.68-.631A7 7 0 0 0 8 3"/>
  </symbol>
  <symbol id="bi-house-door" viewBox="0 0 16 16">
  <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4z"/>
  </symbol>
  <symbol id="bi-menu-button" viewBox="0 0 16 16">
  <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0h8A1.5 1.5 0 0 1 11 1.5v2A1.5 1.5 0 0 1 9.5 5h-8A1.5 1.5 0 0 1 0 3.5zM1.5 1a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 0-.5-.5z"/>
  <path d="m7.823 2.823-.396-.396A.25.25 0 0 1 7.604 2h.792a.25.25 0 0 1 .177.427l-.396.396a.25.25 0 0 1-.354 0M0 8a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm1 3v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2zm14-1V8a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v2zM2 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0 4a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5"/>
  </symbol>
  <symbol id="bi-box" viewBox="0 0 16 16">
  <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z"/>
  </symbol>
  <symbol id="bi-chevron-down" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
  </symbol>
  <symbol id="bi-grid" viewBox="0 0 16 16">
  <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5z"/>
  </symbol>
  <symbol id="bi-plugin" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1 8a7 7 0 1 1 2.898 5.673c-.167-.121-.216-.406-.002-.62l1.8-1.8a3.5 3.5 0 0 0 4.572-.328l1.414-1.415a.5.5 0 0 0 0-.707l-.707-.707 1.559-1.563a.5.5 0 1 0-.708-.706l-1.559 1.562-1.414-1.414 1.56-1.562a.5.5 0 1 0-.707-.706l-1.56 1.56-.707-.706a.5.5 0 0 0-.707 0L5.318 5.975a3.5 3.5 0 0 0-.328 4.571l-1.8 1.8c-.58.58-.62 1.6.121 2.137A8 8 0 1 0 0 8a.5.5 0 0 0 1 0"/>
  </symbol>
  <symbol id="bi-puzzle" viewBox="0 0 16 16">
  <path d="M3.112 3.645A1.5 1.5 0 0 1 4.605 2H7a.5.5 0 0 1 .5.5v.382c0 .696-.497 1.182-.872 1.469a.5.5 0 0 0-.115.118l-.012.025L6.5 4.5v.003l.003.01q.005.015.036.053a.9.9 0 0 0 .27.194C7.09 4.9 7.51 5 8 5c.492 0 .912-.1 1.19-.24a.9.9 0 0 0 .271-.194.2.2 0 0 0 .039-.063v-.009l-.012-.025a.5.5 0 0 0-.115-.118c-.375-.287-.872-.773-.872-1.469V2.5A.5.5 0 0 1 9 2h2.395a1.5 1.5 0 0 1 1.493 1.645L12.645 6.5h.237c.195 0 .42-.147.675-.48.21-.274.528-.52.943-.52.568 0 .947.447 1.154.862C15.877 6.807 16 7.387 16 8s-.123 1.193-.346 1.638c-.207.415-.586.862-1.154.862-.415 0-.733-.246-.943-.52-.255-.333-.48-.48-.675-.48h-.237l.243 2.855A1.5 1.5 0 0 1 11.395 14H9a.5.5 0 0 1-.5-.5v-.382c0-.696.497-1.182.872-1.469a.5.5 0 0 0 .115-.118l.012-.025.001-.006v-.003a.2.2 0 0 0-.039-.064.9.9 0 0 0-.27-.193C8.91 11.1 8.49 11 8 11s-.912.1-1.19.24a.9.9 0 0 0-.271.194.2.2 0 0 0-.039.063v.003l.001.006.012.025c.016.027.05.068.115.118.375.287.872.773.872 1.469v.382a.5.5 0 0 1-.5.5H4.605a1.5 1.5 0 0 1-1.493-1.645L3.356 9.5h-.238c-.195 0-.42.147-.675.48-.21.274-.528.52-.943.52-.568 0-.947-.447-1.154-.862C.123 9.193 0 8.613 0 8s.123-1.193.346-1.638C.553 5.947.932 5.5 1.5 5.5c.415 0 .733.246.943.52.255.333.48.48.675.48h.238zM4.605 3a.5.5 0 0 0-.498.55l.001.007.29 3.4A.5.5 0 0 1 3.9 7.5h-.782c-.696 0-1.182-.497-1.469-.872a.5.5 0 0 0-.118-.115l-.025-.012L1.5 6.5h-.003a.2.2 0 0 0-.064.039.9.9 0 0 0-.193.27C1.1 7.09 1 7.51 1 8s.1.912.24 1.19c.07.14.14.225.194.271a.2.2 0 0 0 .063.039H1.5l.006-.001.025-.012a.5.5 0 0 0 .118-.115c.287-.375.773-.872 1.469-.872H3.9a.5.5 0 0 1 .498.542l-.29 3.408a.5.5 0 0 0 .497.55h1.878c-.048-.166-.195-.352-.463-.557-.274-.21-.52-.528-.52-.943 0-.568.447-.947.862-1.154C6.807 10.123 7.387 10 8 10s1.193.123 1.638.346c.415.207.862.586.862 1.154 0 .415-.246.733-.52.943-.268.205-.415.39-.463.557h1.878a.5.5 0 0 0 .498-.55l-.001-.007-.29-3.4A.5.5 0 0 1 12.1 8.5h.782c.696 0 1.182.497 1.469.872.05.065.091.099.118.115l.025.012.006.001h.003a.2.2 0 0 0 .064-.039.9.9 0 0 0 .193-.27c.14-.28.24-.7.24-1.191s-.1-.912-.24-1.19a.9.9 0 0 0-.194-.271.2.2 0 0 0-.063-.039H14.5l-.006.001-.025.012a.5.5 0 0 0-.118.115c-.287.375-.773.872-1.469.872H12.1a.5.5 0 0 1-.498-.543l.29-3.407a.5.5 0 0 0-.497-.55H9.517c.048.166.195.352.463.557.274.21.52.528.52.943 0 .568-.447.947-.862 1.154C9.193 5.877 8.613 6 8 6s-1.193-.123-1.638-.346C5.947 5.447 5.5 5.068 5.5 4.5c0-.415.246-.733.52-.943.268-.205.415-.39.463-.557z"/>
  </symbol>
  <symbol id="bi-person" viewBox="0 0 16 16">
  <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
  </symbol>
  <symbol id="bi-airplane" viewBox="0 0 16 16">
  <path d="M6.428 1.151C6.708.591 7.213 0 8 0s1.292.592 1.572 1.151C9.861 1.73 10 2.431 10 3v3.691l5.17 2.585a1.5 1.5 0 0 1 .83 1.342V12a.5.5 0 0 1-.582.493l-5.507-.918-.375 2.253 1.318 1.318A.5.5 0 0 1 10.5 16h-5a.5.5 0 0 1-.354-.854l1.319-1.318-.376-2.253-5.507.918A.5.5 0 0 1 0 12v-1.382a1.5 1.5 0 0 1 .83-1.342L6 6.691V3c0-.568.14-1.271.428-1.849m.894.448C7.111 2.02 7 2.569 7 3v4a.5.5 0 0 1-.276.447l-5.448 2.724a.5.5 0 0 0-.276.447v.792l5.418-.903a.5.5 0 0 1 .575.41l.5 3a.5.5 0 0 1-.14.437L6.708 15h2.586l-.647-.646a.5.5 0 0 1-.14-.436l.5-3a.5.5 0 0 1 .576-.411L15 11.41v-.792a.5.5 0 0 0-.276-.447L9.276 7.447A.5.5 0 0 1 9 7V3c0-.432-.11-.979-.322-1.401C8.458 1.159 8.213 1 8 1s-.458.158-.678.599"/>
  </symbol>
  <symbol id="bi-gear" viewBox="0 0 16 16">
  <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
  <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
  </symbol>
  <symbol id="bi-book" viewBox="0 0 16 16">
  <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/>
  </symbol>
  <symbol id="bi-collection" viewBox="0 0 16 16">
  <path d="M2.5 3.5a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1zm2-2a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1zM0 13a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 16 13V6a1.5 1.5 0 0 0-1.5-1.5h-13A1.5 1.5 0 0 0 0 6zm1.5.5A.5.5 0 0 1 1 13V6a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5z"/>
  </symbol>
  <symbol id="bi-chat-right-text-fill" viewBox="0 0 16 16">
  <path d="M16 2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h9.586a1 1 0 0 1 .707.293l2.853 2.853a.5.5 0 0 0 .854-.353zM3.5 3h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1 0-1m0 2.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1 0-1m0 2.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1"/>
  </symbol>
  <symbol id="bi-chat-right-quote-fill" viewBox="0 0 16 16">
  <path d="M16 2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h9.586a1 1 0 0 1 .707.293l2.853 2.853a.5.5 0 0 0 .854-.353zM7.194 4.766q.13.188.227.401c.428.948.393 2.377-.942 3.706a.446.446 0 0 1-.612.01.405.405 0 0 1-.011-.59c.419-.416.672-.831.809-1.22-.269.165-.588.26-.93.26C4.775 7.333 4 6.587 4 5.667S4.776 4 5.734 4c.271 0 .528.06.756.166l.008.004c.169.07.327.182.469.324q.128.125.227.272M11 7.073c-.269.165-.588.26-.93.26-.958 0-1.735-.746-1.735-1.666S9.112 4 10.069 4c.271 0 .528.06.756.166l.008.004c.17.07.327.182.469.324q.128.125.227.272.131.188.228.401c.428.948.392 2.377-.942 3.706a.446.446 0 0 1-.613.01.405.405 0 0 1-.011-.59c.42-.416.672-.831.81-1.22z"/>
  </symbol>
  <symbol id="bi-floppy-fill" viewBox="0 0 16 16">
  <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0H3v5.5A1.5 1.5 0 0 0 4.5 7h7A1.5 1.5 0 0 0 13 5.5V0h.086a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5H14v-5.5A1.5 1.5 0 0 0 12.5 9h-9A1.5 1.5 0 0 0 2 10.5V16h-.5A1.5 1.5 0 0 1 0 14.5z"/>
  <path d="M3 16h10v-5.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5zm9-16H4v5.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5zM9 1h2v4H9z"/>
  </symbol>
  <symbol id="bi-trash-fill" viewBox="0 0 16 16">
  <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
  </symbol>
  <symbol id="bi-envelope" viewBox="0 0 16 16">
    <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
  </symbol>
  <symbol id="bi-power" viewBox="0 0 16 16">
    <path d="M7.5 1v7h1V1z"/>
    <path d="M3 8.812a5 5 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812"/>
  </symbol>
  <symbol id="bi-search" viewBox="0 0 16 16">
  	<path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
  </symbol>
</svg>

<!-- top navigation bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
	<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="offcanvasExample">
	  <span class="navbar-toggler-icon" data-bs-target="#sidebar"></span>
	</button>
	<a
	  class="navbar-brand me-auto ms-lg-0 ms-3 text-uppercase fw-bold"
	  href="#"
	  >Frontendfunn</a
	>
	<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavBar" aria-controls="topNavBar" aria-expanded="false" aria-label="Toggle navigation">
	  <span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" style="flex:none">
	  <ul class="navbar-nav">
		<li class="nav-item dropdown">
		  <a class="nav-link dropdown-toggle ms-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
		  	<?php echo $_MEMBER['mb_nick']?>
		  </a>
		  <ul class="dropdown-menu dropdown-menu-end">
			<li><a class="dropdown-item" href="<?php echo getUrl('','member','inbox', 'popup', '1') ?>" target="_blank"><svg class="bi" aria-hidden="true"><use xlink:href="#bi-envelope"/></svg> <?php echo getLang('Inbox') ?></a></li>
			<li><hr class="dropdown-divider"></li>
			<li><a class="dropdown-item" href="<?php echo getUrl('', 'module', 'member', 'act', 'signOut')?>"><svg class="bi" aria-hidden="true"><use xlink:href="#bi-power"/></svg> <?php echo getLang('logout') ?></a></li>
		  </ul>
		</li>
	  </ul>
	</div>
  </div>
</nav>
<!-- top navigation bar -->
<!-- offcanvas -->
<div class="offcanvas sidebar-nav bg-dark" tabindex="-1" id="sidebar">
  <div class="offcanvas-body p-0">
	<nav class="navbar-dark">
	  <ul class="navbar-nav">
		<li>
		  <div class="text-muted small fw-bold text-uppercase px-3">
			CORE
		  </div>
		</li>
		<li>
		  <a href="./?admin" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'default' ? ' active': '' ?>">
			<svg class="bi me-2" width="1em" height="1em"><use href="#bi-speedometer2"></use></svg>
			<span><?php echo getLang('menu_name_dashbd')?></a></span>
		  </a>
		</li>
		<li class="my-3"><hr class="navbar-divider" /></li>
		<li>
		  <div class="text-muted small fw-bold text-uppercase px-3">
			Interface
		  </div>
		</li>
		<li>
		  <a href="./?admin=theme" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'theme' ? ' active': '' ?>">
		  <svg class="bi me-2" width="1em" height="1em"><use href="#bi-house-door"></use></svg>
			<span><?php echo getLang('menu_name_theme')?></span>
		  </a>
		</li>
		<li>
		  <a href="./?admin=menu" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'menu' ? ' active': '' ?>">
		  	<svg class="bi me-2" width="1em" height="1em"><use href="#bi-menu-button"></use></svg>
			<span><?php echo getLang('menu_name_menu')?></span>
		  </a>
		</li>
		<li class="my-3"><hr class="navbar-divider" /></li>
		<li>
		  <div class="text-muted small fw-bold text-uppercase px-3">
		  Contents
		  </div>
		</li>
		<li>
			<a href="./?admin=page" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'page' ? ' active': '' ?>">
				<svg class="bi me-2" width="1em" height="1em"><use href="#bi-book"></use></svg>
				<span><?php echo getLang('menu_name_page')?></span>
			</a>
		</li>
		<li>
			<a href="./?admin=board" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'board' ? ' active': '' ?>">
				<svg class="bi me-2" width="1em" height="1em"><use href="#bi-collection"></use></svg>
				<span><?php echo getLang('menu_name_board')?></span>
			</a>
		</li>
		<?php $tmp=(in_array($_DATA['disp'], ['document','comment','file','trash']))?>
		<li>
		  <a class="nav-link px-3 pb-0 sidebar-link" data-bs-toggle="collapse" href="#layouts" aria-expanded="<?php echo $tmp?'true':'false'?>">
			<svg class="bi me-2" width="1em" height="1em"><use href="#bi-box"></use></svg>
			<span style="margin-left:4px"><?php echo getLang('menu_name_content')?></span>
			<span class="ms-auto">
			  <span class="right-icon">
			  <svg class="bi me-2" width="1em" height="1em"><use href="#bi-chevron-down"></use></svg>
			  </span>
			</span>
		  </a>
		  <div class="collapse<?php echo $tmp?' show':''?>" id="layouts">
			<ul class="navbar-nav ps-3">
			  <li>
				<a href="./?admin=document" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'document' ? ' active': '' ?>">
					<svg class="bi me-2" width="1em" height="1em"><use href="#bi-chat-right-text-fill"></use></svg>
					<span><?php echo getLang('menu_name_document')?></span>
				</a>
			  </li>
			  <li>
				<a href="./?admin=comment" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'comment' ? ' active': '' ?>">
					<svg class="bi me-2" width="1em" height="1em"><use href="#bi-chat-right-quote-fill"></use></svg>
					<span><?php echo getLang('menu_name_comment')?></span>
				</a>
			  </li>
			  <li>
				<a href="./?admin=file" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'file' ? ' active': '' ?>">
					<svg class="bi me-2" width="1em" height="1em"><use href="#bi-floppy-fill"></use></svg>
					<span><?php echo getLang('menu_name_file')?></span>
				</a>
			  </li>
			  <li>
				<a href="./?admin=trash" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'trash' ? ' active': '' ?>">
					<svg class="bi me-2" width="1em" height="1em"><use href="#bi-trash-fill"></use></svg>
					<span><?php echo getLang('menu_name_trash')?></span>
				</a>
			  </li>
			</ul>
		  </div>
		</li>
		<li class="my-3"><hr class="navbar-divider" /></li>
		<li>
		  <div class="text-muted small fw-bold text-uppercase px-3">
			Addons
		  </div>
		</li>
		<li>
		  <a href="./?admin=module" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'module' ? ' active': '' ?>">
			<svg class="bi me-2" width="1em" height="1em"><use href="#bi-grid"></use></svg>
			<span><?php echo getLang('menu_name_module')?></span>
		  </a>
		</li>
		<li>
		  <a href="./?admin=addon" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'addon' ? ' active': '' ?>">
			<svg class="bi me-2" width="1em" height="1em"><use href="#bi-plugin"></use></svg>
			<span><?php echo getLang('menu_name_addon')?></span>
		  </a>
		</li>
		<li>
		  <a href="./?admin=widget" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'widget' ? ' active': '' ?>">
			<svg class="bi me-2" width="1em" height="1em"><use href="#bi-puzzle"></use></svg>
			<span><?php echo getLang('menu_name_widget')?></span>
		  </a>
		</li>
		<li>
		  <a href="./?admin=visit" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'visit' ? ' active': '' ?>">
		  <svg class="bi me-2" width="1em" height="1em"><use href="#bi-airplane"></use></svg>
			<span><?php echo getLang('menu_name_visit')?></span>
		  </a>
		</li>
		<li class="my-3"><hr class="navbar-divider" /></li>
		<li>
		  <div class="text-muted small fw-bold text-uppercase px-3">
		  Settings
		  </div>
		</li>
		<li>
		  <a href="./?admin=member" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'member' ? ' active': '' ?>">
			<svg class="bi me-2" width="1em" height="1em"><use href="#bi-person"></use></svg>
			<span><?php echo getLang('menu_name_member')?></span>
		  </a>
		</li>
		<li>
		  <a href="./?admin=setup" class="nav-link px-3 pb-0<?php echo $_DATA['disp'] == 'setup' ? ' active': '' ?>">
		  	<svg class="bi me-2" width="1em" height="1em"><use href="#bi-gear"></use></svg>
			<span><?php echo getLang('menu_name_setup')?></span>
		  </a>
		</li>
	  </ul>
	</nav>
  </div>
</div>
<!-- offcanvas -->
<main class="my-4 p-1 pt-5">

<?php if($_DATA['disp'] != 'default') { ?>

	<div class="mx-2 mb-4">
		<h3 class="fst-italic"><?php echo getLang('menu_name_'.$admin)?></h3>
		<hr class="navbar-divider" />
		<small class="d-inline-flex w-100 px-2 py-1 fw-semibold text-secondary-emphasis bg-secondary-subtle border border-secondary-subtle rounded-1"><?php echo getLang('menu_desc_'.$admin)?></small>
	</div>

<?php } ?>
	<section class="container-fluid">
<?php
		if (!$is_admin && !in_array($admin,['default'])) {
			messageBox(getLang('error_permitted'), 4501, false);
		} else {
			if (is_array($err = get_error())) messageBox($err['message'], $err['error'], false);
			if(empty($_DATA['md_id'])){
				require_once _AF_ADMIN_PATH_ . $admin . '.php';
			}else{
				@include_once _AF_MODULES_PATH_ . $admin . '/lang/' . _AF_LANG_ . '.php';
				require_once _AF_MODULES_PATH_ . $admin . '/setup.php';
			}
		}
?>
	</section>
</main>
<?php
/* End of file admin.php */
/* Location: ./module/admin/admin.php */
