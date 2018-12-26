<template>
  <div style="height:100%;">
    <view-box ref="viewBox">
      <headerView></headerView>
      <router-view></router-view>
    </view-box>
  </div>
</template>

<script>
import headerView from '@/components/header'

import {ViewBox} from 'vux'

export default {
  name: 'app',
  components: {headerView, ViewBox}
}
</script>

<style lang="less">
@import '~vux/src/styles/reset.less';

body {
  background-color: #fbf9fe;
  font-size:14px;
}
html, body {
  height: 100%;
  width: 100%;
  overflow-x: hidden;
}
</style>