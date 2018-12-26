//通用头部
<template>
  <div id="header">
    <x-header :left-options="{showBack: false,preventGoBack:true}">管理后台</x-header>
    <tab>
      <tab-item selected @on-item-click="onItemClick">标签热词</tab-item>
      <tab-item @on-item-click="onItemClick">添加线路</tab-item>
      <tab-item @on-item-click="onItemClick">编辑线路</tab-item>
    </tab>
 </div>
</template>



<script>
import {XHeader, Tab, TabItem} from 'vux'
export default {
  name: 'headerView',
  components: {
    XHeader, Tab, TabItem
  },
  methods: {
    onItemClick: function (idx) {
      if (idx === 0) { // tag
        this.$router.push('tags')
      } else if (idx === 1) { // poi
        this.$router.push('pois_add')
      } else if (idx === 2) { // poi
        this.$router.push('pois_list')
      }
    }
  },
  data () {
    return {}
  }
}
</script>
<style scoped>
</style>